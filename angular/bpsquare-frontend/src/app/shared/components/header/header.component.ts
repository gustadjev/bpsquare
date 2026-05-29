import { Component, HostListener, signal } from '@angular/core';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { MatIconModule } from '@angular/material/icon';
import { MatButtonModule } from '@angular/material/button';
import { CommonModule } from '@angular/common';

interface NavLink {
  label: string;
  path: string;
}

@Component({
  selector: 'bps-header',
  standalone: true,
  imports: [CommonModule, RouterLink, RouterLinkActive, MatIconModule, MatButtonModule],
  templateUrl: './header.component.html',
  styleUrl: './header.component.scss',
})
export class HeaderComponent {
  readonly navLinks: NavLink[] = [
    { label: 'Home',      path: '/' },
    { label: 'About',     path: '/about' },
    { label: 'Services',  path: '/services' },
    { label: 'Process',   path: '/process' },
    { label: 'Portfolio', path: '/portfolio' },
    { label: 'Contact',   path: '/contact' },
  ];

  menuOpen = signal(false);
  scrolled  = signal(false);

  @HostListener('window:scroll')
  onScroll(): void {
    this.scrolled.set(window.scrollY > 20);
  }

  toggleMenu(): void {
    this.menuOpen.update((v) => !v);
  }

  closeMenu(): void {
    this.menuOpen.set(false);
  }
}
