{{-- Chat Widget for Restaurantes Mexicanos Famosos - Carmen --}}
{{-- Context-aware: Different behavior on owner pages vs regular pages --}}
<style>
:root {
    --rmf-primary: #D4AF37;
    --rmf-dark: #B8962E;
    --rmf-soft: rgba(212,175,55,0.08);
    --rmf-accent: #D4AF37;
    --rmf-gradient: linear-gradient(135deg, #1A1A1A 0%, #111 100%);
    --rmf-bg: #0B0B0B;
    --rmf-card: #1A1A1A;
    --rmf-border: rgba(212,175,55,0.2);
    --rmf-text: #F5F5F5;
    --rmf-muted: #9CA3AF;
}

#rmf-chat-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 99999;
    font-family: 'Poppins', 'Segoe UI', sans-serif;
}

#rmf-chat-btn {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    border: 2px solid rgba(212,175,55,0.6);
    cursor: pointer;
    box-shadow: 0 4px 24px rgba(212,175,55,0.25);
    transition: all 0.3s ease;
    overflow: hidden;
    padding: 0;
    background: #111;
    position: relative;
}

#rmf-chat-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 32px rgba(212,175,55,0.45);
    animation: none;
}

#rmf-chat-btn img {
    border-radius: 50%;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Avatar Animation - Gold pulse */
#rmf-chat-btn::before {
    content: '';
    position: absolute;
    top: -4px;
    left: -4px;
    right: -4px;
    bottom: -4px;
    border-radius: 50%;
    background: linear-gradient(135deg, #D4AF37, #B8962E);
    z-index: -1;
    animation: rmfAvatarGlow 2s ease-in-out infinite;
}

#rmf-chat-btn::after {
    content: '';
    position: absolute;
    top: -8px;
    left: -8px;
    right: -8px;
    bottom: -8px;
    border-radius: 50%;
    border: 2px solid rgba(212,175,55,0.5);
    opacity: 0;
    animation: rmfAvatarRing 2s ease-in-out infinite;
}

@keyframes rmfAvatarGlow {
    0%, 100% { opacity: 0.6; transform: scale(1); }
    50% { opacity: 1; transform: scale(1.05); }
}

@keyframes rmfAvatarRing {
    0% { opacity: 0; transform: scale(0.9); }
    50% { opacity: 0.5; transform: scale(1.1); }
    100% { opacity: 0; transform: scale(1.2); }
}

#rmf-chat-btn {
    animation: rmfAvatarBounce 3s ease-in-out infinite;
}

@keyframes rmfAvatarBounce {
    0%, 100% { transform: translateY(0); }
    15% { transform: translateY(-6px); }
    30% { transform: translateY(0); }
    45% { transform: translateY(-4px); }
    60% { transform: translateY(0); }
}

#rmf-trigger-bubble {
    position: absolute;
    bottom: 80px;
    right: 0;
    background: #1A1A1A;
    padding: 14px 18px;
    border-radius: 20px 20px 4px 20px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.5);
    max-width: 280px;
    font-size: 14px;
    line-height: 1.5;
    color: var(--rmf-text);
    display: none;
    animation: rmfBubblePop 0.3s ease;
    border: 1px solid var(--rmf-border);
}

@keyframes rmfBubblePop {
    0% { opacity: 0; transform: scale(0.8) translateY(10px); }
    100% { opacity: 1; transform: scale(1) translateY(0); }
}

#rmf-trigger-bubble .close-bubble {
    position: absolute;
    top: -8px;
    left: -8px;
    width: 20px;
    height: 20px;
    background: #2A2A2A;
    color: var(--rmf-muted);
    border-radius: 50%;
    border: 1px solid #3A3A3A;
    cursor: pointer;
    font-size: 12px;
    line-height: 20px;
}

#rmf-chat-container {
    position: fixed;
    bottom: 100px;
    right: 20px;
    width: 380px;
    max-width: calc(100vw - 40px);
    height: 550px;
    max-height: calc(100vh - 120px);
    background: var(--rmf-bg);
    border-radius: 20px;
    box-shadow: 0 10px 60px rgba(0,0,0,0.7), 0 0 0 1px var(--rmf-border);
    display: none;
    flex-direction: column;
    overflow: hidden;
    z-index: 99998;
}

