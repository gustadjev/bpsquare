import { Component, OnInit, inject } from '@angular/core';
import { MatIconModule } from '@angular/material/icon';
import { RouterLink } from '@angular/router';
import { SeoService } from '../../core/services/seo.service';
import { CtaSectionComponent } from '../../shared/components/cta-section/cta-section.component';
import { PageHeroComponent } from '../../shared/components/page-hero/page-hero.component';

export interface ProcessPhase {
  number: number;
  icon: string;
  title: string;
  description: string;
  details: string[];
}

@Component({
  selector: 'bps-process',
  standalone: true,
  imports: [MatIconModule, RouterLink, CtaSectionComponent, PageHeroComponent],
  templateUrl: './process.component.html',
  styleUrl: './process.component.scss',
})
export class ProcessComponent implements OnInit {
  private readonly seo = inject(SeoService);

  readonly phases: ProcessPhase[] = [
    {
      number: 1, icon: 'search', title: 'Discovery Call',
      description: 'We start by learning how your business works today and where the workflow is creating friction.',
      details: ['Focused call or meeting', 'No technical knowledge required', 'Workflow and pain-point review', 'Clear next-step recommendation'],
    },
    {
      number: 2, icon: 'assignment', title: 'Requirements Gathering',
      description: 'After the discovery call, we document what we heard and work with you to define exactly what needs to be built.',
      details: ['Written summary of your goals', 'Feature list and scope definition', 'User journey mapping', 'Content and integration inventory'],
    },
    {
      number: 3, icon: 'calculate', title: 'Estimate & Proposal',
      description: 'We prepare a clear, detailed proposal that explains what we will build, how long it will take, and what it will cost.',
      details: ['Itemized scope', 'Timeline with milestones', 'Clear pricing (fixed or hourly)', 'Payment schedule options'],
    },
    {
      number: 4, icon: 'design_services', title: 'Design & Wireframe',
      description: 'Before we write code, we design how the tool, page, form, or admin flow should work for real users.',
      details: ['Page layouts and wireframes', 'Workflow screens', 'Your review and approval', 'Design revisions included'],
    },
    {
      number: 5, icon: 'code', title: 'Development',
      description: 'We build your project using clean, well-organized code and follow a structured development approach.',
      details: ['Regular progress updates', 'Staging environment for review', 'Iterative development sprints', 'Version-controlled codebase'],
    },
    {
      number: 6, icon: 'fact_check', title: 'Testing & Review',
      description: 'Before anything goes live, we test thoroughly and walk you through the final product.',
      details: ['Functional and browser testing', 'Mobile and responsive testing', 'Client walkthrough session', 'Feedback and final adjustments'],
    },
    {
      number: 7, icon: 'rocket_launch', title: 'Launch',
      description: 'We handle the deployment, make sure everything is running correctly, and support you through go-live.',
      details: ['Production deployment', 'Domain and DNS setup', 'SSL and security configuration', 'Launch monitoring'],
    },
    {
      number: 8, icon: 'support_agent', title: 'Ongoing Support',
      description: 'Our relationship doesn\'t end at launch. We offer maintenance plans and are available when you need us.',
      details: ['Monthly maintenance plans', 'Bug fixes and updates', 'Content updates on request', 'Performance reviews'],
    },
  ];

  ngOnInit(): void {
    this.seo.set({
      title: 'How We Build',
      description: 'Learn how BPSquare LLC scopes, designs, builds, tests, launches, and supports custom web apps, internal tools, WordPress systems, and workflow automation.',
      canonicalUrl: 'https://bpsquarellc.com/process',
    });
  }
}
