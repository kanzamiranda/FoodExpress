import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';
import { NavbarComponent } from '../../shared/navbar/navbar';
import { LucideAngularModule } from 'lucide-angular';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-landing',
  standalone: true,
  imports: [CommonModule, RouterLink, NavbarComponent, LucideAngularModule],
  templateUrl: './landing.html',
  styleUrl: './landing.css',
})
export class LandingComponent {
  categories = [
    { icon: '🍕', name: 'Pizza', count: 24 },
    { icon: '🍔', name: 'Burgers', count: 18 },
    { icon: '🍜', name: 'Massas', count: 15 },
    { icon: '🥗', name: 'Saladas', count: 12 },
    { icon: '🍰', name: 'Sobremesas', count: 20 },
    { icon: '🥤', name: 'Bebidas', count: 16 },
  ];

  features = [
    {
      icon: 'clock',
      title: 'Entrega Rápida',
      desc: 'Receba a sua comida favorita em 30-45 minutos, direto à sua porta.',
      color: '#fbbf24',
    },
    {
      icon: 'check-circle',
      title: 'Pagamento Seguro',
      desc: 'Dinheiro, cartão ou transferência. Todas as transações protegidas.',
      color: '#22c55e',
    },
    {
      icon: 'globe',
      title: 'Multi-Idioma',
      desc: 'Plataforma disponível em Português, Inglês e Francês.',
      color: '#3b82f6',
    },
    {
      icon: 'smartphone',
      title: '100% Responsivo',
      desc: 'Faça o seu pedido no telemóvel, tablet ou computador com facilidade.',
      color: '#a855f7',
    },
  ];

  steps = [
    { step: '01', icon: 'map-pin', title: 'Escolha o seu local', desc: 'Insira o endereço de entrega e veja os restaurantes disponíveis.' },
    { step: '02', icon: 'menu', title: 'Selecione os pratos', desc: 'Navegue pelo cardápio e adicione os seus favoritos ao carrinho.' },
    { step: '03', icon: 'arrow-right', title: 'Receba em casa', desc: 'Confirme o pedido e acompanhe a entrega em tempo real.' },
  ];

  testimonials = [
    { name: 'Ana Silva', role: 'Cliente Regular', text: 'Incrível! Recebi a minha pizza em 25 minutos. A plataforma é muito fácil de usar.', stars: 5, avatar: '👩‍💼' },
    { name: 'Carlos Mendes', role: 'Foodie', text: 'A variedade de pratos é enorme. Uso quase todos os dias para almoço no escritório!', stars: 5, avatar: '👨‍💻' },
    { name: 'Maria João', role: 'Mãe de Família', text: 'Perfeito para encomendar para toda a família. Muito rápido e acessível!', stars: 5, avatar: '👩‍👧' },
  ];

  stats = [
    { value: '50K+', label: 'Clientes Satisfeitos' },
    { value: '200+', label: 'Pratos Disponíveis' },
    { value: '30min', label: 'Tempo Médio de Entrega' },
    { value: '4.9★', label: 'Avaliação Média' },
  ];
}
