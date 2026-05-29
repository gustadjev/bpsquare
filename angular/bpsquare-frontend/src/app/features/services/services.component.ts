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

  ngOnInit(): void {
    this.seo.set({
      title: 'Services',
      description: 'BPSquare LLC offers website development, custom web application development, UI/UX design, business process automation, technical consulting, and ongoing maintenance & support.',
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
