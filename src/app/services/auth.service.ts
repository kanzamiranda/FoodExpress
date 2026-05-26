import { Injectable, signal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, tap, catchError, throwError } from 'rxjs';
import { environment } from '../../environments/environment';

export interface User {
  id: string;
  name: string;
  email: string;
  role: 'cliente' | 'restaurante' | 'admin';
}

export interface AuthResponse {
  success: boolean;
  message?: string;
  data: {
    user: User;
    access_token: string;
    refresh_token: string;
  };
}

@Injectable({
  providedIn: 'root',
})
export class AuthService {
  private apiUrl = environment.apiUrl;
  
  currentUser = signal<User | null>(null);
  token = signal<string | null>(null);

  constructor(private http: HttpClient) {
    if (typeof window !== 'undefined') {
      const savedUser = localStorage.getItem('fe_user');
      const savedToken = localStorage.getItem('fe_token');
      
      if (savedUser) {
        try {
          this.currentUser.set(JSON.parse(savedUser));
        } catch {
          localStorage.removeItem('fe_user');
        }
      }
      
      if (savedToken) {
        this.token.set(savedToken);
      }
    }
  }

  isLoggedIn(): boolean {
    return this.token() !== null;
  }

  isAdmin(): boolean {
    return this.currentUser()?.role === 'admin';
  }

  register(name: string, email: string, password: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/auth/register`, {
      name,
      email,
      password,
    });
  }

  login(email: string, password: string): Observable<AuthResponse> {
    return this.http.post<AuthResponse>(`${this.apiUrl}/auth/login`, {
      email,
      password,
    }).pipe(
      tap((res) => {
        if (res.success && res.data) {
          const { user, access_token } = res.data;
          this.currentUser.set(user);
          this.token.set(access_token);
          
          if (typeof window !== 'undefined') {
            localStorage.setItem('fe_user', JSON.stringify(user));
            localStorage.setItem('fe_token', access_token);
            localStorage.setItem('fe_refresh_token', res.data.refresh_token);
          }
        }
      })
    );
  }

  logout(): Observable<any> {
    return this.http.post(`${this.apiUrl}/auth/logout`, {}).pipe(
      tap(() => {
        this.clearSession();
      }),
      catchError((err) => {
        this.clearSession();
        return throwError(() => err);
      })
    );
  }

  forgotPassword(email: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/auth/forgot-password`, { email });
  }

  private clearSession() {
    this.currentUser.set(null);
    this.token.set(null);
    if (typeof window !== 'undefined') {
      localStorage.removeItem('fe_user');
      localStorage.removeItem('fe_token');
      localStorage.removeItem('fe_refresh_token');
    }
  }
}
