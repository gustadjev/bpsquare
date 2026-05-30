import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';
import { MatIconModule } from '@angular/material/icon';

@Component({
  selector: 'bps-footer',
  standalone: true,
  imports: [RouterLink, MatIconModule],
  templateUrl: './footer.component.html',
  styleUrl: './footer.component.scss',
})
export class FooterComponent {
  readonly currentYear = new Date().getFullYear();

  readonly services = [
    { label: 'Custom Web Apps', path: '/services' },
    { label: 'Internal Tools', path: '/services' },
    { label: 'Workflow Automation', path: '/services' },
    { label: 'WordPress + Angular Integration', path: '/services' },
    { label: 'Custom WordPress Themes', path: '/services' },
  ];

  readonly quickLinks = [
    { label: 'About', path: '/about' },
    { label: 'Services', path: '/services' },
    { label: 'Our Process', path: '/process' },
    { label: 'Project Lab', path: '/portfolio' },
    { label: 'Contact', path: '/contact' },
    { label: 'Privacy Policy', path: '/privacy' },
  ];
}
