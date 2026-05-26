import { Injectable, signal, computed } from '@angular/core';

export interface Product {
  id: any;
  name: string;
  description: string;
  price: number;
  emoji: string;
  image?: string;
  category: string;
  badge?: string;
  rating: number;
  prepTime: string;
}

export interface CartItem {
  product: Product;
  quantity: number;
}

@Injectable({ providedIn: 'root' })
export class CartService {
  private _items = signal<CartItem[]>([]);

  readonly items = this._items.asReadonly();

  readonly totalItems = computed(() =>
    this._items().reduce((sum, i) => sum + i.quantity, 0)
  );

  readonly subtotal = computed(() =>
    this._items().reduce((sum, i) => sum + i.product.price * i.quantity, 0)
  );

  readonly deliveryFee = computed(() => (this.subtotal() > 0 ? 2500 : 0)); // Taxa de entrega de 2500 KZ

  readonly total = computed(() => this.subtotal() + this.deliveryFee());

  addToCart(product: Product) {
    this._items.update(items => {
      const existing = items.find(i => i.product.id === product.id);
      if (existing) {
        return items.map(i =>
          i.product.id === product.id
            ? { ...i, quantity: i.quantity + 1 }
            : i
        );
      }
      return [...items, { product, quantity: 1 }];
    });
  }

  removeOne(productId: any) {
    this._items.update(items => {
      const existing = items.find(i => i.product.id === productId);
      if (existing && existing.quantity === 1) {
        return items.filter(i => i.product.id !== productId);
      }
      return items.map(i =>
        i.product.id === productId
          ? { ...i, quantity: i.quantity - 1 }
          : i
      );
    });
  }

  removeItem(productId: any) {
    this._items.update(items => items.filter(i => i.product.id !== productId));
  }

  getQuantity(productId: any): number {
    return this._items().find(i => i.product.id === productId)?.quantity ?? 0;
  }

  clearCart() {
    this._items.set([]);
  }
}
