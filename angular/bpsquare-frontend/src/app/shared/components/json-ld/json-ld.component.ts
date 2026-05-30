import { Component, OnInit, PLATFORM_ID, inject } from '@angular/core';
import { isPlatformBrowser } from '@angular/common';

/**
 * Injects a JSON-LD LocalBusiness schema script tag into the document head.
 * Used once in AppComponent for site-wide structured data.
 */
@Component({
  selector: 'bps-json-ld',
  standalone: true,
  template: '',
})
export class JsonLdComponent implements OnInit {
  private readonly platformId = inject(PLATFORM_ID);

  ngOnInit(): void {
    if (!isPlatformBrowser(this.platformId)) return;

    const schema = {
      '@context': 'https://schema.org',
      '@type': 'ProfessionalService',
      name: 'BPSquare LLC',
      url: 'https://bpsquarellc.com',
      description:
        'Founder-led full-stack studio helping service-based small businesses build custom web apps, internal tools, workflow automation, WordPress systems, and Angular integrations.',
      slogan: 'Custom Web Apps and WordPress Systems for Service-Based Small Businesses',
      email: 'info@bpsquarellc.com',
      areaServed: 'US',
      knowsAbout: [
        'Custom Web Apps for Service Businesses',
        'Internal Tools',
        'Workflow Automation',
        'Small Business Operations Modernization',
        'WordPress Custom Theme Development',
        'WordPress and Angular Integration',
        'Software Engineering',
      ],
      sameAs: ['https://bpsquarellc.com'],
    };

    const existing = document.querySelector('script[type="application/ld+json"]');
    if (existing) return;

    const script = document.createElement('script');
    script.type = 'application/ld+json';
    script.text = JSON.stringify(schema);
    document.head.appendChild(script);
  }
}
