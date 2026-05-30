import { Injectable, inject } from '@angular/core';
import { DOCUMENT } from '@angular/common';
import { Title, Meta } from '@angular/platform-browser';

export interface SeoConfig {
  title: string;
  description: string;
  ogTitle?: string;
  ogDescription?: string;
  ogImage?: string;
  canonicalUrl?: string;
}

const BRAND        = 'BPSquare LLC';
const SITE_URL     = 'https://bpsquarellc.com';
const DEFAULT_IMAGE = `${SITE_URL}/assets/images/og-image.png`;

/**
 * SEO Service
 *
 * Sets the page title, meta description, Open Graph, and Twitter Card tags for
 * each route. Static fallbacks in index.html cover crawlers that skip JavaScript.
 */
@Injectable({ providedIn: 'root' })
export class SeoService {
  private readonly titleSvc  = inject(Title);
  private readonly meta      = inject(Meta);
  private readonly document  = inject(DOCUMENT);

  set(config: SeoConfig): void {
    const fullTitle   = `${config.title} | ${BRAND}`;
    const description = config.description;
    const ogTitle     = config.ogTitle       ?? fullTitle;
    const ogDesc      = config.ogDescription ?? description;
    const ogImage     = config.ogImage       ?? DEFAULT_IMAGE;
    const canonical   = config.canonicalUrl  ?? this.document.location.href;

    this.titleSvc.setTitle(fullTitle);

    // Standard
    this.meta.updateTag({ name: 'description', content: description });

    // Open Graph
    this.meta.updateTag({ property: 'og:site_name',   content: BRAND });
    this.meta.updateTag({ property: 'og:type',        content: 'website' });
    this.meta.updateTag({ property: 'og:url',         content: canonical });
    this.meta.updateTag({ property: 'og:title',       content: ogTitle });
    this.meta.updateTag({ property: 'og:description', content: ogDesc });
    this.meta.updateTag({ property: 'og:image',       content: ogImage });

    // Twitter Card
    this.meta.updateTag({ name: 'twitter:card',        content: 'summary_large_image' });
    this.meta.updateTag({ name: 'twitter:title',       content: ogTitle });
    this.meta.updateTag({ name: 'twitter:description', content: ogDesc });
    this.meta.updateTag({ name: 'twitter:image',       content: ogImage });

    this.upsertCanonical(canonical);
  }

  setDefault(): void {
    this.set({
      title: 'Custom Web Apps & WordPress Systems for Small Businesses',
      description:
        'BPSquare LLC builds custom web apps, internal tools, workflow automation, WordPress themes, and Angular business systems for service-based small businesses.',
      canonicalUrl: SITE_URL + '/',
    });
  }

  private upsertCanonical(url: string): void {
    const existing = this.document.querySelector('link[rel="canonical"]');
    if (existing) {
      existing.setAttribute('href', url);
    } else {
      const link = this.document.createElement('link');
      link.setAttribute('rel', 'canonical');
      link.setAttribute('href', url);
      this.document.head.appendChild(link);
    }
  }
}
