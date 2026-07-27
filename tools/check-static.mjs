// Resolve every root-relative reference in the exported site against dist/
// and report anything missing.
import { readFile, access } from 'node:fs/promises';
import { join } from 'node:path';
import { glob } from 'node:fs/promises';

const OUT = process.argv[2];
const missing = new Map();
let checked = 0;

const files = [];
for await (const f of glob('**/*.{html,css,js,svg}', { cwd: OUT })) files.push(f);

for (const rel of files) {
  const text = await readFile(join(OUT, rel), 'utf8');
  const refs = new Set();

  for (const m of text.matchAll(/(?:src|href)\s*=\s*["']([^"']+)["']/gi)) refs.add(m[1]);
  for (const m of text.matchAll(/url\(\s*['"]?([^'")]+?)['"]?\s*\)/gi)) refs.add(m[1]);
  for (const m of text.matchAll(/srcset\s*=\s*["']([^"']+)["']/gi)) {
    for (const part of m[1].split(',')) refs.add(part.trim().split(/\s+/)[0]);
  }

  for (const raw of refs) {
    const r = raw.trim();
    if (!r.startsWith('/')) continue;           // external, data:, anchors, relative
    if (r.startsWith('//')) continue;           // protocol-relative external
    const path = decodeURIComponent(r.split('?')[0].split('#')[0]);
    checked++;
    try {
      await access(join(OUT, path));
    } catch {
      if (!missing.has(path)) missing.set(path, new Set());
      missing.get(path).add(rel);
    }
  }
}

console.log(`scanned files:   ${files.length}`);
console.log(`refs checked:    ${checked}`);
console.log(`missing targets: ${missing.size}`);
for (const [path, from] of missing) {
  console.log(`  MISSING ${path}\n          referenced by: ${[...from].join(', ')}`);
}
