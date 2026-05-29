import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable, of } from 'rxjs';
import { catchError, map } from 'rxjs/operators';

import { environment } from '../../../environments/environment';
import { WpPage } from '../models/page.model';
import { Service } from '../models/service.model';
import { CaseStudy } from '../models/case-study.model';
import { Testimonial } from '../models/testimonial.model';
import { Faq } from '../models/faq.model';

/**
 * WordPress API Service
 *
 * Wraps all read-only calls to the WordPress REST API.
 * Uses the BPSquare-specific namespace for CPT endpoints.
 */
@Injectable({ providedIn: 'root' })
export class WordpressApiService {
  private readonly http = inject(HttpClient);
  private readonly wpApi = environment.wpApiUrl;

  // ---------------------------------------------------------------------------
  // Pages (standard WP REST API)
  // ---------------------------------------------------------------------------

  getPageBySlug(slug: string): Observable<WpPage | null> {
    const params = new HttpParams().set('slug', slug);
    return this.http
      .get<WpPage[]>(`${this.wpApi}/wp/v2/pages`, { params })
      .pipe(
        map((pages) => (pages.length ? pages[0] : null)),
        catchError(() => of(null))
      );
  }

  // ---------------------------------------------------------------------------
  // BPSquare custom endpoints
  // ---------------------------------------------------------------------------

  getServices(): Observable<Service[]> {
    return this.http
      .get<Service[]>(`${this.wpApi}/bpsquare/v1/services`)
      .pipe(catchError(() => of([])));
  }

  getCaseStudies(): Observable<CaseStudy[]> {
    return this.http
      .get<CaseStudy[]>(`${this.wpApi}/bpsquare/v1/case-studies`)
      .pipe(catchError(() => of([])));
  }

  getTestimonials(): Observable<Testimonial[]> {
    return this.http
      .get<Testimonial[]>(`${this.wpApi}/bpsquare/v1/testimonials`)
      .pipe(catchError(() => of([])));
  }

  getFaqs(): Observable<Faq[]> {
    return this.http
      .get<Faq[]>(`${this.wpApi}/bpsquare/v1/faqs`)
      .pipe(catchError(() => of([])));
  }
}
