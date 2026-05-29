import { Component, OnInit, inject } from '@angular/core';
import { SeoService } from '../../core/services/seo.service';

@Component({
  selector: 'bps-privacy',
  standalone: true,
  imports: [],
  templateUrl: './privacy.component.html',
  styleUrl: './privacy.component.scss',
})
export class PrivacyComponent implements OnInit {
  private readonly seo = inject(SeoService);
  readonly lastUpdated = 'January 2025';

  ngOnInit(): void {
    this.seo.set({
      title: 'Privacy Policy',
      description: 'BPSquare LLC Privacy Policy — how we collect, use, and protect information submitted through our website.',
      canonicalUrl: 'https://bpsquarellc.com/privacy',
    });
  }
}