#rmf-chat-header {
    background: #111;
    color: var(--rmf-text);
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    border-bottom: 1px solid var(--rmf-border);
}

#rmf-chat-header .avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: 2px solid rgba(212,175,55,0.5);
    object-fit: cover;
}

#rmf-chat-header .info h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: var(--rmf-text);
    font-family: 'Playfair Display', serif;
}

#rmf-chat-header .info span {
    font-size: 11px;
    color: var(--rmf-muted);
}

#rmf-chat-header .close-btn {
    background: rgba(255,255,255,0.06);
    border: 1px solid #2A2A2A;
    color: var(--rmf-muted);
    width: 32px;
    height: 32px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 18px;
    transition: all 0.2s;
}

#rmf-chat-header .close-btn:hover {
    background: rgba(212,175,55,0.1);
    border-color: rgba(212,175,55,0.4);
    color: var(--rmf-primary);
}

#rmf-header-actions {
    display: flex;
    gap: 8px;
    margin-left: auto;
    margin-right: 8px;
}

.rmf-header-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 1px solid #2A2A2A;
    background: rgba(255,255,255,0.04);
    color: var(--rmf-muted);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.rmf-header-btn:hover {
    background: rgba(212,175,55,0.1);
    border-color: rgba(212,175,55,0.4);
    color: var(--rmf-primary);
}

.rmf-header-btn svg {
    width: 18px;
    height: 18px;
    fill: currentColor;
}

#rmf-lang-toggle {
    display: flex;
    gap: 4px;
}

#rmf-lang-toggle button {
    padding: 4px 8px;
    border: 1px solid #2A2A2A;
    background: transparent;
    color: var(--rmf-muted);
    border-radius: 4px;
    cursor: pointer;
    font-size: 11px;
    transition: all 0.2s;
}

#rmf-lang-toggle button.active {
    background: rgba(212,175,55,0.15);
    border-color: rgba(212,175,55,0.5);
    color: var(--rmf-primary);
}

#rmf-chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
    background: #0D0D0D;
    scrollbar-width: thin;
    scrollbar-color: #2A2A2A transparent;
}

#rmf-chat-messages::-webkit-scrollbar { width: 4px; }
#rmf-chat-messages::-webkit-scrollbar-track { background: transparent; }
#rmf-chat-messages::-webkit-scrollbar-thumb { background: #2A2A2A; border-radius: 2px; }

.rmf-message {
    margin-bottom: 12px;
    display: flex;
    flex-direction: column;
}

.rmf-message.bot { align-items: flex-start; }
.rmf-message.user { align-items: flex-end; }

.rmf-message .bubble {
    max-width: 85%;
    padding: 12px 16px;
    border-radius: 18px;
    font-size: 14px;
    line-height: 1.5;
}

.rmf-message.bot .bubble {
    background: #1A1A1A;
    color: var(--rmf-text);
    border: 1px solid #2A2A2A;
    border-bottom-left-radius: 4px;
}

.rmf-message.user .bubble {
    background: linear-gradient(135deg, #D4AF37, #B8962E);
    color: #0B0B0B;
    font-weight: 500;
    border-bottom-right-radius: 4px;
}

.rmf-quick-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    padding: 10px 15px;
    border-top: 1px solid #1A1A1A;
    background: #111;
}

.rmf-quick-btn {
    background: rgba(212,175,55,0.08);
    border: 1px solid rgba(212,175,55,0.3);
    color: #D4AF37;
    padding: 8px 14px;
    border-radius: 20px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s;
}

.rmf-quick-btn:hover {
    background: rgba(212,175,55,0.18);
    border-color: rgba(212,175,55,0.6);
}

#rmf-chat-input-area {
    display: flex;
    padding: 12px;
    border-top: 1px solid #1A1A1A;
    background: #111;
    gap: 8px;
}

#rmf-chat-input {
    flex: 1;
    border: 1px solid #2A2A2A;
    border-radius: 25px;
    padding: 12px 18px;
    font-size: 14px;
    outline: none;
    background: #1A1A1A;
    color: var(--rmf-text);
    transition: border-color 0.2s;
}

