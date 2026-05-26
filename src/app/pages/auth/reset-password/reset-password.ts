import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { NavbarComponent } from '../../../shared/navbar/navbar';
import { I18nService } from '../../../services/i18n.service';
import { AuthService } from '../../../services/auth.service';

@Component({
  selector: 'app-reset-password',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterLink, NavbarComponent],
  templateUrl: './reset-password.html',
  styleUrl: './reset-password.css',
})
export class ResetPasswordComponent {
  resetForm: FormGroup;
  success = false;
  error = '';
  loading = false;

  constructor(
    private fb: FormBuilder,
    public i18n: I18nService,
    private auth: AuthService
  ) {
    this.resetForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
    });
  }

  submit() {
    if (this.resetForm.invalid) {
      this.resetForm.markAllAsTouched();
      return;
    }

    const { email } = this.resetForm.value;
    this.error = '';
    this.loading = true;

    this.auth.forgotPassword(email).subscribe({
      next: (res) => {
        this.loading = false;
        this.success = true;
      },
      error: (err) => {
        this.loading = false;
        this.error = err.error?.message || 'Erro ao enviar link de recuperação. Verifique o e-mail.';
      }
    });
  }
}
