# Static export pipeline

Vercel cannot run PHP, so the public site is a static snapshot of the local
WordPress render rather than WordPress itself.

## Re-exporting after a content change

1. Start XAMPP (Apache + MySQL) and confirm <http://localhost/Wordpress-elementor/>
   returns the page. `wp-config.php` must point at the **local** database
   (`wordpress_elementor` on `127.0.0.1`) — a remote DB host will 500.
2. Regenerate `dist/`:

   ```sh
   node tools/export-static.mjs dist
   ```

3. Verify nothing dangles:

   ```sh
   node tools/check-static.mjs dist
   ```

   `missing targets: 0` means every referenced asset was captured.

4. Commit `dist/` and push. `vercel.json` sets `outputDirectory` to `dist`, so
   Vercel publishes only that folder.

## What the exporter does

- Crawls the front page and follows every same-origin asset reference
  (including `url(...)` inside stylesheets and `srcset` candidates).
- Rewrites `http://localhost/Wordpress-elementor/...` to root-relative `/...`,
  covering the JSON-escaped form Elementor emits in `data-settings`.
- Strips head tags that only work on a live install: oEmbed discovery, RSS
  feeds, `wp-json`, `xmlrpc.php`, shortlink, and generator meta.

Only the front page (id 65) is exported. The default `hello-world` post and
`sample-page` are deliberately excluded.

## Limitations

- No `wp-admin` on the deployed site — edit locally, re-export, push.
- Any contact form is inert. Point it at a third-party endpoint
  (Formspree, Web3Forms) if you need submissions.
