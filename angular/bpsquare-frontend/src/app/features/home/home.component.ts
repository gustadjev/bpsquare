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
    { icon: 'business_center', heading: 'Operations-First Approach', body: 'We start with how your service business actually works, then shape the technology around the workflow.' },
    { icon: 'chat', heading: 'Clear Communication', body: 'No jargon. You get practical explanations, honest tradeoffs, and steady updates while the work moves forward.' },
    { icon: 'schema', heading: 'Systems That Connect', body: 'We build websites, admin tools, forms, APIs, and automations that support the same business process.' },
    { icon: 'diversity_3', heading: 'Small Business Focus', body: 'BPSquare is built for service businesses that need serious engineering without a large-agency engagement.' },
    { icon: 'verified', heading: 'Production-Minded Build', body: 'Security, accessibility, SEO, performance, and maintainability are considered from the beginning.' },
    { icon: 'local_hospital', heading: 'Domain Experience', body: 'Experience across healthcare, finance, and business systems helps us respect accuracy, privacy, and usability.' },
  ];

  readonly deliveryPrinciples: WhyItem[] = [
    { icon: 'person', heading: 'Direct Access', body: 'You work with the person scoping and building the solution, not a handoff chain.' },
    { icon: 'route', heading: 'Phased Scope', body: 'Projects can start with a practical first release, then grow once the workflow is proven.' },
    { icon: 'handyman', heading: 'Maintainable Systems', body: 'The goal is software your business can keep using, updating, and understanding after launch.' },
  ];

  ngOnInit(): void {
    this.seo.setDefault();
    this.api.getServices().subscribe((services) => {
      this.services.set(services);
      this.servicesLoading.set(false);
    });
  }
}
