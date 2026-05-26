import { Component, OnInit, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { NavbarComponent } from '../../shared/navbar/navbar';
import { LucideAngularModule } from 'lucide-angular';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { I18nService } from '../../services/i18n.service';
import { ThemeService } from '../../services/theme.service';
import { environment } from '../../../environments/environment';

interface CategoryDisplay {
  icon: string;
  name: string;
  count: number;
  image: string;
}

@Component({
  selector: 'app-landing',
  standalone: true,
  imports: [CommonModule, RouterLink, NavbarComponent, LucideAngularModule],
  templateUrl: './landing.html',
  styleUrl: './landing.css',
})
export class LandingComponent implements OnInit {
  constructor(
    public i18n: I18nService,
    public theme: ThemeService,
    private http: HttpClient
  ) {}

  categories = signal<CategoryDisplay[]>([
    { icon: '🍕', name: 'Pizza', count: 24, image: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=400&h=300&fit=crop' },
    { icon: '🍔', name: 'Burgers', count: 18, image: 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=400&h=300&fit=crop' },
    { icon: '🍝', name: 'Massas', count: 15, image: 'https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?w=400&h=300&fit=crop' },
    { icon: '🥗', name: 'Saladas', count: 12, image: 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400&h=300&fit=crop' },
    { icon: '🍰', name: 'Sobremesas', count: 20, image: 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=400&h=300&fit=crop' },
    { icon: '🥤', name: 'Bebidas', count: 16, image: 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=400&h=300&fit=crop' },
  ]);

  get features() {
    return [
      {
        icon: 'clock',
        title: this.i18n.t('featureDeliveryTitle'),
        desc: this.i18n.t('featureDeliveryDesc'),
        color: '#fbbf24',
      },
      {
        icon: 'check-circle',
        title: this.i18n.t('featurePaymentTitle'),
        desc: this.i18n.t('featurePaymentDesc'),
        color: '#22c55e',
      },
      {
        icon: 'globe',
        title: this.i18n.t('featureLangTitle'),
        desc: this.i18n.t('featureLangDesc'),
        color: '#3b82f6',
      },
      {
        icon: 'smartphone',
        title: this.i18n.t('featureResponsiveTitle'),
        desc: this.i18n.t('featureResponsiveDesc'),
        color: '#a855f7',
      },
    ];
  }

  get steps() {
    return [
      { step: '01', icon: 'map-pin', title: this.i18n.t('step1Title'), desc: this.i18n.t('step1Desc') },
      { step: '02', icon: 'menu', title: this.i18n.t('step2Title'), desc: this.i18n.t('step2Desc') },
      { step: '03', icon: 'arrow-right', title: this.i18n.t('step3Title'), desc: this.i18n.t('step3Desc') },
    ];
  }

  get testimonials() {
    return [
      { name: 'Ana Silva', role: this.i18n.t('testimonialRole1'), text: this.i18n.t('testimonialText1'), stars: 5, avatar: '👩‍💼' },
      { name: 'Carlos Mendes', role: this.i18n.t('testimonialRole2'), text: this.i18n.t('testimonialText2'), stars: 5, avatar: '👨‍💻' },
      { name: 'Maria João', role: this.i18n.t('testimonialRole3'), text: this.i18n.t('testimonialText3'), stars: 5, avatar: '👩‍👧' },
    ];
  }

  get stats() {
    return [
      { value: '50K+', label: this.i18n.t('statClients') },
      { value: '200+', label: this.i18n.t('statDishes') },
      { value: '30min', label: this.i18n.t('statDelivery') },
      { value: '4.9★', label: this.i18n.t('statRating') },
    ];
  }

  get heroCards() {
    return {
      main: { name: 'Pizza Margherita', price: '3.500 KZ', rating: '4.9', image: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=200&h=200&fit=crop' },
      left: { name: 'Classic Burger', price: '2.800 KZ', image: 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=120&h=120&fit=crop' },
      right: { name: 'Pasta Carbonara', price: '3.200 KZ', image: 'https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?w=120&h=120&fit=crop' },
    };
  }

  ngOnInit() {
    this.loadCategories();
  }

  loadCategories() {
    this.http.get<{ success: boolean; data: string[] }>(`${environment.apiUrl}/categories`).subscribe({
      next: (res) => {
        if (res.success && res.data) {
          const iconMap: Record<string, { icon: string; image: string }> = {
            'Pizza': { icon: '🍕', image: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=400&h=300&fit=crop' },
            'Burgers': { icon: '🍔', image: 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=400&h=300&fit=crop' },
            'Massas': { icon: '🍝', image: 'https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?w=400&h=300&fit=crop' },
            'Saladas': { icon: '🥗', image: 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400&h=300&fit=crop' },
            'Sobremesas': { icon: '🍰', image: 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=400&h=300&fit=crop' },
            'Bebidas': { icon: '🥤', image: 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=400&h=300&fit=crop' },
          };
          // Filter out 'Todos' if present, map to CategoryDisplay
          const mapped = res.data
            .filter(name => name !== 'Todos')
            .map(name => ({
              icon: iconMap[name]?.icon || '🍽️',
              name,
              count: Math.floor(Math.random() * 15) + 8,
              image: iconMap[name]?.image || 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=400&h=300&fit=crop',
            }));
          if (mapped.length > 0) {
            this.categories.set(mapped);
          }
        }
      },
      error: () => {
        // Keep default static categories on error
      }
    });
  }
}
