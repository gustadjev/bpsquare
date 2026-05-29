# BPSquare LLC Website

**Domain:** bpsquarellc.com  
**Stack:** Headless WordPress (PHP/MySQL) + Angular 21  
**Status:** Phase 1 MVP

---

## Architecture Overview

This project uses **Headless WordPress + Angular** (Option 1):

| Layer | Technology | Role |
|---|---|---|
| CMS / Backend | WordPress + PHP + MySQL | Content management, REST API, admin dashboard |
| Custom Plugin | `bpsquare-core` | CPTs, REST endpoints, inquiry handling, email |
| Custom Theme | `bpsquare-theme` | Minimal headless theme; CORS support |
| Frontend | Angular 21 (standalone) | All public-facing UI, consumes WP REST API |
| Styling | Angular Material + SCSS | Design system, responsive layout |
| Local Dev | Docker Compose | WordPress + MySQL + Angular dev server |

### Why Headless WordPress + Angular?
- Full control over the frontend UX with modern Angular 21 standalone components
- WordPress admin remains familiar for non-technical content editors
- REST API decouples frontend from backend; each can evolve independently
- Angular lazy-loaded routes, Angular Material, and SCSS give a professional, performant result
- SEO handled via Angular's `Title`/`Meta` services + optional SSR

---

## Repository Structure

```
bp-square-llc/
├── .env.example                    # Environment variable template
├── docker-compose.yml              # Local development environment
├── README.md                       # This file
│
├── wordpress/
│   └── wp-content/
│       ├── themes/
│       │   └── bpsquare-theme/     # Minimal headless WordPress theme
│       └── plugins/
│           └── bpsquare-core/      # BPSquare custom plugin
│               ├── bpsquare-core.php
│               └── includes/
│                   ├── class-plugin.php
│                   ├── class-cpt-service.php
│                   ├── class-cpt-case-study.php
│                   ├── class-cpt-testimonial.php
│                   ├── class-cpt-faq.php
│                   ├── class-rest-inquiries-controller.php
│                   ├── class-rest-content-controller.php
│                   ├── class-email-service.php
│                   ├── class-validation-service.php
│                   └── class-admin-settings.php
│
└── angular/
    └── bpsquare-frontend/          # Angular 21 standalone app
        └── src/app/
            ├── core/               # Services, models, interceptors
            ├── shared/             # Reusable components
            └── features/           # Page-level feature components
```

---

## Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (for local dev)
- [Node.js 20+](https://nodejs.org/) + npm
- [Angular CLI 21](https://angular.io/cli): `npm install -g @angular/cli@latest`

---

## Local Development Setup

### 1. Clone and configure environment

```bash
git clone <repo-url> bp-square-llc
cd bp-square-llc
cp .env.example .env
# Edit .env with your preferred passwords/settings
```

### 2. Start WordPress + MySQL (Docker)

```bash
docker-compose up -d
```

WordPress will be available at **http://localhost:8080**  
wp-admin will be at **http://localhost:8080/wp-admin**

### 3. Complete WordPress installation

1. Open http://localhost:8080 in your browser
2. Complete the WordPress 5-minute install wizard
3. Activate the **BPSquare Theme** (Appearance → Themes)
4. Activate the **BPSquare Core** plugin (Plugins → Installed Plugins)
5. Run the seed data importer (BPSquare → Seed Data in WP admin) to populate sample content

### 4. Start the Angular frontend

```bash
cd angular/bpsquare-frontend
npm install
npm start
# or: ng serve
```

Angular dev server: **http://localhost:4200**

The Angular app will proxy WordPress REST API calls through the dev server. See `proxy.conf.json`.

### 5. WordPress REST API

Test the API is working:

```bash
curl http://localhost:8080/wp-json/wp/v2/posts
curl http://localhost:8080/wp-json/bpsquare/v1/services
```

---

## WordPress Content Management

### Custom Post Types (managed via WP Admin)

| Post Type | Slug | Purpose |
|---|---|---|
| Service | `bps_service` | Service offerings |
| Case Study | `bps_case_study` | Portfolio / case studies |
| Testimonial | `bps_testimonial` | Client testimonials |
| FAQ | `bps_faq` | Frequently asked questions |
| Inquiry | `bps_inquiry` | Contact/project inquiry submissions |

### REST API Endpoints

| Method | Endpoint | Description |
|---|---|---|
| GET | `/wp-json/bpsquare/v1/services` | List all services |
| GET | `/wp-json/bpsquare/v1/case-studies` | List all case studies |
| GET | `/wp-json/bpsquare/v1/testimonials` | List all testimonials |
| GET | `/wp-json/bpsquare/v1/faqs` | List all FAQs |
| POST | `/wp-json/bpsquare/v1/inquiries` | Submit project inquiry |

### Contact Form Submission (POST /wp-json/bpsquare/v1/inquiries)

**Request body (JSON):**
```json
{
  "firstName": "Jane",
  "lastName": "Smith",
  "businessName": "Smith Services LLC",
  "email": "jane@example.com",
  "phone": "555-123-4567",
  "serviceInterest": "Website Development",
  "budgetRange": "$2,500 – $5,000",
  "preferredTimeline": "2–3 months",
  "projectDescription": "We need a new website for our consulting business.",
  "consentAccepted": true
}
```

**Responses:**
- `201 Created` — inquiry saved, email sent
- `400 Bad Request` — validation errors
- `429 Too Many Requests` — rate limit exceeded
- `500 Internal Server Error` — unexpected failure

---

## Angular Pages

| Route | Component | Content Source |
|---|---|---|
| `/` | HomeComponent | WordPress pages API + CPTs |
| `/about` | AboutComponent | WordPress pages API |
| `/services` | ServicesComponent | `/wp-json/bpsquare/v1/services` |
| `/process` | ProcessComponent | Static + WordPress pages API |
| `/portfolio` | PortfolioComponent | `/wp-json/bpsquare/v1/case-studies` |
| `/contact` | ContactComponent | Reactive form → POST to inquiries |
| `/privacy` | PrivacyComponent | WordPress pages API |

---

## Deployment

### Shared Hosting / cPanel

1. Upload `wordpress/wp-content/` to your hosting's `wp-content/` directory
2. Run `ng build --configuration production` in `angular/bpsquare-frontend/`
3. Upload `dist/bpsquare-frontend/browser/` to your domain's document root
4. Set `ANGULAR_WP_API_URL` to `https://bpsquarellc.com/wp-json` in environment files before building
5. Configure `wp-config.php` with production database credentials (do not use `.env` on shared hosting — use `wp-config.php` constants)

### VPS / Cloud (Ubuntu + Nginx)

See `docs/deployment-vps.md` (coming soon) for Nginx config samples, SSL via Let's Encrypt, and PM2 or static file serving for Angular.

### Managed WordPress Hosting (WP Engine, Kinsta, Flywheel)

- Upload only the `wordpress/wp-content/` directory
- Deploy Angular separately (Netlify, Vercel, or a subdomain)
- Set CORS origin in `bpsquare-core` admin settings to point to your Angular domain

### Environment Variables for Production

Do **not** use `.env` files in production WordPress. Instead, define constants in `wp-config.php`:

```php
define('BPSQUARE_ADMIN_EMAIL', 'admin@bpsquarellc.com');
define('BPSQUARE_FROM_EMAIL', 'noreply@bpsquarellc.com');
```

For Angular, update `src/environments/environment.prod.ts` before building:

```typescript
export const environment = {
  production: true,
  wpApiUrl: 'https://bpsquarellc.com/wp-json',
  siteUrl: 'https://bpsquarellc.com',
};
```

---

## Security Notes

- All PHP input is sanitized with `sanitize_text_field()`, `sanitize_email()`, etc.
- All output is escaped with `esc_html()`, `esc_attr()`, `esc_url()`
- Contact form has honeypot field + server-side rate limiting (5 submissions / IP / hour)
- WordPress nonce validation on admin-only endpoints
- CORS restricted to known origins only
- No sensitive data in error responses
- Admin-only REST endpoints require `manage_options` capability
- Use HTTPS in production (free SSL via Let's Encrypt or hosting provider)

---

## SEO

- Angular `Title` and `Meta` services set unique titles/descriptions per route
- Open Graph tags on all pages
- Semantic HTML5 structure
- `robots.txt` managed by WordPress (Yoast SEO plugin compatible)
- `sitemap.xml` auto-generated by WordPress
- Schema.org `LocalBusiness` / `ProfessionalService` JSON-LD in Angular app

---

## Future Roadmap (Post-Phase 1)

- [ ] Blog / news section
- [ ] Client portal (authenticated)
- [ ] Appointment booking
- [ ] Quote estimator
- [ ] Newsletter integration
- [ ] Angular Universal SSR for full SEO + performance benefits
- [ ] Storybook for component documentation

---

## License

© 2025 BPSquare LLC. All rights reserved.
