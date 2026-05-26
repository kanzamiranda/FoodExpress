import { Injectable, signal } from '@angular/core';

export type AppLanguage = 'pt' | 'en' | 'fr';

const TRANSLATIONS: Record<AppLanguage, Record<string, string>> = {
  pt: {
    home: 'Início',
    menu: 'Cardápio',
    login: 'Entrar',
    register: 'Cadastrar',
    admin: 'Admin',
    contact: 'Contacto',
    features: 'Funcionalidades',
    quickOrder: 'Pedir Agora',
    howItWorks: 'Como Funciona',
    trustText: '+50.000 clientes satisfeitos',
    categoriesTitle: 'Explore o nosso Cardápio',
    categoriesSubtitle: 'Escolha entre centenas de pratos de diferentes categorias',
    howTitle: '3 passos simples',
    howSubtitle: 'Pedir comida nunca foi tão fácil',
    advantages: 'Por que escolher o FoodExpress?',
    searchPlaceholder: 'Pesquisar pratos...',
    menuTitle: '🍽️ Cardápio',
    menuSubtitle: 'Descubra os nossos pratos frescos e deliciosos',
    foundItems: 'pratos encontrados',
    inCart: 'no carrinho',
    add: 'Adicionar',
    orderConfirmed: 'Pedido Confirmado!',
    orderPreparing: 'O seu pedido está a ser preparado. Entrega em 30-45 minutos!',
    onTheWay: 'A caminho...',
    cartTitle: 'Carrinho',
    cartEmpty: 'Carrinho vazio',
    cartEmptySubtitle: 'Adicione pratos do cardápio!',
    subtotal: 'Subtotal',
    deliveryFee: 'Taxa de entrega',
    total: 'Total',
    confirmOrder: 'Confirmar Pedido',
    clearCart: 'Limpar Carrinho',
    viewCart: 'Ver Carrinho',
    passwordReset: 'Recuperar senha',
    resetSent: 'Link de recuperação enviado para o seu email.',
    resetButton: 'Enviar link',
    fillAllFields: 'Por favor, preencha todos os campos corretamente.',
    loginTitle: 'Entrar na sua conta',
    loginEmail: 'Email',
    loginPassword: 'Senha',
    forgotPassword: 'Recuperar senha',
    loginButton: 'Entrar',
    noAccount: 'Ainda não tem conta?',
    registerTitle: 'Criar nova conta',
    registerName: 'Nome completo',
    registerConfirmPassword: 'Confirmar senha',
    registerButton: 'Cadastrar',
    passwordMismatch: 'As senhas não coincidem.',
    alreadyAccount: 'Já tem conta? Entrar',
    adminTitle: 'Painel Administrativo',
    adminSubtitle: 'Visão geral de pedidos e desempenho',
    darkMode: 'Modo Escuro',
    lightMode: 'Modo Claro',
    googleTranslate: 'Traduzir com Google',
    language: 'Idioma',
    emailSent: 'Verifique seu email para redefinir a senha.',
  },
  en: {
    home: 'Home',
    menu: 'Menu',
    login: 'Login',
    register: 'Register',
    admin: 'Admin',
    contact: 'Contact',
    features: 'Features',
    quickOrder: 'Order Now',
    howItWorks: 'How It Works',
    trustText: 'More than 50,000 happy customers',
    categoriesTitle: 'Explore our Menu',
    categoriesSubtitle: 'Choose from hundreds of dishes across categories',
    howTitle: '3 easy steps',
    howSubtitle: 'Ordering food has never been easier',
    advantages: 'Why choose FoodExpress?',
    searchPlaceholder: 'Search dishes...',
    menuTitle: '🍽️ Menu',
    menuSubtitle: 'Discover our fresh and delicious dishes',
    foundItems: 'dishes found',
    inCart: 'in cart',
    add: 'Add',
    orderConfirmed: 'Order Confirmed!',
    orderPreparing: 'Your order is being prepared. Delivery in 30-45 minutes!',
    onTheWay: 'On the way...',
    cartTitle: 'Cart',
    cartEmpty: 'Your cart is empty',
    cartEmptySubtitle: 'Add dishes from the menu!',
    subtotal: 'Subtotal',
    deliveryFee: 'Delivery fee',
    total: 'Total',
    confirmOrder: 'Confirm Order',
    clearCart: 'Clear Cart',
    viewCart: 'View Cart',
    passwordReset: 'Reset password',
    resetSent: 'Recovery link sent to your email.',
    resetButton: 'Send link',
    fillAllFields: 'Please fill all fields correctly.',
    loginTitle: 'Sign in to your account',
    loginEmail: 'Email',
    loginPassword: 'Password',
    forgotPassword: 'Forgot password',
    loginButton: 'Login',
    noAccount: 'Don’t have an account?',
    registerTitle: 'Create a new account',
    registerName: 'Full name',
    registerConfirmPassword: 'Confirm password',
    registerButton: 'Register',
    passwordMismatch: 'Passwords do not match.',
    alreadyAccount: 'Already have an account? Login',
    adminTitle: 'Admin Dashboard',
    adminSubtitle: 'Overview of orders and performance',
    darkMode: 'Dark Mode',
    lightMode: 'Light Mode',
    googleTranslate: 'Translate with Google',
    language: 'Language',
    emailSent: 'Check your email to reset your password.',
  },
  fr: {
    home: 'Accueil',
    menu: 'Menu',
    login: 'Connexion',
    register: 'Inscription',
    admin: 'Admin',
    contact: 'Contact',
    features: 'Fonctionnalités',
    quickOrder: 'Commander maintenant',
    howItWorks: 'Comment ça marche',
    trustText: 'Plus de 50 000 clients satisfaits',
    categoriesTitle: 'Explorez notre menu',
    categoriesSubtitle: 'Choisissez parmi des centaines de plats',
    howTitle: '3 étapes simples',
    howSubtitle: 'Commander de la nourriture n’a jamais été aussi simple',
    advantages: 'Pourquoi choisir FoodExpress?',
    searchPlaceholder: 'Rechercher des plats...',
    menuTitle: '🍽️ Menu',
    menuSubtitle: 'Découvrez nos plats frais et délicieux',
    foundItems: 'plats trouvés',
    inCart: 'dans le panier',
    add: 'Ajouter',
    orderConfirmed: 'Commande confirmée!',
    orderPreparing: 'Votre commande est en cours de préparation. Livraison en 30-45 minutes!',
    onTheWay: 'En route...',
    cartTitle: 'Panier',
    cartEmpty: 'Panier vide',
    cartEmptySubtitle: 'Ajoutez des plats au menu!',
    subtotal: 'Sous-total',
    deliveryFee: 'Frais de livraison',
    total: 'Total',
    confirmOrder: 'Confirmer la commande',
    clearCart: 'Vider le panier',
    viewCart: 'Voir le panier',
    passwordReset: 'Réinitialiser le mot de passe',
    resetSent: 'Lien de récupération envoyé à votre email.',
    resetButton: 'Envoyer le lien',
    fillAllFields: 'Veuillez remplir tous les champs correctement.',
    loginTitle: 'Connectez-vous à votre compte',
    loginEmail: 'Email',
    loginPassword: 'Mot de passe',
    forgotPassword: 'Mot de passe oublié',
    loginButton: 'Connexion',
    noAccount: 'Vous n’avez pas de compte ?',
    registerTitle: 'Créer un nouveau compte',
    registerName: 'Nom complet',
    registerConfirmPassword: 'Confirmer le mot de passe',
    registerButton: 'S’inscrire',
    passwordMismatch: 'Les mots de passe ne correspondent pas.',
    alreadyAccount: 'Vous avez déjà un compte ? Connexion',
    adminTitle: 'Tableau de bord admin',
    adminSubtitle: 'Vue d’ensemble des commandes et des performances',
    darkMode: 'Mode sombre',
    lightMode: 'Mode clair',
    googleTranslate: 'Traduire avec Google',
    language: 'Langue',
    emailSent: 'Vérifiez votre email pour réinitialiser votre mot de passe.',
  },
};

