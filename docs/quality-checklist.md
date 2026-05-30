# Quality Checklist

Use this before a production release or after meaningful UI/backend changes.

## Accessibility

- Navigate every route with keyboard only.
- Confirm skip link appears on focus and moves to main content.
- Confirm visible focus styles on navigation, buttons, form fields, and accordions.
- Confirm form labels and errors are announced by assistive technology.
- Confirm heading order is logical on each route.
- Confirm color contrast is readable in hero, cards, form states, and footer.

## Performance

- Run `npm run build` from `angular/bpsquare-frontend`.
- Review Angular bundle budget warnings.
- Compress or replace oversized images.
- Confirm lazy routes still split into separate chunks.
- Run Lighthouse on home, services, project lab, and contact pages.
- Confirm Google Fonts load and do not block page rendering longer than expected.

## SEO

- Confirm each route has a meaningful title and meta description.
- Confirm canonical URLs point to `https://bpsquarellc.com`.
- Confirm Open Graph and Twitter Card metadata use `assets/images/og-image.png`.
- Confirm `/robots.txt` references `/sitemap.xml`.
- Confirm `/sitemap.xml` includes all public routes.

## Functional

- Confirm service and project lab pages render with WordPress API online.
- Stop WordPress locally and confirm fallback service/project lab content still renders.
- Submit the contact form with valid data.
- Submit the contact form with invalid email, invalid URL, missing consent, and honeypot filled.
- Confirm WordPress stores new inquiry fields and status.
- Confirm admin and acknowledgement emails contain expected information.
