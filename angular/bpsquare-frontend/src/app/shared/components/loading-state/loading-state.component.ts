import { Component } from '@angular/core';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';

@Component({
  selector: 'bps-loading-state',
  standalone: true,
  imports: [MatProgressSpinnerModule],
  template: `
    <div class="loading-state" role="status" aria-label="Loading content">
      <mat-spinner diameter="48" strokeWidth="4" />
      <p>Loading...</p>
    </div>
  `,
  styles: [`
    .loading-state {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 1rem;
      padding: 4rem 2rem;
      color: #475569;
      font-size: 0.875rem;
    }
  `],
})
export class LoadingStateComponent {}
