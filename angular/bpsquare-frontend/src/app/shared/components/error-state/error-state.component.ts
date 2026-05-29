import { Component, Input, Output, EventEmitter } from '@angular/core';
import { MatIconModule } from '@angular/material/icon';
import { MatButtonModule } from '@angular/material/button';

@Component({
  selector: 'bps-error-state',
  standalone: true,
  imports: [MatIconModule, MatButtonModule],
  template: `
    <div class="error-state" role="alert">
      <mat-icon class="error-state__icon">error_outline</mat-icon>
      <h3>{{ heading }}</h3>
      <p>{{ message }}</p>
      @if (showRetry) {
        <button mat-stroked-button color="primary" (click)="retry.emit()">Try Again</button>
      }
    </div>
  `,
  styles: [`
    .error-state {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 1rem;
      padding: 4rem 2rem;
      text-align: center;
      color: #475569;
    }
    .error-state__icon {
      font-size: 3rem;
      width: 3rem;
      height: 3rem;
      color: #dc2626;
    }
    h3 { margin: 0; color: #1e293b; font-size: 1.25rem; }
    p  { margin: 0; font-size: 0.875rem; }
  `],
})
export class ErrorStateComponent {
  @Input() heading = 'Something went wrong';
  @Input() message = 'We could not load this content. Please try again.';
  @Input() showRetry = true;
  @Output() retry = new EventEmitter<void>();
}
