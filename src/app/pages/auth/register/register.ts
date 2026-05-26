import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NavbarComponent } from '../../../shared/navbar/navbar';
import { I18nService } from '../../../services/i18n.service';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink, NavbarComponent],
  templateUrl: './register.html',
  styleUrl: './register.css',
})
export class RegisterComponent {
  name = '';
  email = '';
  password = '';
  confirmPassword = '';
  error = '';
  loading = false;

  constructor(public i18n: I18nService, private router: Router) {}

  submit() {
    this.error = '';
    if (!this.name || !this.email || !this.password || !this.confirmPassword) {
      this.error = this.i18n.t('fillAllFields');
      return;
    }

    if (this.password !== this.confirmPassword) {
      this.error = this.i18n.t('passwordMismatch');
      return;
    }

    this.loading = true;
    setTimeout(() => {
      this.loading = false;
      this.router.navigate(['/login']);
    }, 700);
  }
}
