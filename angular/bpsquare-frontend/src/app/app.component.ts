import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { HeaderComponent } from './shared/components/header/header.component';
import { FooterComponent } from './shared/components/footer/footer.component';
import { JsonLdComponent } from './shared/components/json-ld/json-ld.component';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet, HeaderComponent, FooterComponent, JsonLdComponent],
  template: `
    <a class="skip-link" href="#main-content">Skip to main content</a>
    <bps-json-ld />
    <bps-header />
    <main id="main-content" class="app-main">
      <router-outlet />
    </main>
    <bps-footer />
  `,
  styles: [`
    .skip-link {
      position: fixed;
      left: 1rem;
      top: 1rem;
      z-index: 10000;
      transform: translateY(-150%);
      background: #ffffff;
      color: #1e40af;
      border: 2px solid #1e40af;
      border-radius: 6px;
      padding: 0.75rem 1rem;
      font-weight: 700;
      text-decoration: none;
    }

    .skip-link:focus {
      transform: translateY(0);
    }

    .app-main {
      min-height: 100vh;
      padding-top: 68px; /* fixed header height */
    }
  `],
})
export class AppComponent {}

