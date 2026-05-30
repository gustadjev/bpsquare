import { Component, OnInit, inject, signal } from '@angular/core';
import { MatIconModule } from '@angular/material/icon';
import { RouterLink } from '@angular/router';

import { WordpressApiService } from '../../core/services/wordpress-api.service';
import { SeoService } from '../../core/services/seo.service';
import { ServiceCardComponent } from '../../shared/components/service-card/service-card.component';
import { LoadingStateComponent } from '../../shared/components/loading-state/loading-state.component';
import { ErrorStateComponent } from '../../shared/components/error-state/error-state.component';
import { CtaSectionComponent } from '../../shared/components/cta-section/cta-section.component';
import { PageHeroComponent } from '../../shared/components/page-hero/page-hero.component';
import { Service } from '../../core/models/service.model';

interface FocusService {
  icon: string;
  title: string;
  summary: string;
  details: string[];
}

@Component({
  selector: 'bps-services',
  standalone: true,
  imports: [MatIconModule, RouterLink, ServiceCardComponent, LoadingStateComponent, ErrorStateComponent, CtaSectionComponent, PageHeroComponent],
  templateUrl: './services.component.html',
  styleUrl: './services.component.scss',
})
export class ServicesComponent implements OnInit {
  private readonly api = inject(WordpressApiService);
  private readonly seo = inject(SeoService);

  services = signal<Service[]>([]);
  loading  = signal(true);
  error    = signal(false);

  readonly focusServices: FocusService[] = [
    {
      icon: 'code',
      title: 'Custom Web Apps for Service Businesses',
      summary: 'Client portals, intake systems, dashboards, and business-specific tools that support the way your team works.',
      details: ['Role-based workflows', 'Admin dashboards', 'Forms, requests, and status tracking', 'REST API and database integration'],
    },
    {
      icon: 'schema',
      title: 'Internal Tools & Workflow Automation',
      summary: 'Practical systems that reduce spreadsheet work, repeated emails, and manual handoffs between people or platforms.',
      details: ['Workflow mapping', 'Form and notification automation', 'Reporting views', 'Data movement between tools'],
    },
    {
      icon: 'trending_up',
      title: 'Small Business Operations Modernization',
      summary: 'A phased path from informal manual processes to clearer, trackable digital operations.',
      details: ['Current-state review', 'Process redesign', 'Phased implementation plans', 'Training-friendly handoff'],
    },
    {
      icon: 'integration_instructions',
      title: 'WordPress + Angular & Business Systems Integration',
      summary: 'Headless WordPress, Angular frontends, and custom REST endpoints for content-managed business experiences.',
      details: ['Custom post types', 'Angular frontends', 'WordPress REST API', 'Business system integrations'],
    },
    {
      icon: 'dashboard_customize',
      title: 'Custom WordPress Theme Development',
      summary: 'Lightweight, business-specific WordPress themes and admin experiences built for clarity, speed, and maintainability.',
      details: ['Custom themes', 'Admin settings', 'Reusable templates', 'SEO and performance foundations'],
    },
  ];

  ngOnInit(): void {
    this.seo.set({
      title: 'Custom Web Apps, Internal Tools & WordPress Systems',
      description: 'BPSquare LLC builds custom web apps, internal tools, workflow automation, WordPress themes, and Angular business systems for service-based small businesses.',
      canonicalUrl: 'https://bpsquarellc.com/services',
    });
    this.load();
  }

  load(): void {
    this.loading.set(true);
    this.error.set(false);
    this.api.getServices().subscribe({
      next: (s) => { this.services.set(s); this.loading.set(false); },
      error: ()  => { this.error.set(true); this.loading.set(false); },
    });
  }
}
