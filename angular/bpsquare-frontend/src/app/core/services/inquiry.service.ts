import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

import { environment } from '../../../environments/environment';
import { InquiryForm, InquiryResponse } from '../models/inquiry.model';

/**
 * Inquiry Service
 *
 * Submits project/contact inquiry forms to the WordPress REST API.
 */
@Injectable({ providedIn: 'root' })
export class InquiryService {
  private readonly http = inject(HttpClient);
  private readonly endpoint = `${environment.wpApiUrl}/bpsquare/v1/inquiries`;

  submit(payload: InquiryForm): Observable<InquiryResponse> {
    return this.http.post<InquiryResponse>(this.endpoint, payload);
  }
}
