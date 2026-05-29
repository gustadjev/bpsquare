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
    { icon: 'diversity_3', title: 'Community Commitment', description: 'We are intentional about supporting small, minority-owned, and community-focused businesses with the same expertise larger companies receive.' },
    { icon: 'settings', title: 'Practical Solutions', description: 'Technology should solve problems and enable growth. We recommend what fits your situation — not the most complex or expensive option.' },
    { icon: 'people', title: 'Long-Term Partnership', description: 'Our relationship doesn\'t end at launch. We want to be your go-to technology partner as your business grows.' },
    { icon: 'school', title: 'Client Education', description: 'We help you understand what we built and why, so you feel confident managing and evolving your digital presence.' },
  ];

  ngOnInit(): void {
    this.seo.set({
      title: 'About Us',
      description: 'BPSquare LLC is an IT consulting and software development company dedicated to helping small businesses, minority-owned businesses, and growing organizations build reliable digital solutions.',
      canonicalUrl: 'https://bpsquarellc.com/about',
    });
  }
}
