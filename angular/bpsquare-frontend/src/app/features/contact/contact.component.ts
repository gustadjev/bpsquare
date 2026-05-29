import { Component, OnInit, inject, signal } from '@angular/core';
import {
  FormBuilder,
  FormGroup,
  ReactiveFormsModule,
  Validators,
} from '@angular/forms';
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

  readonly services = [
    'Website Development',
    'Custom Web Application Development',
    'UI/UX Design & Prototyping',
    'Business Process Automation',
    'Technical Consulting',
    'Maintenance & Support',
    'Not Sure / General Inquiry',
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

  form: FormGroup = this.fb.group({
    firstName:          ['', [Validators.required, Validators.minLength(2)]],
    lastName:           ['', [Validators.required, Validators.minLength(2)]],
    businessName:       ['', Validators.required],
    email:              ['', [Validators.required, Validators.email]],
    phone:              ['', []],
    serviceInterest:    ['', Validators.required],
    budgetRange:        ['', Validators.required],
    preferredTimeline:  ['', Validators.required],
    projectDescription: ['', [Validators.required, Validators.minLength(20)]],
    consentAccepted:    [false, Validators.requiredTrue],
    website:            [''], // honeypot — must remain empty
  });

  ngOnInit(): void {
    this.seo.set({
      title: 'Contact Us',
      description: 'Ready to start your project? Submit a project inquiry to BPSquare LLC and we will get back to you within one business day.',
      canonicalUrl: 'https://bpsquarellc.com/contact',
    });
  }

  get f() { return this.form.controls; }

  fieldError(field: string): string | null {
    const ctrl = this.form.get(field);
    if (!ctrl || !ctrl.invalid || !ctrl.touched) return null;
    if (ctrl.errors?.['required']) return 'This field is required.';
    if (ctrl.errors?.['email']) return 'Please enter a valid email address.';
    if (ctrl.errors?.['minlength']) {
      const min = ctrl.errors['minlength'].requiredLength as number;
      return `Please enter at least ${min} characters.`;
    }
    if (ctrl.errors?.['requiredTrue']) return 'You must accept the terms to continue.';
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

    this.inquiry.submit(this.form.value).subscribe({
      next: () => {
        this.status.set('success');
        this.form.reset();
      },
      error: () => {
        this.status.set('error');
      },
    });
  }

  retry(): void {
    this.status.set('idle');
  }
}
