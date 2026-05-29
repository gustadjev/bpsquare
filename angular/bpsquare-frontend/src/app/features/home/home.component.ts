import { Component, OnInit, inject, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { MatIconModule } from '@angular/material/icon';

import { WordpressApiService } from '../../core/services/wordpress-api.service';
import { SeoService } from '../../core/services/seo.service';
import { ServiceCardComponent } from '../../shared/components/service-card/service-card.component';
import { CtaSectionComponent } from '../../shared/components/cta-section/cta-section.component';
import { LoadingStateComponent } from '../../shared/components/loading-state/loading-state.component';
import { Service } from '../../core/models/service.model';

interface ProcessStep {
  icon: string;
  label: string;
  desc: string;
}

interface WhyItem {
  icon: string;
  heading: string;
  body: string;
}

@Component({
  selector: 'bps-home',
  standalone: true,
  imports: [RouterLink, MatIconModule, ServiceCardComponent, CtaSectionComponent, LoadingStateComponent],
  templateUrl: './home.component.html',
  styleUrl: './home.component.scss',
})
export class HomeComponent implements OnInit {
  private readonly api = inject(WordpressApiService);
  private readonly seo = inject(SeoService);

  services    = signal<Service[]>([]);
  servicesLoading = signal(true);

  readonly processSteps: ProcessStep[] = [
    { icon: 'search', label: 'Discover',  desc: 'Understand your goals and current situation' },
    { icon: 'checklist', label: 'Plan',   desc: 'Define requirements, scope, and timeline' },
    { icon: 'design_services', label: 'Design', desc: 'Wireframes and visual design concepts' },
    { icon: 'code', label: 'Build',       desc: 'Development with regular check-ins' },
    { icon: 'bug_report', label: 'Test',  desc: 'Quality review and client walkthroughs' },
    { icon: 'rocket_launch', label: 'Launch', desc: 'Deployment and go-live support' },
    { icon: 'support_agent', label: 'Support', desc: 'Ongoing maintenance and updates' },
  ];

  readonly whyItems: WhyItem[] = [
    { icon: 'business_center', heading: 'Business-First Approach', body: 'We start with your goals, not the technology. Every decision is tied back to your business outcomes.' },
    { icon: 'chat', heading: 'Clear Communication', body: 'No jargon. We keep you informed at every step and translate technical concepts into plain language.' },
    { icon: 'trending_up', heading: 'Scalable Solutions', body: 'We build for where you are today and where you\'re going tomorrow.' },
    { icon: 'diversity_3', heading: 'Community Focus', body: 'Proud to support small, minority-owned, and community-based businesses with the same quality solutions as larger enterprises.' },
    { icon: 'verified', heading: 'Practical Technology', body: 'We recommend technologies that fit your budget, team, and needs — not the most expensive options.' },
    { icon: 'local_hospital', heading: 'Domain Experience', body: 'Experience across healthcare, finance, and business systems means we understand real-world complexity.' },
  ];

  ngOnInit(): void {
    this.seo.setDefault();
    this.api.getServices().subscribe((services) => {
      this.services.set(services);
      this.servicesLoading.set(false);
    });
  }
}
