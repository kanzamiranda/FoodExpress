import { Injectable, signal } from '@angular/core';

export type AppLanguage = 'pt' | 'en' | 'fr';

const TRANSLATIONS: Record<AppLanguage, Record<string, string>> = {
  pt: {
    // === Navbar ===
    home: 'Início',
    menu: 'Cardápio',
    login: 'Entrar',
    register: 'Cadastrar',
    admin: 'Admin',
    contact: 'Contacto',
    features: 'Funcionalidades',
    quickOrder: 'Pedir Agora',
    howItWorks: 'Como Funciona',
    darkMode: 'Modo Escuro',
    lightMode: 'Modo Claro',
    googleTranslate: 'Traduzir com Google',
    language: 'Idioma',

    // === Hero ===
    heroBadge: 'Entrega rápida e grátis acima de 15.000 KZ!',
    heroTitle: 'A sua comida favorita, quente e à sua porta',
    heroSubtitle: 'Peça pratos deliciosos dos melhores restaurantes locais e acompanhe a entrega em tempo real.',
    trustText: 'clientes satisfeitos',
    deliveryIn: 'Entrega em',

    // === Stats ===
    statClients: 'Clientes Satisfeitos',
    statDishes: 'Pratos Disponíveis',
    statDelivery: 'Tempo Médio de Entrega',
    statRating: 'Avaliação Média',

    // === Categories ===
    categories: 'Categorias',
    categoriesTitle: 'Explore o nosso Cardápio',
    categoriesSubtitle: 'Escolha entre centenas de pratos de diferentes categorias',
    dishesLabel: 'pratos',

    // === How It Works ===
    howTitle: '3 passos simples',
    howSubtitle: 'Pedir comida nunca foi tão fácil',
    step1Title: 'Escolha o seu local',
    step1Desc: 'Insira o endereço de entrega e veja os restaurantes disponíveis.',
    step2Title: 'Selecione os pratos',
    step2Desc: 'Navegue pelo cardápio e adicione os seus favoritos ao carrinho.',
    step3Title: 'Receba em casa',
    step3Desc: 'Confirme o pedido e acompanhe a entrega em tempo real.',

    // === Features ===
    advantages: 'Por que escolher o FoodExpress?',
    featureDeliveryTitle: 'Entrega Rápida',
    featureDeliveryDesc: 'Receba a sua comida favorita em 30-45 minutos, direto à sua porta.',
    featurePaymentTitle: 'Pagamento Seguro',
    featurePaymentDesc: 'Dinheiro, cartão ou transferência. Todas as transações protegidas.',
    featureLangTitle: 'Multi-Idioma',
    featureLangDesc: 'Plataforma disponível em Português, Inglês e Francês.',
    featureResponsiveTitle: '100% Responsivo',
    featureResponsiveDesc: 'Faça o seu pedido no telemóvel, tablet ou computador com facilidade.',

    // === Testimonials ===
    testimonials: 'Testemunhos',
    testimonialsTitle: 'O que dizem os nossos clientes',
    testimonialRole1: 'Cliente Regular',
    testimonialText1: 'Incrível! Recebi a minha pizza em 25 minutos. A plataforma é muito fácil de usar.',
    testimonialRole2: 'Foodie',
    testimonialText2: 'A variedade de pratos é enorme. Uso quase todos os dias para almoço no escritório!',
    testimonialRole3: 'Mãe de Família',
    testimonialText3: 'Perfeito para encomendar para toda a família. Muito rápido e acessível!',

    // === CTA ===
    ctaTitle: 'Pronto para pedir?',
    ctaSubtitle: 'Registe-se agora e ganhe 15% de desconto na sua primeira encomenda!',
    viewMenu: 'Ver Cardápio',
    createFreeAccount: 'Criar Conta Grátis',

    // === Footer ===
    footerTagline: 'A sua plataforma de delivery favorita. Rápido, fácil e delicioso.',
    platform: 'Plataforma',
    support: 'Suporte',
    openingHours: '08h00 – 23h00',
    allRightsReserved: 'Todos os direitos reservados.',
    promotions: 'Promoções',
    mobileApp: 'App Mobile',
    helpCenter: 'Central de Ajuda',
    complaints: 'Reclamações',
    privacyPolicy: 'Política de Privacidade',
    hostedOn: 'Hospedado no Render · BD: Neon PostgreSQL',

    // === Menu Page ===
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

    // === Auth ===
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
    emailSent: 'Verifique seu email para redefinir a senha.',

    // === Admin ===
    adminTitle: 'Painel Administrativo',
    adminSubtitle: 'Visão geral de pedidos e desempenho',
  },
  en: {
    // === Navbar ===
    home: 'Home',
    menu: 'Menu',
    login: 'Login',
    register: 'Register',
    admin: 'Admin',
    contact: 'Contact',
    features: 'Features',
    quickOrder: 'Order Now',
    howItWorks: 'How It Works',
    darkMode: 'Dark Mode',
    lightMode: 'Light Mode',
    googleTranslate: 'Translate with Google',
    language: 'Language',

    // === Hero ===
    heroBadge: 'Fast and free delivery above 15,000 KZ!',
    heroTitle: 'Your favorite food, hot and at your door',
    heroSubtitle: 'Order delicious dishes from the best local restaurants and track the delivery in real-time.',
    trustText: 'happy customers',
    deliveryIn: 'Delivery in',

    // === Stats ===
    statClients: 'Happy Customers',
    statDishes: 'Available Dishes',
    statDelivery: 'Average Delivery Time',
    statRating: 'Average Rating',

    // === Categories ===
    categories: 'Categories',
    categoriesTitle: 'Explore our Menu',
    categoriesSubtitle: 'Choose from hundreds of dishes across categories',
    dishesLabel: 'dishes',

    // === How It Works ===
    howTitle: '3 easy steps',
    howSubtitle: 'Ordering food has never been easier',
    step1Title: 'Choose your location',
    step1Desc: 'Enter your delivery address and see available restaurants.',
    step2Title: 'Select your dishes',
    step2Desc: 'Browse the menu and add your favorites to the cart.',
    step3Title: 'Receive at home',
    step3Desc: 'Confirm the order and track the delivery in real-time.',

    // === Features ===
    advantages: 'Why choose FoodExpress?',
    featureDeliveryTitle: 'Fast Delivery',
    featureDeliveryDesc: 'Receive your favorite food in 30-45 minutes, right to your door.',
    featurePaymentTitle: 'Secure Payment',
    featurePaymentDesc: 'Cash, card or transfer. All transactions protected.',
    featureLangTitle: 'Multi-Language',
    featureLangDesc: 'Platform available in Portuguese, English and French.',
    featureResponsiveTitle: '100% Responsive',
    featureResponsiveDesc: 'Place your order on mobile, tablet or computer with ease.',

    // === Testimonials ===
    testimonials: 'Testimonials',
    testimonialsTitle: 'What our customers say',
    testimonialRole1: 'Regular Customer',
    testimonialText1: 'Amazing! I received my pizza in 25 minutes. The platform is very easy to use.',
    testimonialRole2: 'Foodie',
    testimonialText2: 'The variety of dishes is huge. I use it almost every day for lunch at the office!',
    testimonialRole3: 'Family Mom',
    testimonialText3: 'Perfect for ordering for the whole family. Very fast and affordable!',

    // === CTA ===
    ctaTitle: 'Ready to order?',
    ctaSubtitle: 'Register now and get 15% off on your first order!',
    viewMenu: 'View Menu',
    createFreeAccount: 'Create Free Account',

    // === Footer ===
    footerTagline: 'Your favorite delivery platform. Fast, easy, and delicious.',
    platform: 'Platform',
    support: 'Support',
    openingHours: '08:00 AM – 11:00 PM',
    allRightsReserved: 'All rights reserved.',
    promotions: 'Promotions',
    mobileApp: 'Mobile App',
    helpCenter: 'Help Center',
    complaints: 'Complaints',
    privacyPolicy: 'Privacy Policy',
    hostedOn: 'Hosted on Render · DB: Neon PostgreSQL',

    // === Menu Page ===
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

    // === Auth ===
    passwordReset: 'Reset password',
    resetSent: 'Recovery link sent to your email.',
    resetButton: 'Send link',
    fillAllFields: 'Please fill all fields correctly.',
    loginTitle: 'Sign in to your account',
    loginEmail: 'Email',
    loginPassword: 'Password',
    forgotPassword: 'Forgot password',
    loginButton: 'Login',
    noAccount: "Don't have an account?",
    registerTitle: 'Create a new account',
    registerName: 'Full name',
    registerConfirmPassword: 'Confirm password',
    registerButton: 'Register',
    passwordMismatch: 'Passwords do not match.',
    alreadyAccount: 'Already have an account? Login',
    emailSent: 'Check your email to reset your password.',

    // === Admin ===
    adminTitle: 'Admin Dashboard',
    adminSubtitle: 'Overview of orders and performance',
  },
  fr: {
    // === Navbar ===
    home: 'Accueil',
    menu: 'Menu',
    login: 'Connexion',
    register: 'Inscription',
    admin: 'Admin',
    contact: 'Contact',
    features: 'Fonctionnalités',
    quickOrder: 'Commander maintenant',
    howItWorks: 'Comment ça marche',
    darkMode: 'Mode sombre',
    lightMode: 'Mode clair',
    googleTranslate: 'Traduire avec Google',
    language: 'Langue',

    // === Hero ===
    heroBadge: 'Livraison rapide et gratuite à partir de 15.000 KZ !',
    heroTitle: 'Votre nourriture préférée, chaude et à votre porte',
    heroSubtitle: 'Commandez de délicieux plats dans les meilleurs restaurants locaux et suivez la livraison en temps réel.',
    trustText: 'clients satisfaits',
    deliveryIn: 'Livraison en',

    // === Stats ===
    statClients: 'Clients Satisfaits',
    statDishes: 'Plats Disponibles',
    statDelivery: 'Temps Moyen de Livraison',
    statRating: 'Note Moyenne',

    // === Categories ===
    categories: 'Catégories',
    categoriesTitle: 'Explorez notre menu',
    categoriesSubtitle: 'Choisissez parmi des centaines de plats',
    dishesLabel: 'plats',

    // === How It Works ===
    howTitle: '3 étapes simples',
    howSubtitle: "Commander de la nourriture n'a jamais été aussi simple",
    step1Title: 'Choisissez votre lieu',
    step1Desc: "Entrez l'adresse de livraison et voyez les restaurants disponibles.",
    step2Title: 'Sélectionnez les plats',
    step2Desc: 'Parcourez le menu et ajoutez vos favoris au panier.',
    step3Title: 'Recevez chez vous',
    step3Desc: 'Confirmez la commande et suivez la livraison en temps réel.',

    // === Features ===
    advantages: 'Pourquoi choisir FoodExpress ?',
    featureDeliveryTitle: 'Livraison Rapide',
    featureDeliveryDesc: 'Recevez votre nourriture préférée en 30-45 minutes, directement chez vous.',
    featurePaymentTitle: 'Paiement Sécurisé',
    featurePaymentDesc: 'Espèces, carte ou virement. Toutes les transactions protégées.',
    featureLangTitle: 'Multi-Langue',
    featureLangDesc: 'Plateforme disponible en Portugais, Anglais et Français.',
    featureResponsiveTitle: '100% Responsive',
    featureResponsiveDesc: 'Passez votre commande sur mobile, tablette ou ordinateur facilement.',

    // === Testimonials ===
    testimonials: 'Témoignages',
    testimonialsTitle: 'Ce que disent nos clients',
    testimonialRole1: 'Client Régulier',
    testimonialText1: "Incroyable ! J'ai reçu ma pizza en 25 minutes. La plateforme est très facile à utiliser.",
    testimonialRole2: 'Foodie',
    testimonialText2: "La variété de plats est énorme. Je l'utilise presque tous les jours pour le déjeuner au bureau !",
    testimonialRole3: 'Mère de Famille',
    testimonialText3: 'Parfait pour commander pour toute la famille. Très rapide et abordable !',

    // === CTA ===
    ctaTitle: 'Prêt à commander ?',
    ctaSubtitle: 'Inscrivez-vous maintenant et bénéficiez de 15% de réduction sur votre première commande !',
    viewMenu: 'Voir le menu',
    createFreeAccount: 'Créer un compte gratuit',

    // === Footer ===
    footerTagline: 'Votre plateforme de livraison préférée. Rapide, facile et délicieux.',
    platform: 'Plateforme',
    support: 'Support',
    openingHours: '08h00 – 23h00',
    allRightsReserved: 'Tous droits réservés.',
    promotions: 'Promotions',
    mobileApp: 'App Mobile',
    helpCenter: "Centre d'Aide",
    complaints: 'Réclamations',
    privacyPolicy: 'Politique de Confidentialité',
    hostedOn: 'Hébergé sur Render · BD : Neon PostgreSQL',

    // === Menu Page ===
    searchPlaceholder: 'Rechercher des plats...',
    menuTitle: '🍽️ Menu',
    menuSubtitle: 'Découvrez nos plats frais et délicieux',
    foundItems: 'plats trouvés',
    inCart: 'dans le panier',
    add: 'Ajouter',
    orderConfirmed: 'Commande confirmée !',
    orderPreparing: 'Votre commande est en cours de préparation. Livraison en 30-45 minutes !',
    onTheWay: 'En route...',
    cartTitle: 'Panier',
    cartEmpty: 'Panier vide',
    cartEmptySubtitle: 'Ajoutez des plats au menu !',
    subtotal: 'Sous-total',
    deliveryFee: 'Frais de livraison',
    total: 'Total',
    confirmOrder: 'Confirmer la commande',
    clearCart: 'Vider le panier',
    viewCart: 'Voir le panier',

    // === Auth ===
    passwordReset: 'Réinitialiser le mot de passe',
    resetSent: 'Lien de récupération envoyé à votre email.',
    resetButton: 'Envoyer le lien',
    fillAllFields: 'Veuillez remplir tous les champs correctement.',
    loginTitle: 'Connectez-vous à votre compte',
    loginEmail: 'Email',
    loginPassword: 'Mot de passe',
    forgotPassword: 'Mot de passe oublié',
    loginButton: 'Connexion',
    noAccount: "Vous n'avez pas de compte ?",
    registerTitle: 'Créer un nouveau compte',
    registerName: 'Nom complet',
    registerConfirmPassword: 'Confirmer le mot de passe',
    registerButton: "S'inscrire",
    passwordMismatch: 'Les mots de passe ne correspondent pas.',
    alreadyAccount: 'Vous avez déjà un compte ? Connexion',
    emailSent: 'Vérifiez votre email pour réinitialiser votre mot de passe.',

    // === Admin ===
    adminTitle: 'Tableau de bord admin',
    adminSubtitle: "Vue d'ensemble des commandes et des performances",
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
