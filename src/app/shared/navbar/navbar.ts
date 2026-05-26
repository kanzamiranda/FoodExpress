import { Component, Input, signal, HostListener } from '@angular/core';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { CommonModule } from '@angular/common';
import { CartService } from '../../services/cart.service';
import { I18nService } from '../../services/i18n.service';
import { ThemeService } from '../../services/theme.service';
import { LucideAngularModule } from 'lucide-angular';

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [CommonModule, RouterLink, RouterLinkActive, LucideAngularModule],
  templateUrl: './navbar.html',
  styleUrl: './navbar.css',
})
export class NavbarComponent {
  @Input() transparent = false;

  scrolled = signal(false);
  mobileOpen = signal(false);

  constructor(
    public cartService: CartService,
    public i18n: I18nService,
    public theme: ThemeService
  ) {}

  @HostListener('window:scroll')
  onScroll() {
    this.scrolled.set(window.scrollY > 40);
  }

  toggleMobile() {
    this.mobileOpen.update(v => !v);
  }

  closeMobile() {
    this.mobileOpen.set(false);
  }

  setLanguage(language: string) {
    this.i18n.setLanguage(language as any);
  }

  openGoogleTranslate() {
    if (typeof window !== 'undefined') {
      window.open(this.i18n.googleTranslateUrl(), '_blank');
    }
  }
}
