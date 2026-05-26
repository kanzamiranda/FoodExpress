import { Component, signal, computed, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { NavbarComponent } from '../../shared/navbar/navbar';
import { CartService, Product } from '../../services/cart.service';
import { LucideAngularModule } from 'lucide-angular';
import { I18nService } from '../../services/i18n.service';
import { environment } from '../../../environments/environment';

@Component({
  selector: 'app-menu',
  standalone: true,
  imports: [CommonModule, FormsModule, NavbarComponent, LucideAngularModule],
  templateUrl: './menu.html',
  styleUrl: './menu.css',
})
export class MenuComponent implements OnInit {
  products = signal<Product[]>([]);
  categories = signal<string[]>(['Todos', 'Pizza', 'Burgers', 'Massas', 'Saladas', 'Sobremesas', 'Bebidas']);
  
  categoryIcons: Record<string, string> = {
    'Todos': '🍽️', 'Pizza': '🍕', 'Burgers': '🍔',
    'Massas': '🍝', 'Saladas': '🥗', 'Sobremesas': '🍰', 'Bebidas': '🥤',
  };

  activeCategory = signal('Todos');
  searchQuery = signal('');
  cartOpen = signal(false);
  addedId = signal<any | null>(null);
  checkoutDone = signal(false);
  
  loadingProducts = signal(false);
  loadingCategories = signal(false);
  errorProducts = signal('');

  constructor(
    private http: HttpClient,
    public cartService: CartService,
    public i18n: I18nService
  ) {}

  ngOnInit() {
    this.loadCategories();
    this.loadProducts();
  }

  loadCategories() {
    this.loadingCategories.set(true);
    this.http.get<{ success: boolean; data: string[] }>(`${environment.apiUrl}/categories`).subscribe({
      next: (res) => {
        this.loadingCategories.set(false);
        if (res.success && res.data) {
          this.categories.set(res.data);
        }
      },
      error: () => {
        this.loadingCategories.set(false);
      }
    });
  }

  loadProducts() {
    this.loadingProducts.set(true);
    this.errorProducts.set('');
    this.http.get<{ success: boolean; data: Product[] }>(`${environment.apiUrl}/products`).subscribe({
      next: (res) => {
        this.loadingProducts.set(false);
        if (res.success && res.data) {
          this.products.set(res.data);
        }
      },
      error: (err) => {
        this.loadingProducts.set(false);
        this.errorProducts.set('Não foi possível carregar os pratos do cardápio.');
        console.error(err);
      }
    });
  }

  filteredProducts = computed(() => {
    const cat = this.activeCategory();
    const q = this.searchQuery().toLowerCase();
    return this.products().filter(p => {
      const matchesCat = cat === 'Todos' || p.category === cat;
      const matchesQ = !q || p.name.toLowerCase().includes(q) || p.description.toLowerCase().includes(q);
      return matchesCat && matchesQ;
    });
  });

  selectCategory(cat: string) { this.activeCategory.set(cat); }

  onSearch(val: string) { this.searchQuery.set(val); }

  addToCart(product: Product) {
    this.cartService.addToCart(product);
    this.addedId.set(product.id);
    setTimeout(() => this.addedId.set(null), 1200);
    this.cartOpen.set(true);
  }

  toggleCart() { this.cartOpen.update(v => !v); }
  closeCart() { this.cartOpen.set(false); }

  checkout() {
    this.checkoutDone.set(true);
    setTimeout(() => {
      this.cartService.clearCart();
      this.checkoutDone.set(false);
      this.cartOpen.set(false);
    }, 2500);
  }

  formatPrice(price: number): string {
    return price.toLocaleString('pt-AO') + ' KZ';
  }

  badgeColor(badge?: string): string {
    const map: Record<string, string> = {
      'Popular': '#ff6b35', 'Best Seller': '#e85d04',
      'Novo': '#3b82f6', 'Vegan': '#22c55e', 'Saudável': '#22c55e',
    };
    return badge ? map[badge] || '#94a3b8' : '#94a3b8';
  }
}
