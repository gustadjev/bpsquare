import { Component, OnInit, inject } from '@angular/core';
import { RouterLink } from '@angular/router';
import { MatIconModule } from '@angular/material/icon';
import { SeoService } from '../../core/services/seo.service';
import { CtaSectionComponent } from '../../shared/components/cta-section/cta-section.component';
import { PageHeroComponent } from '../../shared/components/page-hero/page-hero.component';

interface Value {
  icon: string;
  title: string;
  description: string;
}

@Component({
  selector: 'bps-about',
  standalone: true,
  imports: [RouterLink, MatIconModule, CtaSectionComponent, PageHeroComponent],
  templateUrl: './about.component.html',
  styleUrl: './about.component.scss',
})
export class AboutComponent implements OnInit {
  private readonly seo = inject(SeoService);

  readonly values: Value[] = [
    { icon: 'handshake', title: 'Honest Communication', description: 'We tell you what you need to hear, not what you want to hear. Transparent timelines, realistic estimates, and plain-language updates.' },
    { icon: 'verified', title: 'Quality Over Shortcuts', description: 'We build things right the first time. Clean code, tested solutions, and well-organized systems you can maintain and grow.' },
    { icon: 'diversity_3', title: 'Service Business Focus', description: 'We are intentional about helping small, minority-owned, and community-focused service businesses improve the way work gets done.' },
    { icon: 'settings', title: 'Practical Solutions', description: 'Technology should solve problems and enable growth. We recommend what fits your situation — not the most complex or expensive option.' },
    { icon: 'route', title: 'Phased Progress', description: 'Early projects can start small, prove the workflow, and grow into a stronger system over time.' },
    { icon: 'school', title: 'Client Education', description: 'We help you understand what we built and why, so you feel confident managing and evolving your digital presence.' },
  ];

  ngOnInit(): void {
    this.seo.set({
      title: 'About BPSquare LLC',
      description: 'BPSquare LLC is a first-year, founder-led full-stack studio building custom web apps, workflow automation, WordPress systems, and Angular integrations for service-based small businesses.',
      canonicalUrl: 'https://bpsquarellc.com/about',
    });
  }
}
