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
    <bps-json-ld />
    <bps-header />
    <main id="main-content" class="app-main">
      <router-outlet />
    </main>
    <bps-footer />
  `,
  styles: [`
    .app-main {
      min-height: 100vh;
      padding-top: 68px; /* fixed header height */
    }
  `],
})
export class AppComponent {}


