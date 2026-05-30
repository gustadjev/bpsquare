import { Component, OnInit, inject, signal } from '@angular/core';
import {
  FormBuilder,
  FormGroup,
  ReactiveFormsModule,
  Validators,
} from '@angular/forms';
import { HttpErrorResponse } from '@angular/common/http';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { MatIconModule } from '@angular/material/icon';
import { MatFormFieldModule } from '@angular/material/form-field';

import { InquiryService } from '../../core/services/inquiry.service';
import { SeoService } from '../../core/services/seo.service';

export type FormStatus = 'idle' | 'submitting' | 'success' | 'error';

@Component({
  selector: 'bps-contact',
  standalone: true,
  imports: [
    ReactiveFormsModule,
    MatInputModule,
    MatSelectModule,
    MatCheckboxModule,
    MatIconModule,
    MatFormFieldModule,
  ],
  templateUrl: './contact.component.html',
  styleUrl: './contact.component.scss',
})
export class ContactComponent implements OnInit {
  private readonly fb      = inject(FormBuilder);
  private readonly inquiry = inject(InquiryService);
  private readonly seo     = inject(SeoService);

  status = signal<FormStatus>('idle');
  errorMessage = signal<string | null>(null);

  readonly services = [
    'Custom Web App for a Service Business',
    'Internal Tool or Workflow Automation',
    'Small Business Operations Modernization',
    'WordPress + Angular / Business System Integration',
    'Custom WordPress Theme Development',
    'Project Scoping or Technical Consulting',
    'Not Sure / General Inquiry',
  ];

  readonly businessTypes = [
    'Service-Based Small Business',
    'Minority-Owned Business',
    'Nonprofit / Community Organization',
    'Solo Consultant / Professional Service',
    'Healthcare / Wellness Service',
    'Finance / Administrative Service',
    'Other',
  ];

  readonly budgetRanges = [
    'Under $1,000',
    '$1,000 – $3,000',
    '$3,000 – $7,500',
    '$7,500 – $15,000',
    '$15,000 – $30,000',
    '$30,000+',
    'Not Sure Yet',
  ];

  readonly timelines = [
    'As Soon as Possible',
    'Within 1 Month',
    '1–3 Months',
    '3–6 Months',
    '6+ Months',
    'Flexible / Not Sure',
  ];

  readonly phasedOptions = [
    'Yes, I prefer a phased approach',
    'Maybe, I would like guidance',
    'No, I need a complete launch',
    'Not sure yet',
  ];

  form: FormGroup = this.fb.group({
    firstName:          ['', [Validators.required, Validators.minLength(2)]],
    lastName:           ['', [Validators.required, Validators.minLength(2)]],
    businessName:       ['', Validators.required],
    businessType:       [''],
    currentWebsite:     ['', [Validators.pattern(/^https?:\/\/.+\..+/i)]],
    email:              ['', [Validators.required, Validators.email]],
    phone:              ['', []],
    serviceInterest:    ['', Validators.required],
    budgetRange:        ['', Validators.required],
    preferredTimeline:  ['', Validators.required],
    phasedApproach:     [''],
    operationalProblem: [''],
    projectDescription: ['', [Validators.required, Validators.minLength(20)]],
    consentAccepted:    [false, Validators.requiredTrue],
    website:            [''], // honeypot — must remain empty
  });

  ngOnInit(): void {
    this.seo.set({
      title: 'Contact Us',
      description: 'Start a project conversation with BPSquare LLC about a custom web app, internal tool, workflow automation, WordPress system, or Angular integration.',
      canonicalUrl: 'https://bpsquarellc.com/contact',
    });
  }

  get f() { return this.form.controls; }

  fieldError(field: string): string | null {
    const ctrl = this.form.get(field);
    if (!ctrl || !ctrl.invalid || !ctrl.touched) return null;
    if (ctrl.errors?.['requiredTrue']) return 'You must accept the terms to continue.';
    if (field === 'consentAccepted' && ctrl.errors?.['required']) return 'You must accept the terms to continue.';
    if (ctrl.errors?.['required']) return 'This field is required.';
    if (ctrl.errors?.['email']) return 'Please enter a valid email address.';
    if (ctrl.errors?.['pattern']) return 'Please enter a valid URL starting with http:// or https://.';
    if (ctrl.errors?.['minlength']) {
      const min = ctrl.errors['minlength'].requiredLength as number;
      return `Please enter at least ${min} characters.`;
    }
    return null;
  }

  onSubmit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    // Honeypot check (client-side guard)
    if (this.form.value.website) return;

    this.status.set('submitting');
    this.errorMessage.set(null);

    this.inquiry.submit(this.form.value).subscribe({
      next: () => {
        this.status.set('success');
        this.form.reset();
      },
      error: (error: HttpErrorResponse) => {
        this.errorMessage.set(this.toFriendlyError(error));
        this.status.set('error');
      },
    });
  }

  retry(): void {
    this.errorMessage.set(null);
    this.status.set('idle');
  }

  private toFriendlyError(error: HttpErrorResponse): string {
    if (error.status === 429) {
      return 'Too many submissions were sent recently. Please wait a bit or email BPSquare directly.';
    }
    if (error.status === 400) {
      const validation = error.error?.data?.errors ?? error.error?.errors;
      const firstError = validation
        ? Object.values(validation).flat().find(Boolean)
        : null;
      return typeof firstError === 'string'
        ? firstError
        : 'Some form details need attention. Please review the fields and try again.';
    }
    if (error.status === 0) {
      return 'The form could not reach the server. Please check your connection or email BPSquare directly.';
    }
    return error.error?.message || 'We were unable to submit your inquiry at this time.';
  }
}
