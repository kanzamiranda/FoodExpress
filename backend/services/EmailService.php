<?php
// ============================================================
// FoodExpress — Email Service (Brevo Transactional API v3)
// ============================================================
declare(strict_types=1);

class EmailService
{
    private string $apiKey;
    private string $fromEmail;
    private string $fromName;
    private string $frontendUrl;

    public function __construct()
    {
        $this->apiKey      = getenv('BREVO_API_KEY')    ?: '';
        $this->fromEmail   = getenv('BREVO_FROM_EMAIL') ?: 'noreply@foodexpress.com';
        $this->fromName    = getenv('BREVO_FROM_NAME')  ?: 'FoodExpress';
        $this->frontendUrl = getenv('FRONTEND_URL')     ?: 'http://localhost:4200';
    }

    // ─────────────────────────────────────────────────────────
    // PÚBLICA: Recuperação de Senha
    // ─────────────────────────────────────────────────────────
    public function sendPasswordReset(string $toEmail, string $token): bool
    {
        $resetLink = "{$this->frontendUrl}/reset-password?token={$token}";

        $htmlContent = $this->renderTemplate('password_reset', [
            'reset_link'  => $resetLink,
            'expiry_time' => '1 hora',
        ]);

        return $this->send(
            toEmail: $toEmail,
            subject: '🔑 Recuperação de senha — FoodExpress',
            html:    $htmlContent
        );
    }

    // ─────────────────────────────────────────────────────────
    // PÚBLICA: Boas-Vindas após Registo
    // ─────────────────────────────────────────────────────────
    public function sendWelcome(string $toEmail, string $nome): bool
    {
        $htmlContent = $this->renderTemplate('welcome', [
            'nome'         => htmlspecialchars($nome),
            'login_link'   => "{$this->frontendUrl}/login",
            'frontend_url' => $this->frontendUrl,
        ]);

        return $this->send(
            toEmail: $toEmail,
            subject: '🍔 Bem-vindo ao FoodExpress!',
            html:    $htmlContent
        );
    }

    // ─────────────────────────────────────────────────────────
    // PÚBLICA: Confirmação de Pedido
    // ─────────────────────────────────────────────────────────
    public function sendOrderConfirmation(string $toEmail, string $nome, string|int $orderId, float $total): bool
    {
        $orderLink   = "{$this->frontendUrl}/orders/{$orderId}";
        $htmlContent = $this->renderTemplate('order_confirmation', [
            'nome'       => htmlspecialchars($nome),
            'order_id'   => $orderId,
            'total'      => number_format($total, 2, ',', '.'),
            'order_link' => $orderLink,
        ]);

        return $this->send(
            toEmail: $toEmail,
            subject: "✅ Pedido #{$orderId} confirmado — FoodExpress",
            html:    $htmlContent
        );
    }

