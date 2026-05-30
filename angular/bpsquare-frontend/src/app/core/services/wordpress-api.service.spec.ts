import { TestBed } from '@angular/core/testing';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { provideHttpClient } from '@angular/common/http';

import { environment } from '../../../environments/environment';
import { WordpressApiService } from './wordpress-api.service';

describe('WordpressApiService', () => {
  let service: WordpressApiService;
  let http: HttpTestingController;

  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [WordpressApiService, provideHttpClient(), provideHttpClientTesting()],
    });

    service = TestBed.inject(WordpressApiService);
    http = TestBed.inject(HttpTestingController);
  });

  afterEach(() => {
    http.verify();
  });

  it('falls back to local services when the WordPress API is unavailable', (done) => {
    service.getServices().subscribe((services) => {
      expect(services.length).toBeGreaterThan(0);
      expect(services[0].title).toContain('Custom Web Apps');
      done();
    });

    const req = http.expectOne(`${environment.wpApiUrl}/bpsquare/v1/services`);
    req.flush('Server error', { status: 500, statusText: 'Server Error' });
  });

  it('falls back to project lab content when the WordPress API returns no case studies', (done) => {
    service.getCaseStudies().subscribe((caseStudies) => {
      expect(caseStudies.length).toBeGreaterThan(0);
      expect(caseStudies[0].status).toBeDefined();
      done();
    });

    const req = http.expectOne(`${environment.wpApiUrl}/bpsquare/v1/case-studies`);
    req.flush([]);
  });
});