#rmf-chat-input::placeholder { color: var(--rmf-muted); }

#rmf-chat-input:focus {
    border-color: rgba(212,175,55,0.5);
}

#rmf-chat-send {
    background: linear-gradient(135deg, #D4AF37, #B8962E);
    border: none;
    color: #0B0B0B;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 18px;
    font-weight: bold;
    transition: transform 0.2s, opacity 0.2s;
}

#rmf-chat-send:hover {
    transform: scale(1.05);
    opacity: 0.9;
}

.rmf-typing {
    display: flex;
    gap: 4px;
    padding: 12px 16px;
    background: #1A1A1A;
    border-radius: 18px;
    border: 1px solid #2A2A2A;
    width: fit-content;
}

.rmf-typing span {
    width: 8px;
    height: 8px;
    background: #D4AF37;
    border-radius: 50%;
    animation: rmfTyping 1.4s infinite;
}

.rmf-typing span:nth-child(2) { animation-delay: 0.2s; }
.rmf-typing span:nth-child(3) { animation-delay: 0.4s; }

@keyframes rmfTyping {
    0%, 100% { opacity: 0.3; }
    50% { opacity: 1; }
}

.rmf-restaurant-card {
    background: #1A1A1A;
    border: 1px solid rgba(212,175,55,0.2);
    border-radius: 12px;
    padding: 12px;
    margin-top: 8px;
}

.rmf-restaurant-card h4 {
    margin: 0 0 4px 0;
    font-size: 14px;
    color: var(--rmf-text);
}

.rmf-restaurant-card .rating {
    color: #D4AF37;
    font-size: 12px;
}

.rmf-restaurant-card .location {
    color: var(--rmf-muted);
    font-size: 12px;
    margin: 4px 0;
}

