import {
  Component,
  signal,
  ViewChild,
  ElementRef,
  AfterViewChecked,
} from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { LucideAngularModule } from 'lucide-angular';

type Language = 'pt' | 'en' | 'fr';

interface Message {
  id: number;
  text: string;
  sender: 'bot' | 'user';
  timestamp: Date;
  typing?: boolean;
}

interface QuickReply {
  label: string;
  value: string;
}

@Component({
  selector: 'app-chatbot',
  standalone: true,
  imports: [CommonModule, FormsModule, LucideAngularModule],
  templateUrl: './chatbot.html',
  styleUrl: './chatbot.css',
})
export class ChatbotComponent implements AfterViewChecked {
  @ViewChild('messagesContainer') messagesContainer!: ElementRef;
  @ViewChild('inputField') inputField!: ElementRef;

  isOpen = signal(false);
  isTyping = signal(false);
  userInput = signal('');
  messages = signal<Message[]>([]);
  currentLang = signal<Language>('pt');
  hasUnread = signal(false);
  msgIdCounter = 0;
  private shouldScroll = false;

  quickReplies: Record<Language, QuickReply[]> = {
    pt: [
      { label: '🍕 Ver Menu', value: 'Quero ver o menu' },
      { label: '📦 Meu Pedido', value: 'Como acompanhar meu pedido?' },
      { label: '💳 Pagamento', value: 'Quais métodos de pagamento?' },
      { label: '⏱️ Entrega', value: 'Qual o tempo de entrega?' },
    ],
    en: [
      { label: '🍕 View Menu', value: 'I want to see the menu' },
      { label: '📦 My Order', value: 'How to track my order?' },
      { label: '💳 Payment', value: 'What payment methods?' },
      { label: '⏱️ Delivery', value: 'What is the delivery time?' },
    ],
    fr: [
      { label: '🍕 Voir Menu', value: 'Je veux voir le menu' },
      { label: '📦 Ma Commande', value: 'Comment suivre ma commande?' },
      { label: '💳 Paiement', value: 'Quels modes de paiement?' },
      { label: '⏱️ Livraison', value: 'Quel est le délai de livraison?' },
    ],
  };

  greetings: Record<Language, string> = {
    pt: 'Olá! 👋 Bem-vindo ao FoodExpress! Sou o FoodBot, o seu assistente virtual. Posso responder em 🇵🇹 Português, 🇬🇧 Inglês ou 🇫🇷 Francês. Como posso ajudá-lo hoje?',
    en: 'Hello! 👋 Welcome to FoodExpress! I\'m FoodBot, your virtual assistant. I can respond in 🇵🇹 Portuguese, 🇬🇧 English or 🇫🇷 French. How can I help you today?',
    fr: 'Bonjour! 👋 Bienvenue sur FoodExpress! Je suis FoodBot, votre assistant virtuel. Je peux répondre en 🇵🇹 Portugais, 🇬🇧 Anglais ou 🇫🇷 Français. Comment puis-je vous aider?',
  };

  toggleChat() {
    const opening = !this.isOpen();
    this.isOpen.set(opening);
    this.hasUnread.set(false);
    if (opening && this.messages().length === 0) {
      this.addBotMessage(this.greetings[this.currentLang()]);
    }
  }

  closeChat() {
    this.isOpen.set(false);
  }

  onInputChange(val: string) {
    this.userInput.set(val);
  }

  sendQuickReply(reply: QuickReply) {
    this.sendUserMessage(reply.value);
  }

  sendMessage() {
    const text = this.userInput().trim();
    if (!text) return;
    this.userInput.set('');
    this.sendUserMessage(text);
  }

  private sendUserMessage(text: string) {
    const msg: Message = {
      id: ++this.msgIdCounter,
      text,
      sender: 'user',
      timestamp: new Date(),
    };
    this.messages.update(m => [...m, msg]);
    this.shouldScroll = true;
    this.detectLanguage(text);
    this.simulateTyping(text);
  }

  private detectLanguage(text: string) {
    const lower = text.toLowerCase();
    if (/speak english|change to english|\ben\b|hello|hi\b|how can|what/.test(lower)) {
      this.currentLang.set('en');
    } else if (/parle français|change en français|\bfr\b|bonjour|merci|comment/.test(lower)) {
      this.currentLang.set('fr');
    } else if (/fala português|muda para português|\bpt\b|olá|como|quero|qual|obrigad/.test(lower)) {
      this.currentLang.set('pt');
    }
  }

