import { ApplicationConfig, provideZoneChangeDetection, importProvidersFrom, provideZonelessChangeDetection } from '@angular/core';
import { provideRouter } from '@angular/router';
import { routes } from './app.routes';
import { provideClientHydration, withEventReplay } from '@angular/platform-browser';
import { provideHttpClient, withFetch } from '@angular/common/http';
import { LucideAngularModule, ShoppingCart, User, Menu, X, Star, Clock, MapPin, Phone, Mail, ChevronRight, CheckCircle, ArrowRight, Instagram, Facebook, Twitter, Trash2, Search, Plus, Smartphone, Globe } from 'lucide-angular';

export const appConfig: ApplicationConfig = {
  providers: [
    provideZonelessChangeDetection(), 
    provideRouter(routes), 
    provideClientHydration(withEventReplay()),
    provideHttpClient(withFetch()),
    importProvidersFrom(LucideAngularModule.pick({ 
      ShoppingCart, 
      User, 
      Menu, 
      X, 
      Star, 
      Clock, 
      MapPin, 
      Phone, 
      Mail, 
      ChevronRight, 
      CheckCircle, 
      ArrowRight,
      Instagram,
      Facebook,
      Twitter,
      Trash2,
      Search,
      Plus,
      Smartphone,
      Globe
    }))
  ]
};