.rmf-restaurant-card .btn {
    display: inline-block;
    margin-top: 8px;
    padding: 6px 12px;
    background: linear-gradient(135deg, #D4AF37, #B8962E);
    color: #0B0B0B;
    text-decoration: none;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
}

.rmf-benefit-card {
    background: rgba(212,175,55,0.06);
    border: 1px solid rgba(212,175,55,0.25);
    border-radius: 12px;
    padding: 12px;
    margin-top: 8px;
}

.rmf-benefit-card h4 {
    margin: 0 0 8px 0;
    font-size: 14px;
    color: #D4AF37;
}

.rmf-benefit-card ul {
    margin: 0;
    padding-left: 16px;
    font-size: 12px;
    color: var(--rmf-muted);
}

.rmf-benefit-card li {
    margin-bottom: 4px;
}

.rmf-cta-btn {
    display: block;
    margin-top: 10px;
    padding: 10px 16px;
    background: linear-gradient(135deg, #D4AF37, #B8962E);
    color: #0B0B0B;
    text-decoration: none;
    border-radius: 8px;
    font-size: 13px;
    text-align: center;
    font-weight: 700;
    transition: opacity 0.2s;
}

.rmf-cta-btn:hover {
    opacity: 0.88;
}

@media (max-width: 480px) {
    #rmf-chat-container {
        width: 100%;
        height: 100%;
        max-height: 100%;
        bottom: 0;
        right: 0;
        border-radius: 0;
    }
}
</style>

<div id="rmf-chat-widget">
    <div id="rmf-trigger-bubble">
        <button class="close-bubble" onclick="rmfCloseTrigger()">&times;</button>
        <span id="rmf-trigger-text"></span>
    </div>
    <button id="rmf-chat-btn" onclick="rmfToggleChat()">
        <img src="/images/carmen-avatar.jpg" alt="Carmen">
    </button>
</div>

<div id="rmf-chat-container">
    <div id="rmf-chat-header">
        <img src="/images/carmen-avatar.jpg" alt="Carmen" class="avatar">
        <div class="info">
            <h3>Carmen</h3>
            <span id="rmf-status"></span>
        </div>
        <div id="rmf-header-actions">
            <div id="rmf-lang-toggle">
                <button onclick="rmfSetLang('es')" class="active">ES</button>
                <button onclick="rmfSetLang('en')">EN</button>
            </div>
            <button class="rmf-header-btn" onclick="rmfCallPhone()" title="Llamar">
                <svg viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
            </button>
            <button class="rmf-header-btn" onclick="rmfOpenWhatsApp()" title="WhatsApp">
                <svg viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            </button>
        </div>
        <button class="close-btn" onclick="rmfToggleChat()">&times;</button>
    </div>

    <div id="rmf-chat-messages"></div>

    <div class="rmf-quick-actions" id="rmf-quick-actions"></div>

    <div id="rmf-chat-input-area">
        <input type="text" id="rmf-chat-input" placeholder="" onkeypress="if(event.key==='Enter')rmfSendMessage()">
        <button id="rmf-chat-send" onclick="rmfSendMessage()">➤</button>
    </div>
</div>

<script>
const RMF = {
    isOpen: false,
    sessionId: 'rmf_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9),
    language: 'es',
    typewriterSpeed: 20,
    isOwnerPage: false,
    phoneNumber: '+12149876068',
    whatsappNumber: '12149876068',

    init: function() {
        // Detect if on owner page
        var path = window.location.pathname;
        this.isOwnerPage = path.includes('/for-owners') || path.includes('/claim') || path.includes('/grader');

        // Set initial language - default to Spanish
        this.language = 'es';

        // Update UI based on context
        this.updateUI();
    },

    texts: {
        es: {
            // Regular visitor texts
            status: 'En linea - Guia de restaurantes',
            trigger: '¿Buscas un restaurante mexicano?',
            welcome: '¡Hola! Soy Carmen, tu guia de restaurantes mexicanos. ¿Que tipo de comida se te antoja hoy?',
            placeholder: 'Busca restaurantes, platillos...',
            error: 'Hubo un error. Por favor intenta de nuevo.',

            // Owner page texts
            ownerStatus: 'En linea - Asesora para duenos',
            ownerTrigger: '¿Tienes un restaurante? ¡Te ayudo a crecer!',
            ownerWelcome: '¡Hola! Soy Carmen, asesora para duenos de restaurantes. ¿Ya reclamaste tu restaurante en nuestro directorio? Es GRATIS y te ayuda a atraer mas clientes.',
            ownerPlaceholder: 'Pregunta sobre tu negocio...',

            // Quick action labels
            btnSearch: '🔍 Buscar',
            btnFeatured: '⭐ Destacados',
            btnNearby: '📍 Cerca de mi',
            btnRegister: '🏪 Mi negocio',
            btnWhatsapp: '💬 WhatsApp',

            // Owner quick actions
            btnClaim: '✅ Reclamar GRATIS',
            btnBenefits: '📈 Ver Beneficios',
            btnPricing: '💎 Planes Premium',
            btnGrader: '📊 Calificar mi Restaurante',
            btnHelp: '❓ Necesito Ayuda'
        },
        en: {
            status: 'Online - Restaurant Guide',
            trigger: 'Looking for a Mexican restaurant?',
            welcome: 'Hi! I\'m Carmen, your Mexican restaurant guide. What type of food are you craving today?',
            placeholder: 'Search restaurants, dishes...',
            error: 'There was an error. Please try again.',

            ownerStatus: 'Online - Owner Advisor',
            ownerTrigger: 'Own a restaurant? Let me help you grow!',
            ownerWelcome: 'Hi! I\'m Carmen, advisor for restaurant owners. Have you claimed your restaurant in our directory? It\'s FREE and helps you attract more customers.',
            ownerPlaceholder: 'Ask about your business...',

            btnSearch: '🔍 Search',
            btnFeatured: '⭐ Featured',
            btnNearby: '📍 Near me',
            btnRegister: '🏪 My business',
            btnWhatsapp: '💬 WhatsApp',

            btnClaim: '✅ Claim FREE',
            btnBenefits: '📈 See Benefits',
            btnPricing: '💎 Premium Plans',
            btnGrader: '📊 Grade my Restaurant',
            btnHelp: '❓ Need Help'
        }
    },

    getText: function(key) {
        return this.texts[this.language][key] || this.texts['es'][key];
    },

    updateUI: function() {
        var lang = this.language;
        var isOwner = this.isOwnerPage;

        // Update language buttons
        document.querySelectorAll('#rmf-lang-toggle button').forEach(function(btn) {
            btn.classList.remove('active');
        });
        document.querySelector('#rmf-lang-toggle button[onclick*="' + lang + '"]').classList.add('active');

        // Update status and placeholder
        document.getElementById('rmf-status').textContent = isOwner ? this.getText('ownerStatus') : this.getText('status');
        document.getElementById('rmf-chat-input').placeholder = isOwner ? this.getText('ownerPlaceholder') : this.getText('placeholder');
        document.getElementById('rmf-trigger-text').textContent = isOwner ? this.getText('ownerTrigger') : this.getText('trigger');

        // Update quick actions based on context
        var actionsHtml = '';
        if (isOwner) {
            actionsHtml = '<button class="rmf-quick-btn" onclick="rmfQuickAction(\'claim\')">' + this.getText('btnClaim') + '</button>' +
                         '<button class="rmf-quick-btn" onclick="rmfQuickAction(\'benefits\')">' + this.getText('btnBenefits') + '</button>' +
                         '<button class="rmf-quick-btn" onclick="rmfQuickAction(\'pricing\')">' + this.getText('btnPricing') + '</button>' +
                         '<button class="rmf-quick-btn" onclick="rmfQuickAction(\'grader\')">' + this.getText('btnGrader') + '</button>';
        } else {
            actionsHtml = '<button class="rmf-quick-btn" onclick="rmfQuickAction(\'search\')">' + this.getText('btnSearch') + '</button>' +
                         '<button class="rmf-quick-btn" onclick="rmfQuickAction(\'featured\')">' + this.getText('btnFeatured') + '</button>' +
                         '<button class="rmf-quick-btn" onclick="rmfQuickAction(\'nearby\')">' + this.getText('btnNearby') + '</button>' +
                         '<button class="rmf-quick-btn" onclick="rmfQuickAction(\'register\')">' + this.getText('btnRegister') + '</button>';
        }
        document.getElementById('rmf-quick-actions').innerHTML = actionsHtml;
    }
};

function rmfSetLang(lang) {
    RMF.language = lang;
    RMF.updateUI();
    rmfAddMessage(lang === 'es' ? 'Ahora hablamos en espanol' : 'Now we speak English', 'bot');
}

function rmfToggleChat() {
    var container = document.getElementById('rmf-chat-container');
    RMF.isOpen = !RMF.isOpen;
    container.style.display = RMF.isOpen ? 'flex' : 'none';
    document.getElementById('rmf-trigger-bubble').style.display = 'none';

    if (RMF.isOpen && !document.getElementById('rmf-chat-messages').children.length) {
        var welcomeMsg = RMF.isOwnerPage ? RMF.getText('ownerWelcome') : RMF.getText('welcome');
        rmfAddMessage(welcomeMsg, 'bot');
    }
}

function rmfCloseTrigger() {
    document.getElementById('rmf-trigger-bubble').style.display = 'none';
}

function rmfCallPhone() {
    window.location.href = 'tel:' + RMF.phoneNumber;
}

function rmfOpenWhatsApp() {
    var msg = RMF.language === 'es'
        ? 'Hola, necesito informacion sobre restaurantes mexicanos'
        : 'Hi, I need information about Mexican restaurants';
    window.open('https://wa.me/' + RMF.whatsappNumber + '?text=' + encodeURIComponent(msg), '_blank');
}

function rmfTypewriter(element, text, index, callback) {
    if (index < text.length) {
        element.innerHTML = text.substring(0, index + 1);
        setTimeout(function() {
            rmfTypewriter(element, text, index + 1, callback);
        }, RMF.typewriterSpeed);
    } else if (callback) {
        callback();
    }
}

function rmfAddMessage(text, sender, html) {
    var messagesDiv = document.getElementById('rmf-chat-messages');
    var msgDiv = document.createElement('div');
    msgDiv.className = 'rmf-message ' + sender;
    var bubble = document.createElement('div');
    bubble.className = 'bubble';
    msgDiv.appendChild(bubble);
    messagesDiv.appendChild(msgDiv);

    if (sender === 'bot' && !html) {
        rmfTypewriter(bubble, text.replace(/</g, '&lt;').replace(/>/g, '&gt;'), 0, function() {
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        });
    } else {
        bubble.innerHTML = html ? text : text.replace(/</g, '&lt;').replace(/>/g, '&gt;');
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    // Sync to central dashboard
    rmfSyncToCenter(text, sender);
}

function rmfShowTyping() {
    var messagesDiv = document.getElementById('rmf-chat-messages');
    var typingDiv = document.createElement('div');
    typingDiv.id = 'rmf-typing-indicator';
    typingDiv.className = 'rmf-message bot';
    typingDiv.innerHTML = '<div class="rmf-typing"><span></span><span></span><span></span></div>';
    messagesDiv.appendChild(typingDiv);
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

function rmfHideTyping() {
    var typing = document.getElementById('rmf-typing-indicator');
    if (typing) typing.remove();
}

function rmfSendMessage() {
    var input = document.getElementById('rmf-chat-input');
    var message = input.value.trim();
    if (!message) return;
    rmfAddMessage(message, 'user');
    input.value = '';
    rmfProcessMessage(message);
}

async function rmfProcessMessage(message) {
    rmfShowTyping();
    try {
        var response = await fetch('/chat/message', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                message: message,
                session_id: RMF.sessionId,
                language: RMF.language,
                is_owner_page: RMF.isOwnerPage
            })
        });
        var data = await response.json();
        rmfHideTyping();
        rmfAddMessage(data.response || RMF.getText('error'), 'bot', data.html);
    } catch (error) {
        rmfHideTyping();
        rmfAddMessage(RMF.getText('error'), 'bot');
    }
}

