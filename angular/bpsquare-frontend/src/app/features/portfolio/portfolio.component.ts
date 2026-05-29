import { Component, OnInit, inject, signal } from '@angular/core';
import { MatIconModule } from '@angular/material/icon';
import { RouterLink } from '@angular/router';

import { WordpressApiService } from '../../core/services/wordpress-api.service';
import { SeoService } from '../../core/services/seo.service';
import { LoadingStateComponent } from '../../shared/components/loading-state/loading-state.component';
import { ErrorStateComponent } from '../../shared/components/error-state/error-state.component';
import { CtaSectionComponent } from '../../shared/components/cta-section/cta-section.component';
import { PageHeroComponent } from '../../shared/components/page-hero/page-hero.component';
import { CaseStudy } from '../../core/models/case-study.model';

@Component({
  selector: 'bps-portfolio',
  standalone: true,
  imports: [MatIconModule, RouterLink, LoadingStateComponent, ErrorStateComponent, CtaSectionComponent, PageHeroComponent],
  templateUrl: './portfolio.component.html',
  styleUrl: './portfolio.component.scss',
})
export class PortfolioComponent implements OnInit {
  private readonly api = inject(WordpressApiService);
  private readonly seo = inject(SeoService);

  caseStudies = signal<CaseStudy[]>([]);
  loading     = signal(true);
  error       = signal(false);
  expanded    = signal<number | null>(null);

  ngOnInit(): void {
    this.seo.set({
      title: 'Portfolio & Case Studies',
      description: 'See examples of BPSquare LLC project work. Browse case studies covering website development, custom web applications, and digital business solutions.',
      canonicalUrl: 'https://bpsquarellc.com/portfolio',
    });
    this.load();
  }

  load(): void {
    this.loading.set(true);
    this.error.set(false);
    this.api.getCaseStudies().subscribe({
      next: (cs) => { this.caseStudies.set(cs); this.loading.set(false); },
      error: ()  => { this.error.set(true); this.loading.set(false); },
    });
  }

  toggle(id: number): void {
    this.expanded.set(this.expanded() === id ? null : id);
  }
}
