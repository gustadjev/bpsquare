import { Component, Input } from '@angular/core';
import { RouterLink } from '@angular/router';
import { MatIconModule } from '@angular/material/icon';
import { Service } from '../../../core/models/service.model';

@Component({
  selector: 'bps-service-card',
  standalone: true,
  imports: [RouterLink, MatIconModule],
  templateUrl: './service-card.component.html',
  styleUrl: './service-card.component.scss',
})
export class ServiceCardComponent {
  @Input({ required: true }) service!: Service;
  @Input() showFeatures = false;
}