@Injectable({
  providedIn: 'root',
})
export class I18nService {
  language = signal<AppLanguage>('pt');

  constructor() {
    if (typeof window !== 'undefined') {
      const saved = localStorage.getItem('fe_language') as AppLanguage | null;
      if (saved && ['pt', 'en', 'fr'].includes(saved)) {
        this.language.set(saved);
      }
    }
    this.updateDocumentLang();
  }

  setLanguage(language: AppLanguage) {
    this.language.set(language);
    if (typeof window !== 'undefined') {
      localStorage.setItem('fe_language', language);
    }
    this.updateDocumentLang();
  }

  t(key: string): string {
    return TRANSLATIONS[this.language()][key] ?? key;
  }

  googleTranslateUrl(): string {
    const lang = this.language();
    const target = lang === 'fr' ? 'fr' : lang === 'en' ? 'en' : 'pt';
    const url = typeof window !== 'undefined' ? window.location.href : '/';
    return `https://translate.google.com/translate?sl=auto&tl=${target}&u=${encodeURIComponent(url)}`;
  }

  get languages() {
    return [
      { code: 'pt', label: 'Português' },
      { code: 'en', label: 'English' },
      { code: 'fr', label: 'Français' },
    ];
  }

  private updateDocumentLang() {
    if (typeof document !== 'undefined') {
      document.documentElement.lang = this.language();
    }
  }
}
