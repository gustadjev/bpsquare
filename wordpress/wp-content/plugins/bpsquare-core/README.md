# BPSquare Core Plugin

Custom WordPress plugin that provides all backend functionality for the BPSquare LLC website.

## What This Plugin Does

- Registers custom post types: Services, Case Studies, Testimonials, FAQs, Inquiries
- Exposes custom REST API endpoints under `/wp-json/bpsquare/v1/`
- Handles contact/project inquiry submissions with validation, storage, and email notifications
- Implements rate limiting and honeypot spam protection
- Provides an admin settings page for email configuration and CORS origin

## REST API Endpoints

| Method | Endpoint | Description |
|---|---|---|
| GET | `/wp-json/bpsquare/v1/services` | List all published services |
| GET | `/wp-json/bpsquare/v1/case-studies` | List all published case studies |
| GET | `/wp-json/bpsquare/v1/testimonials` | List all published testimonials |
| GET | `/wp-json/bpsquare/v1/faqs` | List all published FAQs |
| POST | `/wp-json/bpsquare/v1/inquiries` | Submit a project inquiry |

## File Structure

```
bpsquare-core/
├── bpsquare-core.php               # Plugin entry point
├── includes/
│   ├── class-plugin.php            # Main plugin singleton
│   ├── class-cpt-service.php       # Service post type + meta
│   ├── class-cpt-case-study.php    # Case Study post type + meta
│   ├── class-cpt-testimonial.php   # Testimonial post type + meta
│   ├── class-cpt-faq.php           # FAQ post type
│   ├── class-cpt-inquiry.php       # Inquiry post type + static save()
│   ├── class-rest-inquiries-controller.php  # POST /inquiries endpoint
│   ├── class-rest-content-controller.php    # GET content endpoints
│   ├── class-validation-service.php         # Input validation
│   ├── class-email-service.php             # Email notifications
│   └── class-admin-settings.php            # WP admin settings page
├── languages/
└── README.md
```

## Requirements

- WordPress 6.0+
- PHP 8.0+
