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
    { label: 'Website Development', path: '/services' },
    { label: 'Web Application Development', path: '/services' },
    { label: 'UI/UX Design & Prototyping', path: '/services' },
    { label: 'Business Process Automation', path: '/services' },
    { label: 'Technical Consulting', path: '/services' },
    { label: 'Maintenance & Support', path: '/services' },
  ];

  readonly quickLinks = [
    { label: 'About', path: '/about' },
    { label: 'Services', path: '/services' },
    { label: 'Our Process', path: '/process' },
    { label: 'Portfolio', path: '/portfolio' },
    { label: 'Contact', path: '/contact' },
    { label: 'Privacy Policy', path: '/privacy' },
  ];
}
