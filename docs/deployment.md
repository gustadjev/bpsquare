# Deployment Guide

This project deploys as two parts:

- WordPress backend: custom theme and `bpsquare-core` plugin.
- Angular frontend: static production build served from the public web root.

## Environments

| Environment | Angular API URL | WordPress URL | Notes |
|---|---|---|---|
| Local | `http://localhost:8080/wp-json` through `proxy.conf.json` | `http://localhost:8080` | Docker Compose + Angular dev server |
| Staging | `https://staging.example.com/wp-json` | `https://staging.example.com` | Optional pre-production review |
| Production | `https://bpsquarellc.com/wp-json` | `https://bpsquarellc.com` | Public site |

Before each production build, confirm `angular/bpsquare-frontend/src/environments/environment.prod.ts` points to the production WordPress REST URL.

## WordPress Deployment

1. Back up the current WordPress files and database.
2. Upload `wordpress/wp-content/plugins/bpsquare-core/` to `wp-content/plugins/bpsquare-core/`.
3. Upload `wordpress/wp-content/themes/bpsquare-theme/` to `wp-content/themes/bpsquare-theme/`.
4. In WordPress Admin, activate the BPSquare theme and plugin if needed.
5. Review BPSquare admin settings, especially allowed CORS origins.
6. Test REST endpoints:
   - `/wp-json/bpsquare/v1/services`
   - `/wp-json/bpsquare/v1/case-studies`
   - `/wp-json/bpsquare/v1/inquiries`

## Angular Deployment

1. Install dependencies:

   ```bash
   cd angular/bpsquare-frontend
   npm ci
   ```

2. Build production assets:

   ```bash
   npm run build
   ```

3. Upload `angular/bpsquare-frontend/dist/bpsquare-frontend/browser/` to the domain document root.
4. Confirm these files are present in the web root:
   - `index.html`
   - `robots.txt`
   - `sitemap.xml`
   - `assets/images/og-image.png`
5. Clear hosting/CDN cache.
6. Visit the main routes:
   - `/`
   - `/about`
   - `/services`
   - `/process`
   - `/portfolio`
   - `/contact`
   - `/privacy`

## Rollback

1. Restore the previous WordPress plugin/theme folders from backup.
2. Restore the previous Angular build directory from backup.
3. Clear all caches.
4. Confirm contact form and REST endpoints still work.

## Release Checklist

- Angular build passes.
- Angular tests pass.
- PHP syntax check passes.
- Contact form submits to WordPress.
- Inquiry appears in WordPress Admin with the correct status and scoping fields.
- Admin notification email includes business type, URL, operational problem, and phased approach.
- `robots.txt` and `sitemap.xml` are reachable.
- Open Graph image loads at `https://bpsquarellc.com/assets/images/og-image.png`.
- Mobile navigation, keyboard focus, and form errors are usable.
