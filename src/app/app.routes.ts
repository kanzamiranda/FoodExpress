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
    path: 'login',
    loadComponent: () =>
      import('./pages/auth/login/login').then(m => m.LoginComponent),
    title: 'Entrar — FoodExpress',
  },
  {
    path: 'register',
    loadComponent: () =>
      import('./pages/auth/register/register').then(m => m.RegisterComponent),
    title: 'Cadastrar — FoodExpress',
  },
  {
    path: 'reset-password',
    loadComponent: () =>
      import('./pages/auth/reset-password/reset-password').then(m => m.ResetPasswordComponent),
    title: 'Recuperar Senha — FoodExpress',
  },
  {
    path: 'admin',
    loadComponent: () =>
      import('./pages/admin/admin').then(m => m.AdminComponent),
    title: 'Admin — FoodExpress',
  },
  {
    path: '**',
    redirectTo: '',
  },
];
