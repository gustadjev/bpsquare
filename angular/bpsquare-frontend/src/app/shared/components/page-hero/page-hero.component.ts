import { Component, Input } from '@angular/core';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'bps-page-hero',
  standalone: true,
  imports: [RouterLink],
  templateUrl: './page-hero.component.html',
  styleUrl: './page-hero.component.scss',
})
export class PageHeroComponent {
  @Input({ required: true }) headline!: string;
  @Input() subheadline?: string;
  @Input() eyebrow?: string;
  @Input() primaryCtaLabel?: string;
  @Input() primaryCtaPath?: string;
  @Input() secondaryCtaLabel?: string;
  @Input() secondaryCtaPath?: string;
  @Input() dark = true;
}