  private simulateTyping(userText: string) {
    this.isTyping.set(true);
    const delay = 800 + Math.random() * 700;
    setTimeout(() => {
      this.isTyping.set(false);
      const response = this.generateResponse(userText);
      this.addBotMessage(response);
      if (!this.isOpen()) this.hasUnread.set(true);
    }, delay);
  }

  private addBotMessage(text: string) {
    const msg: Message = {
      id: ++this.msgIdCounter,
      text,
      sender: 'bot',
      timestamp: new Date(),
    };
    this.messages.update(m => [...m, msg]);
    this.shouldScroll = true;
  }

  private generateResponse(text: string): string {
    const lower = text.toLowerCase();
    const lang = this.currentLang();

    // Language change commands
    if (/speak english|change to english|\ben\b/.test(lower)) {
      this.currentLang.set('en');
      return 'Great! I\'ll now respond in English. 🇬🇧 How can I help you?';
    }
    if (/parle français|change en français|\bfr\b/.test(lower)) {
      this.currentLang.set('fr');
      return 'Parfait! Je vais maintenant responder en français. 🇫🇷 Comment puis-je vous aider?';
    }
    if (/fala português|muda para português|\bpt\b/.test(lower)) {
      this.currentLang.set('pt');
      return 'Ótimo! Vou responder em Português. 🇵🇹 Como posso ajudá-lo?';
    }

    // Check operating hours
    const hour = new Date().getHours();
    if (hour < 8 || hour >= 23) {
      return {
        pt: 'O FoodExpress está disponível das 08h00 às 23h00. Volte mais tarde! 🍕',
        en: 'FoodExpress is available from 8AM to 11PM. Please come back later! 🍕',
        fr: 'FoodExpress est disponible de 8h00 à 23h00. Revenez plus tard! 🍕',
      }[lang];
    }

    // Menu
    if (/menu|prato|comida|food|dish|plat|manger|eat|comer/.test(lower)) {
      return {
        pt: 'Temos várias categorias no nosso menu: 🍕 Pizzas, 🍔 Hambúrgueres, 🍜 Massas, 🥗 Saladas, 🍰 Sobremesas e muito mais!\n\nPara ver o menu completo, aceda à secção **Menu** na plataforma. Quer que eu o ajude a fazer um pedido?',
        en: 'We have several categories on our menu: 🍕 Pizzas, 🍔 Burgers, 🍜 Pasta, 🥗 Salads, 🍰 Desserts and much more!\n\nTo see the full menu, go to the **Menu** section on the platform. Would you like me to help you place an order?',
        fr: 'Nous avons plusieurs catégories dans notre menu: 🍕 Pizzas, 🍔 Burgers, 🍜 Pâtes, 🥗 Salades, 🍰 Desserts et bien plus!\n\nPour voir le menu completo, aceda à secção **Menu** na plataforma. Quer que eu o ajude a fazer um pedido?',
      }[lang];
    }

    // Default fallback
    return {
      pt: 'Entendi! 😊 Posso ajudá-lo com:\n🍕 Menu e pratos\n📦 Acompanhamento de pedidos\n💳 Pagamentos\n⏱️ Tempo de entrega\n🔐 Conta e login\n🎁 Promoções\n\nSobre o que quer saber mais?',
      en: 'Got it! 😊 I can help you with:\n🍕 Menu and dishes\n📦 Order tracking\n💳 Payments\n⏱️ Delivery time\n🔐 Account and login\n🎁 Promotions\n\nWhat would you like to know more about?',
      fr: 'Compris! 😊 Je peux vous aider avec:\n🍕 Menu et plats\n📦 Suivi des commandes\n💳 Paiements\n⏱️ Délai de livraison\n🔐 Compte et connexion\n🎁 Promotions\n\nSur quoi voulez-vous en savoir plus?',
    }[lang];
  }

  formatTime(date: Date): string {
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }

  formatText(text: string): string {
    return text
      .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
      .replace(/\n/g, '<br>');
  }

  get currentQuickReplies(): QuickReply[] {
    return this.quickReplies[this.currentLang()];
  }

  ngAfterViewChecked() {
    if (this.shouldScroll) {
      this.scrollToBottom();
      this.shouldScroll = false;
    }
  }

  private scrollToBottom() {
    try {
      const el = this.messagesContainer?.nativeElement;
      if (el) el.scrollTop = el.scrollHeight;
    } catch {}
  }

  onKeyDown(event: KeyboardEvent) {
    if (event.key === 'Enter' && !event.shiftKey) {
      event.preventDefault();
      this.sendMessage();
    }
  }
}
