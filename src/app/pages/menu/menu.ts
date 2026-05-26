import { Component, signal, computed } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { NavbarComponent } from '../../shared/navbar/navbar';
import { CartService, Product } from '../../services/cart.service';
import { LucideAngularModule } from 'lucide-angular';
import { I18nService } from '../../services/i18n.service';

const ALL_PRODUCTS: Product[] = [
  // Pizzas
  { id: 1, name: 'Pizza Margherita', description: 'Molho de tomate, mozzarella fresca e manjericão', price: 12.50, emoji: '🍕', category: 'Pizza', badge: 'Popular', rating: 4.9, prepTime: '25 min' },
  { id: 2, name: 'Pizza Pepperoni', description: 'Pepperoni fatiado, queijo mozzarella e orégãos', price: 13.90, emoji: '🍕', category: 'Pizza', rating: 4.8, prepTime: '25 min' },
  { id: 3, name: 'Pizza Vegetariana', description: 'Pimentos, cogumelos, azeitonas e queijo', price: 13.00, emoji: '🍕', category: 'Pizza', badge: 'Vegan', rating: 4.7, prepTime: '25 min' },
  { id: 4, name: 'Pizza 4 Queijos', description: 'Mozzarella, gorgonzola, parmesão e gouda', price: 14.50, emoji: '🍕', category: 'Pizza', rating: 4.9, prepTime: '28 min' },
  { id: 5, name: 'Pizza Frango', description: 'Frango grelhado, pimentos e creme de alho', price: 13.50, emoji: '🍕', category: 'Pizza', rating: 4.6, prepTime: '30 min' },
  // Burgers
  { id: 6, name: 'Classic Burger', description: 'Carne 180g, alface, tomate, queijo cheddar', price: 9.90, emoji: '🍔', category: 'Burgers', badge: 'Best Seller', rating: 4.8, prepTime: '20 min' },
  { id: 7, name: 'BBQ Burger', description: 'Dupla carne, bacon crocante e molho BBQ', price: 12.50, emoji: '🍔', category: 'Burgers', rating: 4.9, prepTime: '22 min' },
  { id: 8, name: 'Frango Crispy', description: 'Frango crocante, coleslaw e pickles', price: 10.50, emoji: '🍔', category: 'Burgers', rating: 4.7, prepTime: '20 min' },
  { id: 9, name: 'Double Smash', description: 'Dois smash burgers com queijo americano', price: 14.90, emoji: '🍔', category: 'Burgers', badge: 'Novo', rating: 4.9, prepTime: '25 min' },
  // Massas
  { id: 10, name: 'Pasta Carbonara', description: 'Spaghetti, pancetta, ovo e parmesão', price: 11.00, emoji: '🍝', category: 'Massas', rating: 4.8, prepTime: '20 min' },
  { id: 11, name: 'Bolonhesa Clássica', description: 'Molho bolonhesa lento com tagliatelle', price: 10.50, emoji: '🍝', category: 'Massas', badge: 'Popular', rating: 4.7, prepTime: '20 min' },
  { id: 12, name: 'Penne ao Pesto', description: 'Penne com pesto de manjericão e pinhões', price: 10.90, emoji: '🍝', category: 'Massas', badge: 'Vegan', rating: 4.6, prepTime: '18 min' },
  { id: 13, name: 'Lasanha de Carne', description: 'Lasanha italiana com carne e béchamel', price: 12.00, emoji: '🍝', category: 'Massas', rating: 4.8, prepTime: '30 min' },
  // Saladas
  { id: 14, name: 'Salada Caesar', description: 'Alface romana, croutons, parmesão e molho Caesar', price: 8.50, emoji: '🥗', category: 'Saladas', rating: 4.6, prepTime: '10 min' },
  { id: 15, name: 'Salada Grega', description: 'Tomate, pepino, azeitonas, feta e orégãos', price: 8.90, emoji: '🥗', category: 'Saladas', badge: 'Vegan', rating: 4.7, prepTime: '10 min' },
  { id: 16, name: 'Salada Tropical', description: 'Frango, manga, abacate e vinagrete de lima', price: 10.50, emoji: '🥗', category: 'Saladas', rating: 4.8, prepTime: '12 min' },
  // Sobremesas
  { id: 17, name: 'Cheesecake', description: 'Cheesecake de frutos vermelhos cremoso', price: 5.50, emoji: '🍰', category: 'Sobremesas', badge: 'Popular', rating: 4.9, prepTime: '5 min' },
  { id: 18, name: 'Brownie Quente', description: 'Brownie de chocolate com gelado de baunilha', price: 5.90, emoji: '🍫', category: 'Sobremesas', rating: 4.9, prepTime: '8 min' },
  { id: 19, name: 'Gelado Artesanal', description: 'Seleção de 3 bolas de gelado artesanal', price: 4.90, emoji: '🍦', category: 'Sobremesas', rating: 4.7, prepTime: '3 min' },
  { id: 20, name: 'Pudim Português', description: 'Pudim flan tradicional com caramelo', price: 4.50, emoji: '🍮', category: 'Sobremesas', rating: 4.8, prepTime: '5 min' },
  // Bebidas
  { id: 21, name: 'Sumo Natural', description: 'Sumo de laranja espremido na hora', price: 3.50, emoji: '🍊', category: 'Bebidas', rating: 4.8, prepTime: '3 min' },
  { id: 22, name: 'Coca-Cola', description: 'Coca-Cola gelada 33cl', price: 2.50, emoji: '🥤', category: 'Bebidas', rating: 4.5, prepTime: '2 min' },
  { id: 23, name: 'Água com Gás', description: 'Água mineral com gás 50cl', price: 1.90, emoji: '💧', category: 'Bebidas', rating: 4.4, prepTime: '1 min' },
  { id: 24, name: 'Smoothie Frutas', description: 'Manga, morango e banana batidos', price: 4.90, emoji: '🥤', category: 'Bebidas', badge: 'Saudável', rating: 4.9, prepTime: '5 min' },
];

@Component({
  selector: 'app-menu',
  standalone: true,
  imports: [CommonModule, FormsModule, NavbarComponent, LucideAngularModule],
  templateUrl: './menu.html',
  styleUrl: './menu.css',
})
export class MenuComponent {
  constructor(public cartService: CartService, public i18n: I18nService) {}

  categories = ['Todos', 'Pizza', 'Burgers', 'Massas', 'Saladas', 'Sobremesas', 'Bebidas'];
  categoryIcons: Record<string, string> = {
    'Todos': '🍽️', 'Pizza': '🍕', 'Burgers': '🍔',
    'Massas': '🍝', 'Saladas': '🥗', 'Sobremesas': '🍰', 'Bebidas': '🥤',
  };

  activeCategory = signal('Todos');
  searchQuery = signal('');
  cartOpen = signal(false);
  addedId = signal<number | null>(null);
  checkoutDone = signal(false);

  filteredProducts = computed(() => {
    const cat = this.activeCategory();
    const q = this.searchQuery().toLowerCase();
    return ALL_PRODUCTS.filter(p => {
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
    return price.toFixed(2).replace('.', ',') + ' €';
  }

  badgeColor(badge?: string): string {
    const map: Record<string, string> = {
      'Popular': '#ff6b35', 'Best Seller': '#e85d04',
      'Novo': '#3b82f6', 'Vegan': '#22c55e', 'Saudável': '#22c55e',
    };
    return badge ? map[badge] || '#94a3b8' : '#94a3b8';
  }
}