    // ─────────────────────────────────────────────────────────
    // PRIVADO: Envio via Brevo API v3
    // ─────────────────────────────────────────────────────────
    private function send(string $toEmail, string $subject, string $html): bool
    {
        if (empty($this->apiKey)) {
            error_log('[EmailService] BREVO_API_KEY não configurada.');
            return false;
        }

        $payload = json_encode([
            'sender'      => [
                'name'  => $this->fromName,
                'email' => $this->fromEmail,
            ],
            'to'          => [['email' => $toEmail]],
            'subject'     => $subject,
            'htmlContent' => $html,
        ]);

        $ch = curl_init('https://api.brevo.com/v3/smtp/email');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'accept: application/json',
                'api-key: ' . $this->apiKey,
                'content-type: application/json',
            ],
            CURLOPT_TIMEOUT        => 10,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr) {
            error_log("[EmailService] cURL error: {$curlErr}");
            return false;
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            error_log("[EmailService] Brevo respondeu HTTP {$httpCode}: {$response}");
            return false;
        }

        return true;
    }

    // ─────────────────────────────────────────────────────────
    // PRIVADO: Templates HTML inline
    // ─────────────────────────────────────────────────────────
    private function renderTemplate(string $template, array $vars): string
    {
        return match ($template) {
            'welcome'            => $this->tplWelcome($vars),
            'password_reset'     => $this->tplPasswordReset($vars),
            'order_confirmation' => $this->tplOrderConfirmation($vars),
            default              => '<p>Mensagem do FoodExpress.</p>',
        };
    }

    // ── Template: Boas-Vindas ──────────────────────────────────
    private function tplWelcome(array $v): string
    {
        $nome       = $v['nome'];
        $loginLink  = $v['login_link'];

        return <<<HTML
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bem-vindo ao FoodExpress</title>
</head>
<body style="margin:0;padding:0;background:#f5f5f5;font-family:'Segoe UI',Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:40px 0;">
    <tr><td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
        <!-- Header -->
        <tr>
          <td style="background:linear-gradient(135deg,#FF6B35 0%,#FF8C42 100%);padding:40px 40px 30px;text-align:center;">
            <div style="font-size:48px;margin-bottom:8px;">🍔</div>
            <h1 style="color:#ffffff;margin:0;font-size:28px;font-weight:700;letter-spacing:-0.5px;">FoodExpress</h1>
            <p style="color:rgba(255,255,255,0.85);margin:8px 0 0;font-size:15px;">A tua comida favorita, mais rápida</p>
          </td>
        </tr>
        <!-- Body -->
        <tr>
          <td style="padding:40px;">
            <h2 style="color:#1a1a1a;font-size:22px;margin:0 0 16px;font-weight:600;">Olá, {$nome}! 👋</h2>
            <p style="color:#555;font-size:15px;line-height:1.6;margin:0 0 20px;">
              A tua conta foi criada com sucesso! Estamos muito felizes em tê-lo connosco.
            </p>
            <p style="color:#555;font-size:15px;line-height:1.6;margin:0 0 32px;">
              Com o FoodExpress, podes encomendar os teus pratos favoritos com apenas alguns cliques e acompanhar a tua entrega em tempo real.
            </p>
            <!-- CTA Button -->
            <table cellpadding="0" cellspacing="0" style="margin:0 auto 32px;">
              <tr>
                <td style="background:linear-gradient(135deg,#FF6B35,#FF8C42);border-radius:50px;padding:16px 40px;">
                  <a href="{$loginLink}" style="color:#ffffff;font-size:16px;font-weight:700;text-decoration:none;display:block;">
                    Começar a pedir 🚀
                  </a>
                </td>
              </tr>
            </table>
            <!-- Features -->
            <table width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid #f0f0f0;padding-top:28px;">
              <tr>
                <td width="33%" style="text-align:center;padding:16px 8px;">
                  <div style="font-size:28px;margin-bottom:8px;">⚡</div>
                  <p style="color:#1a1a1a;font-size:13px;font-weight:600;margin:0 0 4px;">Entrega Rápida</p>
                  <p style="color:#888;font-size:12px;margin:0;">Em menos de 30 min</p>
                </td>
                <td width="33%" style="text-align:center;padding:16px 8px;">
                  <div style="font-size:28px;margin-bottom:8px;">🔍</div>
                  <p style="color:#1a1a1a;font-size:13px;font-weight:600;margin:0 0 4px;">Rastreio em Tempo Real</p>
                  <p style="color:#888;font-size:12px;margin:0;">Acompanha o teu pedido</p>
                </td>
                <td width="33%" style="text-align:center;padding:16px 8px;">
                  <div style="font-size:28px;margin-bottom:8px;">⭐</div>
                  <p style="color:#1a1a1a;font-size:13px;font-weight:600;margin:0 0 4px;">Os Melhores Restaurantes</p>
                  <p style="color:#888;font-size:12px;margin:0;">Qualidade garantida</p>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <!-- Footer -->
        <tr>
          <td style="background:#fafafa;border-top:1px solid #f0f0f0;padding:24px 40px;text-align:center;">
            <p style="color:#aaa;font-size:12px;margin:0;">
              © 2026 FoodExpress. Todos os direitos reservados.<br>
              Se não criaste esta conta, ignora este email.
            </p>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
    }

    // ── Template: Recuperação de Senha ────────────────────────
    private function tplPasswordReset(array $v): string
    {
        $resetLink  = $v['reset_link'];
        $expiryTime = $v['expiry_time'];

        return <<<HTML
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recuperação de Senha — FoodExpress</title>
</head>
<body style="margin:0;padding:0;background:#f5f5f5;font-family:'Segoe UI',Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:40px 0;">
    <tr><td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
        <!-- Header -->
        <tr>
          <td style="background:linear-gradient(135deg,#FF6B35 0%,#FF8C42 100%);padding:40px;text-align:center;">
            <div style="font-size:48px;margin-bottom:8px;">🔐</div>
            <h1 style="color:#ffffff;margin:0;font-size:24px;font-weight:700;">Recuperação de Senha</h1>
          </td>
        </tr>
        <!-- Body -->
        <tr>
          <td style="padding:40px;">
            <p style="color:#555;font-size:15px;line-height:1.6;margin:0 0 20px;">
              Recebemos um pedido para redefinir a senha da tua conta FoodExpress.
            </p>
            <p style="color:#555;font-size:15px;line-height:1.6;margin:0 0 32px;">
              Clica no botão abaixo para criar uma nova senha. O link é válido por <strong>{$expiryTime}</strong>.
            </p>
            <!-- CTA Button -->
            <table cellpadding="0" cellspacing="0" style="margin:0 auto 32px;">
              <tr>
                <td style="background:linear-gradient(135deg,#FF6B35,#FF8C42);border-radius:50px;padding:16px 40px;">
                  <a href="{$resetLink}" style="color:#ffffff;font-size:16px;font-weight:700;text-decoration:none;display:block;">
                    Redefinir Senha 🔑
                  </a>
                </td>
              </tr>
            </table>
            <!-- Warning -->
            <div style="background:#fff8f5;border:1px solid #FFD5C4;border-radius:10px;padding:16px 20px;margin-bottom:24px;">
              <p style="color:#c0392b;font-size:13px;margin:0;line-height:1.5;">
                ⚠️ <strong>Não pediste esta recuperação?</strong><br>
                Ignora este email. A tua senha permanece inalterada.
              </p>
            </div>
            <!-- Link fallback -->
            <p style="color:#aaa;font-size:12px;margin:0;word-break:break-all;">
              Ou copia este link: <a href="{$resetLink}" style="color:#FF6B35;">{$resetLink}</a>
            </p>
          </td>
        </tr>
        <!-- Footer -->
        <tr>
          <td style="background:#fafafa;border-top:1px solid #f0f0f0;padding:24px 40px;text-align:center;">
            <p style="color:#aaa;font-size:12px;margin:0;">
              © 2026 FoodExpress. Todos os direitos reservados.
            </p>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
    }

    // ── Template: Confirmação de Pedido ───────────────────────
    private function tplOrderConfirmation(array $v): string
    {
        $nome      = $v['nome'];
        $orderId   = $v['order_id'];
        $total     = $v['total'];
        $orderLink = $v['order_link'];

        return <<<HTML
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pedido Confirmado — FoodExpress</title>
</head>
<body style="margin:0;padding:0;background:#f5f5f5;font-family:'Segoe UI',Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:40px 0;">
    <tr><td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
        <!-- Header -->
        <tr>
          <td style="background:linear-gradient(135deg,#FF6B35 0%,#FF8C42 100%);padding:40px;text-align:center;">
            <div style="font-size:48px;margin-bottom:8px;">✅</div>
            <h1 style="color:#ffffff;margin:0;font-size:24px;font-weight:700;">Pedido Confirmado!</h1>
          </td>
        </tr>
        <!-- Body -->
        <tr>
          <td style="padding:40px;">
            <h2 style="color:#1a1a1a;font-size:20px;margin:0 0 16px;">Olá, {$nome}!</h2>
            <p style="color:#555;font-size:15px;line-height:1.6;margin:0 0 24px;">
              O teu pedido foi recebido e está a ser preparado. 🍳
            </p>
            <!-- Order Summary -->
            <div style="background:#f9f9f9;border-radius:12px;padding:24px;margin-bottom:28px;">
              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td style="color:#888;font-size:13px;padding:6px 0;">Número do Pedido</td>
                  <td style="color:#1a1a1a;font-size:14px;font-weight:700;text-align:right;padding:6px 0;">#{$orderId}</td>
                </tr>
                <tr>
                  <td colspan="2" style="border-top:1px solid #eee;padding:8px 0;"></td>
                </tr>
                <tr>
                  <td style="color:#888;font-size:13px;padding:6px 0;">Total</td>
                  <td style="color:#FF6B35;font-size:18px;font-weight:700;text-align:right;padding:6px 0;">€{$total}</td>
                </tr>
              </table>
            </div>
            <!-- Status Steps -->
            <p style="color:#1a1a1a;font-size:14px;font-weight:600;margin:0 0 16px;">Estado do Pedido:</p>
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
              <tr>
                <td width="24px" style="vertical-align:top;padding-right:12px;">
                  <div style="width:24px;height:24px;background:#FF6B35;border-radius:50%;text-align:center;line-height:24px;color:#fff;font-size:12px;font-weight:700;">✓</div>
                </td>
                <td style="vertical-align:top;padding-bottom:16px;">
                  <p style="color:#1a1a1a;font-size:14px;font-weight:600;margin:0 0 2px;">Pedido Recebido</p>
                  <p style="color:#888;font-size:12px;margin:0;">O restaurante confirmou o teu pedido</p>
                </td>
              </tr>
              <tr>
                <td width="24px" style="vertical-align:top;padding-right:12px;">
                  <div style="width:24px;height:24px;background:#f0f0f0;border-radius:50%;text-align:center;line-height:24px;color:#aaa;font-size:12px;">2</div>
                </td>
                <td style="vertical-align:top;padding-bottom:16px;">
                  <p style="color:#aaa;font-size:14px;margin:0 0 2px;">A Preparar</p>
                  <p style="color:#ccc;font-size:12px;margin:0;">O restaurante está a preparar</p>
                </td>
              </tr>
              <tr>
                <td width="24px" style="vertical-align:top;padding-right:12px;">
                  <div style="width:24px;height:24px;background:#f0f0f0;border-radius:50%;text-align:center;line-height:24px;color:#aaa;font-size:12px;">3</div>
                </td>
                <td style="vertical-align:top;">
                  <p style="color:#aaa;font-size:14px;margin:0 0 2px;">A Caminho</p>
                  <p style="color:#ccc;font-size:12px;margin:0;">Estafeta a dirigir-se ao destino</p>
                </td>
              </tr>
            </table>
            <!-- CTA -->
            <table cellpadding="0" cellspacing="0" style="margin:0 auto;">
              <tr>
                <td style="background:linear-gradient(135deg,#FF6B35,#FF8C42);border-radius:50px;padding:14px 36px;">
                  <a href="{$orderLink}" style="color:#ffffff;font-size:15px;font-weight:700;text-decoration:none;display:block;">
                    Acompanhar Pedido 📍
                  </a>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <!-- Footer -->
        <tr>
          <td style="background:#fafafa;border-top:1px solid #f0f0f0;padding:24px 40px;text-align:center;">
            <p style="color:#aaa;font-size:12px;margin:0;">
              © 2026 FoodExpress. Todos os direitos reservados.
            </p>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
    }
}
