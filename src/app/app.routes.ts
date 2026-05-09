import { Routes } from '@angular/router';

export const routes: Routes = [
  {
    path: '',
    loadComponent: () =>
      import('./pages/landing/landing').then(m => m.LandingComponent),
    title: 'FoodExpress — Peça a sua comida favorita online',
  },
  {
    path: 'menu',
    loadComponent: () =>
      import('./pages/menu/menu').then(m => m.MenuComponent),
    title: 'Cardápio — FoodExpress',
  },
  {
    path: '**',
    redirectTo: '',
  },
];