function rmfQuickAction(action) {
    var lang = RMF.language;

    switch(action) {
        case 'search':
            rmfAddMessage(lang === 'es' ? '¿Que platillo o tipo de restaurante buscas?' : 'What dish or type of restaurant are you looking for?', 'bot');
            break;

        case 'featured':
            rmfAddMessage(lang === 'es' ? 'Ver destacados' : 'View featured', 'user');
            rmfShowFeatured();
            break;

        case 'nearby':
            rmfAddMessage(lang === 'es' ? 'Cerca de mi' : 'Near me', 'user');
            rmfShowNearby();
            break;

        case 'register':
            rmfAddMessage(lang === 'es' ?
                '¿Tienes un restaurante mexicano? ¡Registralo GRATIS! Visita /for-owners o reclama tu negocio en /claim' :
                'Do you own a Mexican restaurant? Register it FREE! Visit /for-owners or claim your business at /claim', 'bot');
            break;

        case 'claim':
            rmfShowClaimInfo();
            break;

        case 'benefits':
            rmfShowBenefits();
            break;

        case 'pricing':
            rmfShowPricing();
            break;

        case 'grader':
            rmfAddMessage(lang === 'es' ? 'Ir al calificador' : 'Go to grader', 'user');
            rmfAddMessage(lang === 'es' ?
                '¡El FAMER Score te ayuda a entender como mejorar tu restaurante! <a href="/grader" class="rmf-cta-btn">Calificar mi Restaurante</a>' :
                'The FAMER Score helps you understand how to improve your restaurant! <a href="/grader" class="rmf-cta-btn">Grade my Restaurant</a>', 'bot', true);
            break;

        case 'whatsapp':
            var msg = RMF.isOwnerPage ?
                (lang === 'es' ? 'Hola, soy dueno de un restaurante y quiero informacion' : 'Hi, I own a restaurant and want information') :
                (lang === 'es' ? 'Hola, tengo una pregunta sobre restaurantes mexicanos' : 'Hi, I have a question about Mexican restaurants');
            window.open('https://wa.me/12149876068?text=' + encodeURIComponent(msg), '_blank');
            break;
    }
}

