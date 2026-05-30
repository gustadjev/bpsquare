import { provideHttpClientTesting } from '@angular/common/http/testing';
import { provideHttpClient } from '@angular/common/http';
import { ComponentFixture, TestBed } from '@angular/core/testing';
import { NoopAnimationsModule } from '@angular/platform-browser/animations';

import { ContactComponent } from './contact.component';

describe('ContactComponent', () => {
  let fixture: ComponentFixture<ContactComponent>;
  let component: ContactComponent;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ContactComponent, NoopAnimationsModule],
      providers: [provideHttpClient(), provideHttpClientTesting()],
    }).compileComponents();

    fixture = TestBed.createComponent(ContactComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('requires key project inquiry fields', () => {
    component.onSubmit();

    expect(component.form.invalid).toBeTrue();
    expect(component.fieldError('firstName')).toBe('This field is required.');
    expect(component.fieldError('email')).toBe('This field is required.');
    expect(component.fieldError('serviceInterest')).toBe('This field is required.');
    expect(component.fieldError('consentAccepted')).toBe('You must accept the terms to continue.');
  });

  it('validates email and optional current URL', () => {
    component.form.patchValue({
      email: 'not-an-email',
      currentWebsite: 'example.com',
    });
    component.form.get('email')?.markAsTouched();
    component.form.get('currentWebsite')?.markAsTouched();

    expect(component.fieldError('email')).toBe('Please enter a valid email address.');
    expect(component.fieldError('currentWebsite')).toBe('Please enter a valid URL starting with http:// or https://.');
  });

  it('keeps new scoping fields in the payload model', () => {
    component.form.patchValue({
      firstName: 'Jane',
      lastName: 'Smith',
      businessName: 'Smith Services',
      businessType: 'Service-Based Small Business',
      currentWebsite: 'https://example.com',
      email: 'jane@example.com',
      serviceInterest: 'Internal Tool or Workflow Automation',
      budgetRange: '$3,000 - $7,500',
      preferredTimeline: '1-3 Months',
      phasedApproach: 'Maybe, I would like guidance',
      operationalProblem: 'Requests are tracked across spreadsheets and email.',
      projectDescription: 'We need a better internal intake and follow-up workflow.',
      consentAccepted: true,
    });

    expect(component.form.valid).toBeTrue();
    expect(component.form.value.businessType).toBe('Service-Based Small Business');
    expect(component.form.value.currentWebsite).toBe('https://example.com');
    expect(component.form.value.phasedApproach).toContain('guidance');
    expect(component.form.value.operationalProblem).toContain('spreadsheets');
  });
});
