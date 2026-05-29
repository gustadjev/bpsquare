import { Component, Input } from '@angular/core';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'bps-cta-section',
  standalone: true,
  imports: [RouterLink],
  templateUrl: './cta-section.component.html',
  styleUrl: './cta-section.component.scss',
})
export class CtaSectionComponent {
  @Input() headline = "Ready to Build Something Great?";
  @Input() subtext = "Schedule a free consultation and let's talk about your project.";
  @Input() primaryLabel = "Start a Project";
  @Input() primaryPath = "/contact";
  @Input() secondaryLabel?: string;
  @Input() secondaryPath?: string;
}