function rmfShowClaimInfo() {
    var lang = RMF.language;
    var html = lang === 'es' ?
        '<div class="rmf-benefit-card"><h4>✅ Reclamar tu Restaurante es GRATIS</h4>' +
        '<ul><li>Aparece en busquedas locales</li>' +
        '<li>Responde a resenas de clientes</li>' +
        '<li>Actualiza fotos y menu</li>' +
        '<li>Recibe estadisticas de visitas</li></ul>' +
        '<a href="/claim" class="rmf-cta-btn">Reclamar Ahora - Es Gratis</a></div>' :
        '<div class="rmf-benefit-card"><h4>✅ Claiming your Restaurant is FREE</h4>' +
        '<ul><li>Appear in local searches</li>' +
        '<li>Respond to customer reviews</li>' +
        '<li>Update photos and menu</li>' +
        '<li>Get visit statistics</li></ul>' +
        '<a href="/claim" class="rmf-cta-btn">Claim Now - It\'s Free</a></div>';
    rmfAddMessage(html, 'bot', true);
}

function rmfShowBenefits() {
    var lang = RMF.language;
    var html = lang === 'es' ?
        '<div class="rmf-benefit-card"><h4>📈 Beneficios para Duenos</h4>' +
        '<ul><li><strong>Gratis:</strong> Perfil basico, responder resenas</li>' +
        '<li><strong>Pro:</strong> Fotos ilimitadas, menu digital, reservaciones</li>' +
        '<li><strong>Premium:</strong> Anuncios destacados, analytics avanzados, soporte prioritario</li></ul>' +
        '<p style="font-size:12px;margin-top:8px;color:#666;">Miles de clientes buscan restaurantes cada dia. ¿Esta el tuyo visible?</p>' +
        '<a href="/for-owners#pricing" class="rmf-cta-btn">Ver Todos los Planes</a></div>' :
        '<div class="rmf-benefit-card"><h4>📈 Benefits for Owners</h4>' +
        '<ul><li><strong>Free:</strong> Basic profile, respond to reviews</li>' +
        '<li><strong>Pro:</strong> Unlimited photos, digital menu, reservations</li>' +
        '<li><strong>Premium:</strong> Featured listings, advanced analytics, priority support</li></ul>' +
        '<p style="font-size:12px;margin-top:8px;color:#666;">Thousands of customers search for restaurants every day. Is yours visible?</p>' +
        '<a href="/for-owners#pricing" class="rmf-cta-btn">See All Plans</a></div>';
    rmfAddMessage(html, 'bot', true);
}

