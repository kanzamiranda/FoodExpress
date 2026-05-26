import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NavbarComponent } from '../../../shared/navbar/navbar';
import { I18nService } from '../../../services/i18n.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink, NavbarComponent],
  templateUrl: './login.html',
  styleUrl: './login.css',
})
export class LoginComponent {
  email = '';
  password = '';
  error = '';
  loading = false;

  constructor(public i18n: I18nService, private router: Router) {}

  submit() {
    this.error = '';
    this.loading = true;

    setTimeout(() => {
      this.loading = false;
      if (!this.email || !this.password) {
        this.error = this.i18n.t('fillAllFields');
        return;
      }

      this.router.navigate(['/menu']);
    }, 700);
  }
}
