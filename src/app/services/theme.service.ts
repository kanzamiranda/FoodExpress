import { Injectable, signal } from '@angular/core';

@Injectable({
  providedIn: 'root',
})
export class ThemeService {
  isDark = signal(false);

  constructor() {
    let savedValue: string | null = null;
    if (typeof window !== 'undefined') {
      savedValue = localStorage.getItem('fe_theme');
    }
    const prefersDark = typeof window !== 'undefined' && window.matchMedia?.('(prefers-color-scheme: dark)').matches;
    this.setTheme(savedValue === 'dark' || (!savedValue && prefersDark));
  }

  toggleTheme() {
    this.setTheme(!this.isDark());
  }

  setTheme(value: boolean) {
    this.isDark.set(value);
    if (typeof document !== 'undefined') {
      document.documentElement.classList.toggle('dark', value);
    }
    if (typeof window !== 'undefined') {
      localStorage.setItem('fe_theme', value ? 'dark' : 'light');
    }
  }
}