function rmfShowPricing() {
    var lang = RMF.language;
    var html = lang === 'es' ?
        '<div class="rmf-benefit-card"><h4>💎 Planes y Precios</h4>' +
        '<div style="margin:8px 0;padding:8px;background:white;border-radius:8px;">' +
        '<strong style="color:#006847;">GRATIS</strong> - $0/mes<br><small>Perfil basico, resenas, contacto</small></div>' +
        '<div style="margin:8px 0;padding:8px;background:white;border-radius:8px;">' +
        '<strong style="color:#CE1126;">PRO</strong> - $39/mes<br><small>Menu digital, fotos, reservaciones</small></div>' +
        '<div style="margin:8px 0;padding:8px;background:white;border-radius:8px;">' +
        '<strong style="color:#D4AF37;">PREMIUM</strong> - $79/mes<br><small>Todo Pro + anuncios, analytics, soporte VIP</small></div>' +
        '<a href="/for-owners#pricing" class="rmf-cta-btn">Comenzar Gratis</a></div>' :
        '<div class="rmf-benefit-card"><h4>💎 Plans and Pricing</h4>' +
        '<div style="margin:8px 0;padding:8px;background:white;border-radius:8px;">' +
        '<strong style="color:#006847;">FREE</strong> - $0/month<br><small>Basic profile, reviews, contact</small></div>' +
        '<div style="margin:8px 0;padding:8px;background:white;border-radius:8px;">' +
        '<strong style="color:#CE1126;">PRO</strong> - $39/month<br><small>Digital menu, photos, reservations</small></div>' +
        '<div style="margin:8px 0;padding:8px;background:white;border-radius:8px;">' +
        '<strong style="color:#D4AF37;">PREMIUM</strong> - $79/month<br><small>All Pro + ads, analytics, VIP support</small></div>' +
        '<a href="/for-owners#pricing" class="rmf-cta-btn">Start Free</a></div>';
    rmfAddMessage(html, 'bot', true);
}

