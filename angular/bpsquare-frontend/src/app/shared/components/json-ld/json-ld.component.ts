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
        'IT consulting and software development company helping small businesses, minority-owned businesses, and growing organizations build reliable digital solutions.',
      slogan: 'Practical Technology Solutions for Growing Businesses',
      email: 'info@bpsquarellc.com',
      areaServed: 'US',
      knowsAbout: [
        'Website Development',
        'Custom Web Application Development',
        'UI/UX Design',
        'Business Process Automation',
        'Technical Consulting',
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
