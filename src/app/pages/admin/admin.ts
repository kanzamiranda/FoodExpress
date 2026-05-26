import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NavbarComponent } from '../../shared/navbar/navbar';
import { LucideAngularModule } from 'lucide-angular';
import { I18nService } from '../../services/i18n.service';

@Component({
  selector: 'app-admin',
  standalone: true,
  imports: [CommonModule, NavbarComponent, LucideAngularModule],
  templateUrl: './admin.html',
  styleUrl: './admin.css',
})
export class AdminComponent {
  stats = [
    { value: '24', label: 'Novos pedidos hoje' },
    { value: '4.9★', label: 'Avaliação média' },
    { value: '320', label: 'Pedidos ativos' },
    { value: '18K€', label: 'Faturação semanal' },
  ];

  recentOrders = [
    { id: 1203, restaurant: 'Pizzaria Bella', client: 'Ana S.', status: 'A preparar', total: '€24,90' },
    { id: 1202, restaurant: 'Burger House', client: 'Carlos M.', status: 'A caminho', total: '€18,30' },
    { id: 1201, restaurant: 'Sushiland', client: 'Maria J.', status: 'Entregue', total: '€32,70' },
  ];

  constructor(public i18n: I18nService) {}
}