async function rmfShowFeatured() {
    rmfShowTyping();
    try {
        var response = await fetch('/chat/restaurants/featured');
        var data = await response.json();
        rmfHideTyping();

        if (data.restaurants && data.restaurants.length > 0) {
            var html = '<div>' + (RMF.language === 'es' ? 'Restaurantes destacados:' : 'Featured restaurants:') + '</div>';
            data.restaurants.forEach(function(r) {
                var stars = '';
                for (var i = 0; i < 5; i++) {
                    stars += i < Math.round(r.average_rating) ? '★' : '☆';
                }
                html += '<div class="rmf-restaurant-card">' +
                    '<h4>' + r.name + '</h4>' +
                    '<div class="rating">' + stars + ' (' + r.total_reviews + ')</div>' +
                    '<div class="location">📍 ' + r.city + '</div>' +
                    '<a href="/restaurantes/' + r.slug + '" class="btn">' + (RMF.language === 'es' ? 'Ver detalles' : 'View details') + '</a></div>';
            });
            rmfAddMessage(html, 'bot', true);
        } else {
            rmfAddMessage(RMF.language === 'es' ? 'No hay restaurantes destacados disponibles.' : 'No featured restaurants available.', 'bot');
        }
    } catch (error) {
        rmfHideTyping();
        rmfAddMessage(RMF.getText('error'), 'bot');
    }
}

function rmfShowNearby() {
    if (navigator.geolocation) {
        rmfShowTyping();
        navigator.geolocation.getCurrentPosition(
            async function(position) {
                try {
                    var response = await fetch('/chat/restaurants/nearby?lat=' + position.coords.latitude + '&lng=' + position.coords.longitude);
                    var data = await response.json();
                    rmfHideTyping();

                    if (data.restaurants && data.restaurants.length > 0) {
                        var html = '<div>' + (RMF.language === 'es' ? 'Restaurantes cerca de ti:' : 'Restaurants near you:') + '</div>';
                        data.restaurants.forEach(function(r) {
                            var stars = '';
                            for (var i = 0; i < 5; i++) {
                                stars += i < Math.round(r.average_rating) ? '★' : '☆';
                            }
                            html += '<div class="rmf-restaurant-card">' +
                                '<h4>' + r.name + '</h4>' +
                                '<div class="rating">' + stars + '</div>' +
                                '<div class="location">📍 ' + r.city + (r.distance ? ' - ' + r.distance.toFixed(1) + ' mi' : '') + '</div>' +
                                '<a href="/restaurantes/' + r.slug + '" class="btn">' + (RMF.language === 'es' ? 'Ver detalles' : 'View details') + '</a></div>';
                        });
                        rmfAddMessage(html, 'bot', true);
                    } else {
                        rmfAddMessage(RMF.language === 'es' ? 'No encontre restaurantes cerca.' : 'No restaurants found nearby.', 'bot');
                    }
                } catch (error) {
                    rmfHideTyping();
                    rmfAddMessage(RMF.getText('error'), 'bot');
                }
            },
            function(error) {
                rmfHideTyping();
                rmfAddMessage(RMF.language === 'es' ? 'No pude acceder a tu ubicacion.' : 'Could not access your location.', 'bot');
            }
        );
    } else {
        rmfAddMessage(RMF.language === 'es' ? 'Tu navegador no soporta geolocalizacion.' : 'Your browser does not support geolocation.', 'bot');
    }
}

function rmfSyncToCenter(message, role) {
    fetch('https://admin.mf-imports.com/api/chat/message', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            business: 'restaurantesmexicanos',
            session_id: RMF.sessionId,
            user_message: role === 'user' ? message : '',
            bot_response: role === 'bot' ? message : '',
            language: RMF.language,
            page_url: window.location.href,
            is_owner_page: RMF.isOwnerPage
        })
    }).catch(function() {});
}

// Initialize
RMF.init();

// Show trigger bubble after delay
setTimeout(function() {
    if (!RMF.isOpen) {
        document.getElementById('rmf-trigger-bubble').style.display = 'block';
    }
}, RMF.isOwnerPage ? 2000 : 4000); // Show faster on owner pages
</script>
