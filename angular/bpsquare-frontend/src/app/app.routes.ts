import { Routes } from '@angular/router';

export const routes: Routes = [
  {
    path: '',
    loadComponent: () =>
      import('./features/home/home.component').then((m) => m.HomeComponent),
    title: 'BPSquare LLC — Practical Technology Solutions for Growing Businesses',
  },
  {
    path: 'about',
    loadComponent: () =>
      import('./features/about/about.component').then((m) => m.AboutComponent),
    title: 'About Us | BPSquare LLC',
  },
  {
    path: 'services',
    loadComponent: () =>
      import('./features/services/services.component').then((m) => m.ServicesComponent),
    title: 'Services | BPSquare LLC',
  },
  {
    path: 'process',
    loadComponent: () =>
      import('./features/process/process.component').then((m) => m.ProcessComponent),
    title: 'Our Process | BPSquare LLC',
  },
  {
    path: 'portfolio',
    loadComponent: () =>
      import('./features/portfolio/portfolio.component').then((m) => m.PortfolioComponent),
    title: 'Portfolio & Case Studies | BPSquare LLC',
  },
  {
    path: 'contact',
    loadComponent: () =>
      import('./features/contact/contact.component').then((m) => m.ContactComponent),
    title: 'Contact Us | BPSquare LLC',
  },
  {
    path: 'privacy',
    loadComponent: () =>
      import('./features/privacy/privacy.component').then((m) => m.PrivacyComponent),
    title: 'Privacy Policy | BPSquare LLC',
  },
  { path: '**', redirectTo: '' },
];
