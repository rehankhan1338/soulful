// Static exporter for the Soulful Beginnings Academy Elementor site.
// Crawls the local WordPress render, downloads every same-origin asset,
// and rewrites localhost URLs to root-relative so the output works on Vercel.

import { mkdir, writeFile } from 'node:fs/promises';
import { dirname, join } from 'node:path';

const ORIGIN = 'http://localhost';
const BASE = '/Wordpress-elementor/';
const ROOT = ORIGIN + BASE;
const OUT = process.argv[2];

const SEEDS = [ROOT];
const SKIP = /\/(wp-admin|wp-login\.php|wp-json|xmlrpc\.php|wp-cron\.php|feed|comments)(\/|$|\?)/i;

const TEXT_EXT = /\.(css|js|html|svg)$/i;
// Only follow references that are unambiguously files. The site is a single
// page, so anything extensionless is a stray match (a directory, a JS
// fragment) and must not be fetched.
const ASSET_EXT = /\.(css|js|mjs|json|png|jpe?g|gif|svg|webp|avif|ico|bmp|woff2?|ttf|otf|eot|mp4|webm|ogg|m4v|pdf)$/i;
const seen = new Map(); // localPath -> true
const queued = new Set();
const failures = [];
let pages = 0, assets = 0;

/** Absolute URL -> path inside the output dir, or null if it isn't ours. */
function toLocal(absUrl) {
  let u;
  try { u = new URL(absUrl); } catch { return null; }
  if (u.host !== 'localhost') return null;
  if (!u.pathname.startsWith(BASE)) return null;
  if (SKIP.test(u.pathname)) return null;
  let p = u.pathname.slice(BASE.length);
  if (p === '' || p.endsWith('/')) p += 'index.html';
  return decodeURIComponent(p);
}

/** Pull every candidate same-origin URL out of a blob of text. */
function extractUrls(text, baseUrl) {
  const out = new Set();
  // JSON/JS-escaped forms that Elementor emits in data-settings attributes
  const unescaped = text.replace(/\\\//g, '/').replace(/\\u002[fF]/g, '/');
  for (const src of [text, unescaped]) {
    for (const m of src.matchAll(/https?:\/\/localhost\/Wordpress-elementor\/[^"'`)\s<>\\,]+/gi)) {
      out.add(m[0]);
    }
    // root-relative references (common inside CSS and srcset)
    for (const m of src.matchAll(/(?<=["'(\s,=])\/Wordpress-elementor\/[^"'`)\s<>\\,]+/gi)) {
      out.add(ORIGIN + m[0]);
    }
  }
  // CSS url(...) relative to the stylesheet's own location
  for (const m of text.matchAll(/url\(\s*['"]?([^'")]+?)['"]?\s*\)/gi)) {
    const raw = m[1].trim();
    if (!raw || raw.startsWith('data:') || raw.startsWith('#') || /^https?:/i.test(raw)) continue;
    if (raw.startsWith('/Wordpress-elementor/')) continue; // already captured
    try { out.add(new URL(raw, baseUrl).href); } catch { /* ignore */ }
  }
  return [...out].map(u => u.split('#')[0]);
}

/** Rewrite our absolute/base-prefixed URLs to root-relative. */
function rewrite(text) {
  return text
    .replace(/https?:\/\/localhost\/Wordpress-elementor\//gi, '/')
    .replace(/https?:\\\/\\\/localhost\\\/Wordpress-elementor\\\//gi, '\\/')
    .replace(/(?<=["'(\s,=])\/Wordpress-elementor\//gi, '/');
}

// Strip head tags that only make sense on a live WordPress install.
// Left in place they point at /wp-json and /xmlrpc.php, which 404 here.
const CRUFT = [
  /[ \t]*<link[^>]+type=["']application\/json\+oembed["'][^>]*>\s*/gi,
  /[ \t]*<link[^>]+type=["']text\/xml\+oembed["'][^>]*>\s*/gi,
  /[ \t]*<link[^>]+rel=["']https:\/\/api\.w\.org\/["'][^>]*>\s*/gi,
  /[ \t]*<link[^>]+href=["'][^"']*\/wp-json\/[^"']*["'][^>]*>\s*/gi,
  /[ \t]*<link[^>]+rel=["']EditURI["'][^>]*>\s*/gi,
  /[ \t]*<link[^>]+rel=["']shortlink["'][^>]*>\s*/gi,
  /[ \t]*<meta[^>]+name=["']generator["'][^>]*>\s*/gi,
  /[ \t]*<link[^>]+type=["']application\/rss\+xml["'][^>]*>\s*/gi,
];

function cleanHtml(html) {
  return CRUFT.reduce((acc, re) => acc.replace(re, ''), html);
}

async function save(localPath, buf) {
  const full = join(OUT, localPath);
  await mkdir(dirname(full), { recursive: true });
  await writeFile(full, buf);
}

async function fetchOne(absUrl, isPage) {
  const localPath = toLocal(absUrl);
  if (!localPath || seen.has(localPath)) return;
  seen.set(localPath, true);

  let res;
  try {
    res = await fetch(absUrl, { redirect: 'follow' });
  } catch (e) {
    failures.push(`${absUrl} -> ${e.message}`);
    return;
  }
  if (!res.ok) {
    failures.push(`${absUrl} -> HTTP ${res.status}`);
    return;
  }

  const ctype = (res.headers.get('content-type') || '').toLowerCase();
  const isText = isPage || TEXT_EXT.test(localPath.split('?')[0]) ||
    /text\/|javascript|json|xml|\+xml/.test(ctype);

  if (!isText) {
    await save(localPath, Buffer.from(await res.arrayBuffer()));
    assets++;
    return;
  }

  const body = await res.text();
  for (const found of extractUrls(body, absUrl)) {
    const lp = toLocal(found);
    if (!lp || !ASSET_EXT.test(lp)) continue;
    if (seen.has(lp) || queued.has(found)) continue;
    queued.add(found);
    await fetchOne(found, false);
  }
  const outBody = isPage ? cleanHtml(rewrite(body)) : rewrite(body);
  await save(localPath, outBody);
  if (isPage) pages++; else assets++;
}

await mkdir(OUT, { recursive: true });
for (const s of SEEDS) await fetchOne(s, true);

console.log(`pages:  ${pages}`);
console.log(`assets: ${assets}`);
console.log(`failed: ${failures.length}`);
for (const f of failures.slice(0, 40)) console.log('  ! ' + f);
