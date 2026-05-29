export interface InquiryForm {
  firstName: string;
  lastName: string;
  businessName: string;
  email: string;
  phone: string;
  serviceInterest: string;
  budgetRange: string;
  preferredTimeline: string;
  projectDescription: string;
  consentAccepted: boolean;
  website?: string; // honeypot — must remain empty
}

export interface InquiryResponse {
  success: boolean;
  message: string;
  inquiryId?: number;
}
