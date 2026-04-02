@section('title', 'FAMER Grader — Analiza el Perfil de Tu Restaurante Mexicano')
@section('meta_description', 'Obtén una calificación gratis de tu restaurante mexicano. FAMER Grader analiza tu presencia online, reseñas, horarios y más en segundos.')

@push('meta')
<meta property="og:type" content="website">
<meta property="og:title" content="🏅 FAMER Grader — ¿Cuánto Vale el Perfil de Tu Restaurante?">
<meta property="og:description" content="Analiza tu restaurante mexicano gratis en segundos. FAMER Grader revisa tus reseñas, horarios, fotos y presencia online — y te da una calificación de 0 a 100.">
<meta property="og:image" content="https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1200&h=630&q=85">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="FAMER Grader — Analiza el perfil de tu restaurante mexicano">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="FAMER - Famous Mexican Restaurants">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="🏅 FAMER Grader — ¿Cuánto Vale el Perfil de Tu Restaurante?">
<meta name="twitter:description" content="Analiza tu restaurante mexicano gratis. Reseñas, fotos, horarios y más — calificación de 0 a 100 en segundos.">
<meta name="twitter:image" content="https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1200&h=630&q=85">
@endpush

<div class="min-h-screen bg-[#0B0B0B]">
    {{-- GSAP CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12/dist/gsap.min.js"></script>

    {{-- Styles --}}
    <style>
        [x-cloak] { display: none !important; }

        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap');

        /* --- Keyframes --- */
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        @keyframes pulse-gold {
            0%, 100% { box-shadow: 0 0 0 0 rgba(212,175,55,0.4); }
            50% { box-shadow: 0 0 20px 10px rgba(212,175,55,0.15); }
        }
        @keyframes glow-ring {
            0%, 100% { filter: drop-shadow(0 0 6px rgba(212,175,55,0.3)); }
            50% { filter: drop-shadow(0 0 16px rgba(212,175,55,0.6)); }
        }
        @keyframes float-particle {
            0%, 100% { transform: translateY(0) scale(1); opacity: 0.3; }
            50% { transform: translateY(-20px) scale(1.5); opacity: 0.7; }
        }
        @keyframes scan-line {
            0% { top: 0; }
            100% { top: 100%; }
        }
        @keyframes typewriter-cursor {
            0%, 100% { opacity: 1; }
            50% { opacity: 0; }
        }
        @keyframes confetti-fall {
            0% { transform: translateY(-100vh) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
        }
        @keyframes map-grid-pulse {
            0%, 100% { opacity: 0.03; }
            50% { opacity: 0.08; }
        }
        @keyframes pin-drop {
            0% { transform: translateY(-40px) scale(0); opacity: 0; }
            60% { transform: translateY(4px) scale(1.1); opacity: 1; }
            100% { transform: translateY(0) scale(1); opacity: 1; }
        }
        @keyframes quality-check {
            0% { transform: scale(0) rotate(-10deg); opacity: 0; }
            60% { transform: scale(1.2) rotate(2deg); opacity: 1; }
            100% { transform: scale(1) rotate(0deg); opacity: 1; }
        }
        @keyframes bar-fill {
            from { width: 0%; }
        }
        @keyframes count-pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.15); }
            100% { transform: scale(1); }
        }

        /* --- Utility classes --- */
        .font-playfair { font-family: 'Playfair Display', serif; }
        .font-poppins { font-family: 'Poppins', sans-serif; }

        .gold-gradient-text {
            background: linear-gradient(135deg, #D4AF37, #E8C67A, #D4AF37);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .gold-border-glow {
            box-shadow: 0 0 0 1px rgba(212,175,55,0.2), 0 0 20px rgba(212,175,55,0.05);
        }
        .shimmer-gold {
            background: linear-gradient(90deg, #D4AF37 0%, #E8C67A 30%, #FFF8DC 50%, #E8C67A 70%, #D4AF37 100%);
            background-size: 200% 100%;
            animation: shimmer 2s ease-in-out infinite;
        }
        .shimmer-bar {
            background: linear-gradient(90deg, #D4AF37 0%, #E8C67A 40%, #F5E6A3 50%, #E8C67A 60%, #D4AF37 100%);
            background-size: 200% 100%;
            animation: shimmer 1.5s ease-in-out infinite;
        }
        .pulse-gold-ring { animation: pulse-gold 2s ease-in-out infinite; }
        .glow-ring-anim { animation: glow-ring 2s ease-in-out infinite; }

        /* --- Particles --- */
        .particle {
            position: absolute;
            width: 4px; height: 4px;
            background: #D4AF37;
            border-radius: 50%;
            opacity: 0.2;
        }
        .particle:nth-child(1) { top: 20%; left: 10%; animation: float-particle 4s ease-in-out infinite 0s; }
        .particle:nth-child(2) { top: 60%; left: 80%; animation: float-particle 5s ease-in-out infinite 0.5s; }
        .particle:nth-child(3) { top: 40%; left: 50%; animation: float-particle 3.5s ease-in-out infinite 1s; }
        .particle:nth-child(4) { top: 80%; left: 20%; animation: float-particle 4.5s ease-in-out infinite 1.5s; }
        .particle:nth-child(5) { top: 30%; left: 70%; animation: float-particle 3s ease-in-out infinite 2s; }
        .particle:nth-child(6) { top: 70%; left: 40%; animation: float-particle 5.5s ease-in-out infinite 0.8s; }

        /* --- Map dark tiles --- */
        .map-dark-bg {
            background-color: #111;
            background-image:
                linear-gradient(rgba(212,175,55,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(212,175,55,0.04) 1px, transparent 1px);
            background-size: 40px 40px;
            animation: map-grid-pulse 4s ease-in-out infinite;
        }

        /* --- Scan line effect --- */
        .scan-line-effect {
            position: absolute;
            left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, #D4AF37, transparent);
            box-shadow: 0 0 20px rgba(212,175,55,0.6);
            animation: scan-line 2.5s ease-in-out infinite;
        }

        /* --- Step sidebar --- */
        .step-circle {
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .step-circle.active {
            box-shadow: 0 0 0 4px rgba(212,175,55,0.2), 0 0 16px rgba(212,175,55,0.3);
        }
        .step-circle.complete {
            box-shadow: 0 0 12px rgba(212,175,55,0.5);
        }
        .step-connector {
            transition: background-color 0.6s ease;
        }

        /* --- Typewriter cursor --- */
        .tw-cursor::after {
            content: '|';
            animation: typewriter-cursor 0.7s step-end infinite;
            color: #D4AF37;
            margin-left: 2px;
        }

        /* --- Confetti particle --- */
        .confetti-piece {
            position: absolute;
            width: 8px; height: 8px;
            animation: confetti-fall linear forwards;
        }

        /* --- Browser chrome mockup --- */
        .browser-chrome {
            background: #1A1A1A;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #2A2A2A;
        }
        .browser-chrome .top-bar {
            background: #111;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .browser-dot {
            width: 10px; height: 10px;
            border-radius: 50%;
        }

        /* --- Phone mockup --- */
        .phone-frame {
            width: 200px;
            background: #111;
            border-radius: 28px;
            border: 3px solid #333;
            padding: 12px 8px;
            position: relative;
        }
        .phone-notch {
            width: 80px; height: 20px;
            background: #111;
            border-radius: 0 0 14px 14px;
            position: absolute;
            top: 0; left: 50%;
            transform: translateX(-50%);
            z-index: 2;
        }
        .phone-screen {
            background: #1A1A1A;
            border-radius: 18px;
            overflow: hidden;
            position: relative;
            min-height: 320px;
        }

        /* --- Star fill animation --- */
        .star-fill {
            color: #2A2A2A;
            transition: color 0.3s ease;
        }
        .star-fill.lit { color: #D4AF37; }

        /* -- Smooth scroll for sections -- */
        .scan-content-area {
            min-height: 380px;
        }

        @media (max-width: 768px) {
            .scan-content-area { min-height: 320px; }
            .phone-frame { width: 160px; }
            .phone-screen { min-height: 260px; }
        }
    </style>

    {{-- Hero Section --}}
    <div class="relative overflow-hidden py-20" style="background: radial-gradient(ellipse at center top, rgba(212,175,55,0.08) 0%, #0B0B0B 70%);">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h1 class="font-playfair text-4xl md:text-6xl font-bold mb-4 gold-gradient-text">
                FAMER Score
            </h1>
            <p class="font-poppins text-xl text-[#F5F5F5] mb-2">
                Rate your restaurant's digital presence
            </p>
            <p class="font-poppins text-[#CCCCCC]/70">
                Discover how to improve your online visibility and attract more customers
            </p>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Messages --}}
        @if ($successMessage)
            <div class="mb-6 bg-[#1F3D2B]/30 border border-[#1F3D2B] text-green-300 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ $successMessage }}</span>
                </div>
            </div>
        @endif

        @if ($errorMessage)
            <div class="mb-6 bg-[#8B1E1E]/20 border border-[#8B1E1E] text-red-300 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ $errorMessage }}</span>
                </div>
            </div>
        @endif

        {{-- ============================================================ --}}
        {{-- SCANNING ANIMATION SECTION (THE STAR)                        --}}
        {{-- ============================================================ --}}
        @if ($showAnalysis && $selectedRestaurant)
            <div
                x-data="{
                    currentStep: -1,
                    overallProgress: 0,
                    isComplete: false,
                    secondsRemaining: 48,
                    countdownInterval: null,
                    stepLabels: [
                        'Restaurant & Competitors',
                        'Google Business Profile',
                        'Yelp Profile',
                        'Review Sentiment',
                        'Photo Quality',
                        'Website Analysis',
                        'Mobile Experience'
                    ],
                    stepIcons: ['map', 'google', 'yelp', 'reviews', 'photos', 'website', 'mobile'],
                    stepDurations: [8000, 7000, 6000, 8000, 6000, 6000, 5000],
                    steps: [
                        { status: 'pending', progress: 0 },
                        { status: 'pending', progress: 0 },
                        { status: 'pending', progress: 0 },
                        { status: 'pending', progress: 0 },
                        { status: 'pending', progress: 0 },
                        { status: 'pending', progress: 0 },
                        { status: 'pending', progress: 0 }
                    ],
                    scanData: {},
                    typewriterText: '',
                    typewriterIndex: 0,
                    activeReviewIndex: 0,
                    photoCheckIndex: -1,
                    starsLit: 0,
                    reviewCountDisplay: 0,
                    googleRatingDisplay: 0,
                    yelpStarsLit: 0,
                    yelpReviewCountDisplay: 0,
                    yelpRatingDisplay: 0,

                    async init() {
                        this.scanData = $wire.scanStepData || {};
                        await this.$nextTick();
                        await new Promise(r => setTimeout(r, 600));
                        this.startCountdown();
                        await this.runScan();
                    },

                    startCountdown() {
                        this.countdownInterval = setInterval(() => {
                            if (this.secondsRemaining > 0) {
                                this.secondsRemaining--;
                            } else {
                                clearInterval(this.countdownInterval);
                            }
                        }, 1000);
                    },

                    async runScan() {
                        const totalSteps = this.steps.length;
                        for (let i = 0; i < totalSteps; i++) {
                            this.currentStep = i;
                            this.steps[i].status = 'active';

                            // Animate step entrance with GSAP
                            this.animateStepIn(i);

                            // Simulate sub-progress
                            const duration = this.stepDurations[i];
                            const ticks = 20;
                            const tickDuration = duration / ticks;
                            for (let t = 1; t <= ticks; t++) {
                                await new Promise(r => setTimeout(r, tickDuration));
                                this.steps[i].progress = (t / ticks) * 100;
                                this.overallProgress = ((i + t / ticks) / totalSteps) * 100;
                            }

                            this.steps[i].status = 'complete';
                            this.steps[i].progress = 100;

                            // Flash the sidebar checkmark
                            this.animateStepComplete(i);

                            // Animate step exit
                            if (i < totalSteps - 1) {
                                await this.animateStepOut(i);
                            }
                        }
                        this.overallProgress = 100;
                        this.secondsRemaining = 0;
                        clearInterval(this.countdownInterval);
                        this.isComplete = true;
                        this.animateScanComplete();
                    },

                    animateStepIn(idx) {
                        const el = document.querySelector('.step-' + idx + '-content');
                        if (!el) return;
                        // Hide all step contents first
                        document.querySelectorAll('.scan-step-content').forEach(s => {
                            if (!s.classList.contains('step-' + idx + '-content')) {
                                gsap.set(s, { display: 'none', opacity: 0 });
                            }
                        });
                        gsap.set(el, { display: 'block', opacity: 0, y: 30 });
                        gsap.to(el, { opacity: 1, y: 0, duration: 0.6, ease: 'power3.out' });

                        // Step-specific GSAP animations
                        if (idx === 0) this.animateMapStep();
                        if (idx === 1) this.animateGoogleStep();
                        if (idx === 2) this.animateYelpStep();
                        if (idx === 3) this.animateReviewStep();
                        if (idx === 4) this.animatePhotoStep();
                        if (idx === 5) this.animateWebsiteStep();
                        if (idx === 6) this.animateMobileStep();
                    },

                    async animateStepOut(idx) {
                        const el = document.querySelector('.step-' + idx + '-content');
                        if (!el) return;
                        await new Promise(resolve => {
                            gsap.to(el, {
                                opacity: 0, y: -20, duration: 0.35, ease: 'power2.in',
                                onComplete: () => { gsap.set(el, { display: 'none' }); resolve(); }
                            });
                        });
                    },

                    animateStepComplete(idx) {
                        const circle = document.querySelector('#step-circle-' + idx);
                        if (circle) {
                            gsap.fromTo(circle,
                                { scale: 1 },
                                { scale: 1.3, duration: 0.2, yoyo: true, repeat: 1, ease: 'power2.out' }
                            );
                        }
                    },

                    animateScanComplete() {
                        const btn = document.querySelector('#scan-complete-btn');
                        if (btn) {
                            gsap.fromTo(btn,
                                { opacity: 0, y: 20, scale: 0.95 },
                                { opacity: 1, y: 0, scale: 1, duration: 0.6, ease: 'back.out(1.7)' }
                            );
                        }
                    },

                    /* --- STEP 1: Map & Competitors --- */
                    animateMapStep() {
                        const competitors = this.scanData.competitors || [];
                        // Pin drop for restaurant
                        gsap.fromTo('#main-pin', { y: -40, scale: 0, opacity: 0 }, { y: 0, scale: 1, opacity: 1, duration: 0.6, delay: 0.5, ease: 'bounce.out' });
                        // Competitor cards stagger
                        gsap.fromTo('.competitor-card', { opacity: 0, x: 60 }, { opacity: 1, x: 0, duration: 0.5, stagger: 0.4, delay: 1.5, ease: 'power3.out' });
                        // Competitor pins stagger
                        gsap.fromTo('.comp-pin', { y: -30, scale: 0, opacity: 0 }, { y: 0, scale: 1, opacity: 1, duration: 0.4, stagger: 0.5, delay: 2, ease: 'bounce.out' });
                    },

                    /* --- STEP 2: Google Business --- */
                    animateGoogleStep() {
                        const card = document.querySelector('#gbp-card');
                        if (card) {
                            gsap.fromTo(card,
                                { opacity: 0, y: 60, rotationX: 8 },
                                { opacity: 1, y: 0, rotationX: 0, duration: 0.8, delay: 0.4, ease: 'power3.out' }
                            );
                        }
                        // Animate stars one by one
                        const rating = parseFloat(this.scanData.google_rating) || 0;
                        const totalStars = Math.round(rating);
                        for (let i = 0; i < totalStars; i++) {
                            setTimeout(() => { this.starsLit = i + 1; }, 1200 + i * 400);
                        }
                        // Count up reviews
                        const targetReviews = parseInt(this.scanData.google_reviews_count) || 0;
                        if (targetReviews > 0) {
                            const startTime = Date.now();
                            const duration = 2500;
                            const delay = 2000;
                            setTimeout(() => {
                                const tick = () => {
                                    const elapsed = Date.now() - startTime - delay;
                                    const progress = Math.min(elapsed / duration, 1);
                                    this.reviewCountDisplay = Math.round(progress * targetReviews);
                                    if (progress < 1) requestAnimationFrame(tick);
                                };
                                requestAnimationFrame(tick);
                            }, delay);
                        }
                        // Count up rating
                        const ratingTarget = parseFloat(this.scanData.google_rating) || 0;
                        if (ratingTarget > 0) {
                            const startTime2 = Date.now();
                            setTimeout(() => {
                                const tick2 = () => {
                                    const elapsed = Date.now() - startTime2 - 1000;
                                    const progress = Math.min(elapsed / 1500, 1);
                                    this.googleRatingDisplay = (progress * ratingTarget).toFixed(1);
                                    if (progress < 1) requestAnimationFrame(tick2);
                                };
                                requestAnimationFrame(tick2);
                            }, 1000);
                        }
                    },

                    /* --- STEP (Yelp): Yelp Profile --- */
                    animateYelpStep() {
                        const card = document.querySelector('#yelp-card');
                        if (card) {
                            gsap.fromTo(card,
                                { opacity: 0, y: 60, rotationX: 8 },
                                { opacity: 1, y: 0, rotationX: 0, duration: 0.8, delay: 0.4, ease: 'power3.out' }
                            );
                        }
                        // Animate Yelp stars one by one
                        const rating = parseFloat(this.scanData.yelp_rating) || 0;
                        const totalStars = Math.round(rating);
                        for (let i = 0; i < totalStars; i++) {
                            setTimeout(() => { this.yelpStarsLit = i + 1; }, 1200 + i * 400);
                        }
                        // Count up Yelp reviews
                        const targetReviews = parseInt(this.scanData.yelp_reviews_count) || 0;
                        if (targetReviews > 0) {
                            const startTime = Date.now();
                            const duration = 2000;
                            setTimeout(() => {
                                const tick = () => {
                                    const elapsed = Date.now() - startTime - 1500;
                                    const progress = Math.min(elapsed / duration, 1);
                                    this.yelpReviewCountDisplay = Math.round(progress * targetReviews);
                                    if (progress < 1) requestAnimationFrame(tick);
                                };
                                requestAnimationFrame(tick);
                            }, 1500);
                        }
                        // Count up rating
                        const ratingTarget = parseFloat(this.scanData.yelp_rating) || 0;
                        if (ratingTarget > 0) {
                            const startTime2 = Date.now();
                            setTimeout(() => {
                                const tick2 = () => {
                                    const elapsed = Date.now() - startTime2 - 800;
                                    const progress = Math.min(elapsed / 1500, 1);
                                    this.yelpRatingDisplay = (progress * ratingTarget).toFixed(1);
                                    if (progress < 1) requestAnimationFrame(tick2);
                                };
                                requestAnimationFrame(tick2);
                            }, 800);
                        }
                    },

                    /* --- STEP: Reviews --- */
                    animateReviewStep() {
                        const reviews = this.scanData.reviews || [];
                        if (reviews.length === 0) return;
                        this.activeReviewIndex = 0;
                        this.typewriterIndex = 0;
                        this.typewriterText = '';
                        gsap.fromTo('.review-cards-container', { opacity: 0, y: 20 }, { opacity: 1, y: 0, duration: 0.5, delay: 0.3 });
                        this.typeReview(reviews, 0);
                    },

                    typeReview(reviews, idx) {
                        if (idx >= reviews.length || idx >= 3) return;
                        this.activeReviewIndex = idx;
                        const text = (reviews[idx].comment || '').substring(0, 120);
                        this.typewriterText = '';
                        this.typewriterIndex = 0;
                        const typeChar = () => {
                            if (this.typewriterIndex < text.length && this.currentStep === 2) {
                                this.typewriterText += text[this.typewriterIndex];
                                this.typewriterIndex++;
                                setTimeout(typeChar, 25);
                            } else if (idx < 2 && idx < reviews.length - 1) {
                                setTimeout(() => this.typeReview(reviews, idx + 1), 800);
                            }
                        };
                        setTimeout(typeChar, 600);
                    },

                    /* --- STEP 4: Photos --- */
                    animatePhotoStep() {
                        const photos = this.scanData.photos || [];
                        gsap.fromTo('.photo-item',
                            { opacity: 0, scale: 0.7, rotation: -3 },
                            { opacity: 1, scale: 1, rotation: 0, duration: 0.5, stagger: 0.35, delay: 0.5, ease: 'back.out(1.7)' }
                        );
                        // Quality check overlays
                        photos.forEach((_, i) => {
                            setTimeout(() => { this.photoCheckIndex = i; }, 1500 + i * 500);
                        });
                    },

                    /* --- STEP 5: Website --- */
                    animateWebsiteStep() {
                        gsap.fromTo('#browser-mockup',
                            { opacity: 0, y: 40, scale: 0.95 },
                            { opacity: 1, y: 0, scale: 1, duration: 0.7, delay: 0.3, ease: 'power3.out' }
                        );
                    },

                    /* --- STEP 6: Mobile --- */
                    animateMobileStep() {
                        gsap.fromTo('#phone-mockup',
                            { opacity: 0, y: 40, scale: 0.9 },
                            { opacity: 1, y: 0, scale: 1, duration: 0.7, delay: 0.3, ease: 'power3.out' }
                        );
                    }
                }"
                x-init="init()"
                class="bg-[#0B0B0B] rounded-xl -mt-12 relative z-10 overflow-hidden gold-border-glow"
                style="border: 1px solid rgba(212,175,55,0.15);"
            >
                {{-- Restaurant Header --}}
                <div class="border-b border-[#2A2A2A] px-6 py-4" style="background: linear-gradient(180deg, #1A1A1A 0%, #0B0B0B 100%);">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="font-playfair text-xl font-bold text-[#F5F5F5]">{{ $selectedRestaurant['name'] }}</h2>
                            <p class="font-poppins text-sm text-[#CCCCCC]/60">
                                {{ $selectedRestaurant['city'] }}, {{ $selectedRestaurant['state'] }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-[#D4AF37] rounded-full animate-pulse"></div>
                            <span class="font-poppins text-sm font-medium text-[#D4AF37]" x-show="!isComplete">Scanning...</span>
                            <span class="font-poppins text-sm font-medium text-[#D4AF37]" x-show="isComplete" x-cloak>Complete</span>
                        </div>
                    </div>
                </div>

                {{-- Two-column layout: sidebar + main --}}
                <div class="flex flex-col md:flex-row">

                    {{-- LEFT SIDEBAR: Step checklist --}}
                    {{-- Mobile: compact horizontal bar at top --}}
                    <div class="md:hidden w-full border-b border-[#2A2A2A] px-4 py-3">
                        <div class="flex items-center justify-between">
                            <template x-for="(step, idx) in steps" :key="'m'+idx">
                                <div class="flex items-center">
                                    <div :id="'step-circle-mobile-' + idx"
                                        class="step-circle w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 transition-all duration-400"
                                        :class="{
                                            'bg-[#2A2A2A]': step.status === 'pending',
                                            'bg-[#D4AF37]/20 active': step.status === 'active',
                                            'bg-[#D4AF37] complete': step.status === 'complete'
                                        }">
                                        <span x-show="step.status === 'pending'" class="text-[#CCCCCC]/30 font-poppins text-[10px] font-bold" x-text="idx + 1"></span>
                                        <svg x-show="step.status === 'active'" class="w-3.5 h-3.5 text-[#D4AF37] animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                        <svg x-show="step.status === 'complete'" x-cloak class="w-3.5 h-3.5 text-[#0B0B0B]" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div x-show="idx < steps.length - 1" class="w-3 h-0.5 mx-0.5"
                                        :class="step.status === 'complete' ? 'bg-[#D4AF37]' : 'bg-[#2A2A2A]'"></div>
                                </div>
                            </template>
                        </div>
                        <p class="font-poppins text-[10px] text-[#D4AF37]/70 mt-1 text-center" x-show="currentStep >= 0" x-text="stepLabels[currentStep] + '...'"></p>
                    </div>
                    {{-- Desktop: vertical sidebar --}}
                    <div class="hidden md:block md:w-1/4 w-full md:border-r border-[#2A2A2A] p-4 md:p-6">
                        <div class="flex flex-col space-y-0">
                            <template x-for="(step, idx) in steps" :key="idx">
                                <div class="flex flex-row items-start">
                                    <div class="flex flex-col items-center">
                                        {{-- Circle --}}
                                        <div :id="'step-circle-' + idx"
                                            class="step-circle w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 transition-all duration-400"
                                            :class="{
                                                'bg-[#2A2A2A]': step.status === 'pending',
                                                'bg-[#D4AF37]/20 active': step.status === 'active',
                                                'bg-[#D4AF37] complete': step.status === 'complete'
                                            }">
                                            {{-- Pending --}}
                                            <span x-show="step.status === 'pending'" class="text-[#CCCCCC]/30 font-poppins text-xs font-bold" x-text="idx + 1"></span>
                                            {{-- Active: spinner --}}
                                            <svg x-show="step.status === 'active'" class="w-4 h-4 text-[#D4AF37] animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                            </svg>
                                            {{-- Complete: checkmark --}}
                                            <svg x-show="step.status === 'complete'" x-cloak class="w-4 h-4 text-[#0B0B0B]" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        {{-- Connector line --}}
                                        <div x-show="idx < steps.length - 1"
                                            class="step-connector w-0.5 h-5 my-0.5"
                                            :class="step.status === 'complete' ? 'bg-[#D4AF37]' : 'bg-[#2A2A2A]'"></div>
                                    </div>
                                    {{-- Label --}}
                                    <div class="ml-3 text-left mb-1">
                                        <p class="font-poppins text-xs font-medium transition-colors duration-300"
                                            :class="{
                                                'text-[#CCCCCC]/30': step.status === 'pending',
                                                'text-[#F5F5F5]': step.status === 'active',
                                                'text-[#D4AF37]': step.status === 'complete'
                                            }"
                                            x-text="stepLabels[idx]"></p>
                                        <p x-show="step.status === 'active'" class="font-poppins text-[10px] text-[#D4AF37]/70 mt-0.5">Scanning...</p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- RIGHT MAIN AREA: Step content --}}
                    <div class="md:w-3/4 w-full p-4 md:p-8">
                        <div class="scan-content-area relative">

                            {{-- STEP 0: Restaurant & Competitors --}}
                            <div class="scan-step-content step-0-content" style="display:none; opacity:0;">
                                <h3 class="font-playfair text-lg font-semibold text-[#F5F5F5] mb-4">Mapping Competitive Landscape</h3>
                                <div class="flex flex-col lg:flex-row gap-4">
                                    {{-- Map area --}}
                                    <div class="flex-1 map-dark-bg rounded-xl relative overflow-hidden" style="min-height: 240px;">
                                        {{-- Main restaurant pin --}}
                                        <div id="main-pin" class="absolute" style="top: 45%; left: 48%; opacity: 0;">
                                            <div class="flex flex-col items-center">
                                                <div class="w-5 h-5 bg-[#D4AF37] rounded-full border-2 border-[#E8C67A] shadow-lg" style="box-shadow: 0 0 12px rgba(212,175,55,0.6);"></div>
                                                <div class="w-0 h-0 border-l-[5px] border-r-[5px] border-t-[8px] border-l-transparent border-r-transparent border-t-[#D4AF37]"></div>
                                                <span class="font-poppins text-[10px] text-[#D4AF37] font-bold mt-1 whitespace-nowrap bg-[#0B0B0B]/80 px-2 py-0.5 rounded"
                                                    x-text="(scanData.restaurant || {}).name || 'Your Restaurant'"></span>
                                            </div>
                                        </div>
                                        {{-- Competitor pins scattered --}}
                                        <template x-for="(comp, ci) in (scanData.competitors || []).slice(0, 5)" :key="ci">
                                            <div class="comp-pin absolute"
                                                :style="'top:' + (20 + ci * 14) + '%; left:' + (15 + ci * 16) + '%; opacity:0;'">
                                                <div class="flex flex-col items-center">
                                                    <div class="w-3 h-3 bg-[#666] rounded-full border border-[#888]"></div>
                                                    <span class="font-poppins text-[9px] text-[#888] mt-0.5 whitespace-nowrap bg-[#0B0B0B]/60 px-1 rounded" x-text="comp.name ? comp.name.substring(0,20) : ''"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    {{-- Competitor list --}}
                                    <div class="w-full lg:w-56 space-y-2">
                                        <p class="font-poppins text-xs text-[#CCCCCC]/50 uppercase tracking-wider mb-2">Nearby Competitors</p>
                                        <template x-for="(comp, ci) in (scanData.competitors || []).slice(0, 5)" :key="'cc'+ci">
                                            <div class="competitor-card bg-[#1A1A1A] rounded-lg p-3 border border-[#2A2A2A]" style="opacity:0; transform: translateX(60px);">
                                                <p class="font-poppins text-sm text-[#F5F5F5] font-medium truncate" x-text="comp.name"></p>
                                                <div class="flex items-center justify-between mt-1">
                                                    <span class="text-[#D4AF37] text-xs" x-text="comp.rating ? (comp.rating + ' stars') : 'N/A'"></span>
                                                    <span class="text-[#CCCCCC]/40 text-xs" x-text="(comp.reviews || 0) + ' reviews'"></span>
                                                </div>
                                            </div>
                                        </template>
                                        <div x-show="!scanData.competitors || scanData.competitors.length === 0" class="bg-[#1A1A1A] rounded-lg p-3 border border-[#2A2A2A]">
                                            <p class="font-poppins text-sm text-[#CCCCCC]/40">No competitors found nearby</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- STEP 1: Google Business Profile --}}
                            <div class="scan-step-content step-1-content" style="display:none; opacity:0;">
                                <h3 class="font-playfair text-lg font-semibold text-[#F5F5F5] mb-4">Google Business Profile</h3>
                                <div class="flex justify-center">
                                    <div id="gbp-card" class="bg-[#1A1A1A] rounded-xl p-6 border border-[#2A2A2A] max-w-md w-full" style="opacity:0;">
                                        {{-- Restaurant image or placeholder --}}
                                        <div class="w-full h-32 bg-[#2A2A2A] rounded-lg mb-4 overflow-hidden flex items-center justify-center">
                                            <template x-if="scanData.restaurant && scanData.restaurant.image">
                                                <img :src="scanData.restaurant.image" class="w-full h-full object-cover" alt="">
                                            </template>
                                            <template x-if="!scanData.restaurant || !scanData.restaurant.image">
                                                <div class="text-[#CCCCCC]/20">
                                                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                                                </div>
                                            </template>
                                        </div>
                                        {{-- Name & Address --}}
                                        <h4 class="font-playfair text-xl font-bold text-[#F5F5F5]" x-text="(scanData.restaurant || {}).name || ''"></h4>
                                        <p class="font-poppins text-sm text-[#CCCCCC]/50 mt-1" x-text="((scanData.restaurant || {}).address || '') + ', ' + ((scanData.restaurant || {}).city || '') + ', ' + ((scanData.restaurant || {}).state || '')"></p>
                                        {{-- Stars --}}
                                        <div class="flex items-center mt-4 space-x-1">
                                            <template x-for="s in 5" :key="'star'+s">
                                                <svg class="w-6 h-6 star-fill transition-colors duration-300" :class="s <= starsLit ? 'lit' : ''" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            </template>
                                            <span class="font-poppins text-lg font-bold text-[#D4AF37] ml-2" x-text="googleRatingDisplay"></span>
                                        </div>
                                        {{-- Review count --}}
                                        <div class="mt-3 flex items-center">
                                            <svg class="w-5 h-5 text-[#CCCCCC]/40 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                            <span class="font-poppins text-2xl font-bold text-[#F5F5F5]" x-text="reviewCountDisplay"></span>
                                            <span class="font-poppins text-sm text-[#CCCCCC]/40 ml-2">Google Reviews</span>
                                        </div>
                                        {{-- Phone & Website --}}
                                        <div class="mt-3 space-y-1">
                                            <p class="font-poppins text-xs text-[#CCCCCC]/40" x-show="scanData.restaurant && scanData.restaurant.phone" x-text="'Phone: ' + ((scanData.restaurant || {}).phone || '')"></p>
                                            <p class="font-poppins text-xs text-[#D4AF37]/60" x-show="scanData.restaurant && scanData.restaurant.website" x-text="(scanData.restaurant || {}).website || ''"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- STEP 2: Yelp Profile --}}
                            <div class="scan-step-content step-2-content" style="display:none; opacity:0;">
                                <h3 class="font-playfair text-lg font-semibold text-[#F5F5F5] mb-4">Yelp Profile</h3>
                                <div class="flex justify-center">
                                    <div id="yelp-card" class="bg-[#1A1A1A] rounded-xl p-6 border border-[#2A2A2A] max-w-md w-full" style="opacity:0;">
                                        {{-- Yelp branding bar --}}
                                        <div class="flex items-center gap-3 mb-4 pb-3 border-b border-[#2A2A2A]">
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: #AF0606;">
                                                <span class="text-white font-bold text-lg">Y</span>
                                            </div>
                                            <div>
                                                <span class="font-poppins text-sm font-bold" style="color: #AF0606;">Yelp</span>
                                                <span class="font-poppins text-xs text-[#CCCCCC]/40 ml-2">Business Profile</span>
                                            </div>
                                        </div>
                                        {{-- Restaurant image or placeholder --}}
                                        <div class="w-full h-32 bg-[#2A2A2A] rounded-lg mb-4 overflow-hidden flex items-center justify-center">
                                            <template x-if="scanData.restaurant && scanData.restaurant.image">
                                                <img :src="scanData.restaurant.image" class="w-full h-full object-cover" alt="">
                                            </template>
                                            <template x-if="!scanData.restaurant || !scanData.restaurant.image">
                                                <div class="text-[#CCCCCC]/20">
                                                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                                                </div>
                                            </template>
                                        </div>
                                        {{-- Name --}}
                                        <h4 class="font-playfair text-xl font-bold text-[#F5F5F5]" x-text="(scanData.restaurant || {}).name || ''"></h4>
                                        <p class="font-poppins text-sm text-[#CCCCCC]/50 mt-1" x-text="((scanData.restaurant || {}).city || '') + ', ' + ((scanData.restaurant || {}).state || '')"></p>
                                        {{-- Yelp Stars --}}
                                        <div class="flex items-center mt-4 space-x-1">
                                            <template x-for="s in 5" :key="'ystar'+s">
                                                <svg class="w-6 h-6 transition-colors duration-300" :style="s <= yelpStarsLit ? 'color: #AF0606' : 'color: #2A2A2A'" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            </template>
                                            <span class="font-poppins text-lg font-bold ml-2" style="color: #AF0606;" x-text="yelpRatingDisplay"></span>
                                        </div>
                                        {{-- Yelp Review count --}}
                                        <div class="mt-3 flex items-center">
                                            <svg class="w-5 h-5 text-[#CCCCCC]/40 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                            <span class="font-poppins text-2xl font-bold text-[#F5F5F5]" x-text="yelpReviewCountDisplay"></span>
                                            <span class="font-poppins text-sm text-[#CCCCCC]/40 ml-2">Yelp Reviews</span>
                                        </div>
                                        {{-- Yelp status --}}
                                        <div class="mt-4 pt-3 border-t border-[#2A2A2A]">
                                            <template x-if="scanData.yelp_rating && scanData.yelp_rating > 0">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-5 h-5 rounded-full bg-emerald-500/20 flex items-center justify-center">
                                                        <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    </div>
                                                    <span class="font-poppins text-xs text-emerald-400">Active Yelp listing found</span>
                                                </div>
                                            </template>
                                            <template x-if="!scanData.yelp_rating || scanData.yelp_rating == 0">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-5 h-5 rounded-full bg-amber-500/20 flex items-center justify-center">
                                                        <svg class="w-3 h-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                    </div>
                                                    <span class="font-poppins text-xs text-amber-400">No Yelp listing detected — claim yours</span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- STEP 3: Review Sentiment --}}
                            <div class="scan-step-content step-3-content" style="display:none; opacity:0;">
                                <h3 class="font-playfair text-lg font-semibold text-[#F5F5F5] mb-4">Review Sentiment Analysis</h3>
                                <div class="review-cards-container space-y-3" style="opacity:0;">
                                    <template x-if="scanData.reviews && scanData.reviews.length > 0">
                                        <div class="space-y-3">
                                            <template x-for="(rev, ri) in (scanData.reviews || []).slice(0, 3)" :key="'rev'+ri">
                                                <div class="bg-[#1A1A1A] rounded-lg p-4 border transition-all duration-300"
                                                    :class="ri === activeReviewIndex ? 'border-[#D4AF37]/40 shadow-lg shadow-[#D4AF37]/5' : 'border-[#2A2A2A]'">
                                                    <div class="flex items-center justify-between mb-2">
                                                        <span class="font-poppins text-sm font-semibold text-[#F5F5F5]" x-text="rev.name"></span>
                                                        <div class="flex space-x-0.5">
                                                            <template x-for="rs in 5" :key="'rs'+ri+'-'+rs">
                                                                <svg class="w-3.5 h-3.5" :class="rs <= (rev.rating || 0) ? 'text-[#D4AF37]' : 'text-[#2A2A2A]'" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                                </svg>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <p class="font-poppins text-sm text-[#CCCCCC]/70 leading-relaxed">
                                                        <template x-if="ri === activeReviewIndex">
                                                            <span>
                                                                <span x-text="typewriterText"></span>
                                                                <span class="tw-cursor" x-show="typewriterIndex < ((rev.comment || '').substring(0, 120)).length"></span>
                                                            </span>
                                                        </template>
                                                        <template x-if="ri < activeReviewIndex">
                                                            <span x-text="(rev.comment || '').substring(0, 120)"></span>
                                                        </template>
                                                        <template x-if="ri > activeReviewIndex">
                                                            <span class="text-[#CCCCCC]/20">Waiting to analyze...</span>
                                                        </template>
                                                    </p>
                                                    <p class="font-poppins text-[10px] text-[#CCCCCC]/30 mt-2" x-text="rev.date"></p>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="!scanData.reviews || scanData.reviews.length === 0">
                                        <div class="bg-[#1A1A1A] rounded-lg p-6 border border-[#2A2A2A] text-center">
                                            <svg class="w-10 h-10 text-[#CCCCCC]/20 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                            <p class="font-poppins text-sm text-[#CCCCCC]/40">No reviews found on FAMER</p>
                                            <p class="font-poppins text-xs text-[#D4AF37]/60 mt-1">Encourage your customers to leave reviews to improve your score</p>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- STEP 4: Photo Quality --}}
                            <div class="scan-step-content step-4-content" style="display:none; opacity:0;">
                                <h3 class="font-playfair text-lg font-semibold text-[#F5F5F5] mb-4">Photo Quality Assessment</h3>
                                <template x-if="scanData.photos && scanData.photos.length > 0">
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                        <template x-for="(photo, pi) in (scanData.photos || []).slice(0, 8)" :key="'ph'+pi">
                                            <div class="photo-item relative rounded-lg overflow-hidden" style="opacity:0; aspect-ratio: 1;">
                                                <img :src="photo" class="w-full h-full object-cover" alt="" loading="lazy">
                                                {{-- Quality check overlay --}}
                                                <div x-show="photoCheckIndex >= pi" x-cloak
                                                    class="absolute inset-0 flex items-center justify-center"
                                                    style="background: rgba(0,0,0,0.5);">
                                                    <div style="animation: quality-check 0.4s ease-out forwards;">
                                                        <svg class="w-8 h-8 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="!scanData.photos || scanData.photos.length === 0">
                                    <div class="bg-[#1A1A1A] rounded-lg p-6 border border-[#2A2A2A] text-center">
                                        <svg class="w-10 h-10 text-[#CCCCCC]/20 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <p class="font-poppins text-sm text-[#CCCCCC]/40">No photos available</p>
                                        <p class="font-poppins text-xs text-[#D4AF37]/60 mt-1">Add high-quality photos to increase your FAMER Score</p>
                                    </div>
                                </template>
                            </div>

                            {{-- STEP 5: Website Analysis --}}
                            <div class="scan-step-content step-5-content" style="display:none; opacity:0;">
                                <h3 class="font-playfair text-lg font-semibold text-[#F5F5F5] mb-4">Website Analysis</h3>
                                <div class="flex justify-center">
                                    <div id="browser-mockup" class="browser-chrome max-w-lg w-full" style="opacity:0;">
                                        <div class="top-bar">
                                            <div class="browser-dot" style="background:#ff5f57;"></div>
                                            <div class="browser-dot" style="background:#febc2e;"></div>
                                            <div class="browser-dot" style="background:#28c840;"></div>
                                            <div class="flex-1 mx-3 bg-[#2A2A2A] rounded-md px-3 py-1.5 text-xs font-poppins text-[#CCCCCC]/40 truncate"
                                                x-text="(scanData.restaurant && scanData.restaurant.website) ? scanData.restaurant.website : 'No website detected'">
                                            </div>
                                        </div>
                                        <div class="relative" style="min-height: 220px; background: #1A1A1A;">
                                            {{-- Scan line --}}
                                            <div class="scan-line-effect"></div>
                                            {{-- Content --}}
                                            <div class="flex flex-col items-center justify-center p-8" style="min-height: 220px;">
                                                <template x-if="scanData.restaurant && scanData.restaurant.website">
                                                    <div class="text-center">
                                                        <div class="w-14 h-14 rounded-full bg-[#1F3D2B]/30 flex items-center justify-center mx-auto mb-3">
                                                            <svg class="w-7 h-7 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                            </svg>
                                                        </div>
                                                        <p class="font-poppins text-sm font-semibold text-green-400">Website Found</p>
                                                        <p class="font-poppins text-xs text-[#CCCCCC]/40 mt-1" x-text="scanData.restaurant.website"></p>
                                                    </div>
                                                </template>
                                                <template x-if="!scanData.restaurant || !scanData.restaurant.website">
                                                    <div class="text-center">
                                                        <div class="w-14 h-14 rounded-full bg-[#8B1E1E]/20 flex items-center justify-center mx-auto mb-3">
                                                            <svg class="w-7 h-7 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                            </svg>
                                                        </div>
                                                        <p class="font-poppins text-sm font-semibold text-red-400">No Website Detected</p>
                                                        <p class="font-poppins text-xs text-[#CCCCCC]/40 mt-1">A website is essential for online visibility</p>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- STEP 6: Mobile Experience --}}
                            <div class="scan-step-content step-6-content" style="display:none; opacity:0;">
                                <h3 class="font-playfair text-lg font-semibold text-[#F5F5F5] mb-4">Mobile Experience</h3>
                                <div class="flex justify-center">
                                    <div id="phone-mockup" class="phone-frame" style="opacity:0;">
                                        <div class="phone-notch"></div>
                                        <div class="phone-screen relative">
                                            <div class="scan-line-effect"></div>
                                            <div class="flex flex-col items-center justify-center p-4" style="min-height: 300px;">
                                                <template x-if="scanData.restaurant && scanData.restaurant.website">
                                                    <div class="text-center">
                                                        <div class="w-12 h-12 rounded-full bg-[#1F3D2B]/30 flex items-center justify-center mx-auto mb-3">
                                                            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                            </svg>
                                                        </div>
                                                        <p class="font-poppins text-xs font-semibold text-green-400">Mobile Responsive</p>
                                                        <p class="font-poppins text-[10px] text-[#CCCCCC]/40 mt-1">Site appears mobile-friendly</p>
                                                    </div>
                                                </template>
                                                <template x-if="!scanData.restaurant || !scanData.restaurant.website">
                                                    <div class="text-center">
                                                        <div class="w-12 h-12 rounded-full bg-[#8B1E1E]/20 flex items-center justify-center mx-auto mb-3">
                                                            <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                            </svg>
                                                        </div>
                                                        <p class="font-poppins text-xs font-semibold text-red-400">No Mobile Presence</p>
                                                        <p class="font-poppins text-[10px] text-[#CCCCCC]/40 mt-1">60% of customers search on mobile</p>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- Bottom progress bar & timer --}}
                        <div class="mt-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-poppins text-xs text-[#CCCCCC]/50">Overall Progress</span>
                                <span class="font-poppins text-xs text-[#CCCCCC]/40" x-show="!isComplete">
                                    Scanning... <span x-text="secondsRemaining"></span>s remaining
                                </span>
                                <span class="font-poppins text-xs text-[#D4AF37] font-medium" x-show="isComplete" x-cloak>Analysis Complete</span>
                            </div>
                            <div class="w-full bg-[#2A2A2A] rounded-full h-2 overflow-hidden">
                                <div class="h-2 rounded-full shimmer-bar transition-all duration-300 ease-out"
                                    :style="'width:' + overallProgress + '%'"></div>
                            </div>
                            <div class="text-right mt-1">
                                <span class="font-poppins text-xs font-medium text-[#D4AF37]" x-text="Math.round(overallProgress) + '%'"></span>
                            </div>
                        </div>

                        {{-- Complete button --}}
                        <div x-show="isComplete" x-cloak class="mt-8 text-center">
                            <div id="scan-complete-btn" style="opacity:0;">
                                <div class="mb-5 inline-flex items-center px-5 py-2.5 bg-[#D4AF37]/15 text-[#D4AF37] rounded-full shimmer-gold" style="background-size: 200% 100%;">
                                    <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="font-poppins font-semibold text-lg">Analysis Complete</span>
                                </div>
                                <button
                                    wire:click="completeAnalysis"
                                    class="w-full max-w-md mx-auto block py-5 px-10 font-poppins font-bold rounded-xl text-xl transition-all transform hover:scale-[1.03] text-[#0B0B0B] pulse-gold-ring"
                                    style="background: linear-gradient(135deg, #D4AF37, #E8C67A); box-shadow: 0 4px 30px rgba(212,175,55,0.4);"
                                    onmouseover="this.style.boxShadow='0 8px 40px rgba(212,175,55,0.6)'"
                                    onmouseout="this.style.boxShadow='0 4px 30px rgba(212,175,55,0.4)'"
                                >
                                    View Full Results
                                    <svg class="w-6 h-6 inline-block ml-2 -mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ============================================================ --}}
        {{-- SEARCH SECTION                                                --}}
        {{-- ============================================================ --}}
        @if (!$scoreData && !$showAnalysis)
            <div class="max-w-4xl mx-auto">
                <div class="bg-[#1A1A1A] rounded-xl -mt-12 relative z-10 p-6 md:p-8 gold-border-glow" style="border: 1px solid rgba(212,175,55,0.1);">
                    <h2 class="font-playfair text-2xl font-bold text-[#F5F5F5] mb-6 text-center">
                        Find Your Restaurant
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <label for="searchName" class="block font-poppins text-sm font-medium text-[#CCCCCC] mb-1">
                                Restaurant Name
                            </label>
                            <input
                                type="text"
                                id="searchName"
                                wire:model="searchName"
                                wire:keydown.enter="search"
                                class="w-full rounded-lg bg-[#2A2A2A] border-[#2A2A2A] text-[#F5F5F5] placeholder-[#CCCCCC]/40 shadow-sm focus:border-[#D4AF37] focus:ring-[#D4AF37] text-lg font-poppins"
                                placeholder="e.g. Taqueria El Mexicano"
                                autofocus
                            >
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="searchCity" class="block font-poppins text-sm font-medium text-[#CCCCCC] mb-1">
                                    City
                                </label>
                                <input
                                    type="text"
                                    id="searchCity"
                                    wire:model="searchCity"
                                    wire:keydown.enter="search"
                                    class="w-full rounded-lg bg-[#2A2A2A] border-[#2A2A2A] text-[#F5F5F5] placeholder-[#CCCCCC]/40 shadow-sm focus:border-[#D4AF37] focus:ring-[#D4AF37] font-poppins"
                                    placeholder="e.g. Los Angeles"
                                >
                            </div>

                            <div>
                                <label for="searchState" class="block font-poppins text-sm font-medium text-[#CCCCCC] mb-1">
                                    State
                                </label>
                                <select
                                    id="searchState"
                                    wire:model="searchState"
                                    class="w-full rounded-lg bg-[#2A2A2A] border-[#2A2A2A] text-[#F5F5F5] shadow-sm focus:border-[#D4AF37] focus:ring-[#D4AF37] font-poppins"
                                >
                                    <option value="">All States</option>
                                    @foreach($states as $state)
                                        <option value="{{ $state->code }}">{{ $state->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <button
                            type="button"
                            wire:click="search"
                            wire:loading.attr="disabled"
                            class="w-full py-4 font-poppins font-bold rounded-lg text-lg transition-all disabled:opacity-50 text-[#0B0B0B]"
                            style="background: linear-gradient(135deg, #D4AF37, #E8C67A); box-shadow: 0 4px 15px rgba(212,175,55,0.2);"
                            onmouseover="this.style.boxShadow='0 6px 25px rgba(212,175,55,0.4)'"
                            onmouseout="this.style.boxShadow='0 4px 15px rgba(212,175,55,0.2)'"
                        >
                            <span wire:loading.remove wire:target="search">
                                Get FAMER Score
                            </span>
                            <span wire:loading wire:target="search" class="inline-flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-[#0B0B0B]" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Searching...
                            </span>
                        </button>
                    </div>

                    {{-- Search Results --}}
                    @if ($hasSearched && !empty($searchResults))
                        <div class="mt-8 border-t border-[#2A2A2A] pt-6">
                            <h3 class="font-playfair text-lg font-semibold text-[#F5F5F5] mb-4">
                                Results ({{ count($searchResults) }})
                            </h3>

                            <div class="space-y-3">
                                @foreach ($searchResults as $result)
                                    <button
                                        wire:click="selectResult('{{ $result['id'] }}')"
                                        wire:loading.attr="disabled"
                                        class="w-full text-left p-4 rounded-lg transition-all border hover:border-[#D4AF37]/50 hover:shadow-lg hover:shadow-[#D4AF37]/5 {{ $result['source'] === 'famer' ? 'bg-[#D4AF37]/5 border-[#D4AF37]/20' : 'bg-[#2A2A2A] border-[#2A2A2A]' }}"
                                    >
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                                    <h4 class="font-poppins font-semibold text-[#F5F5F5] truncate">{{ $result['name'] }}</h4>
                                                    @if ($result['source'] === 'famer')
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-[#D4AF37]/20 text-[#D4AF37]">
                                                            {{ $result['source_label'] }}
                                                        </span>
                                                    @elseif ($result['source'] === 'yelp')
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-[#8B1E1E]/20 text-red-400">
                                                            {{ $result['source_label'] }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-900/30 text-blue-400">
                                                            {{ $result['source_label'] }}
                                                        </span>
                                                    @endif
                                                    @if ($result['is_claimed'] ?? false)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-[#1F3D2B]/30 text-green-400">
                                                            Verified
                                                        </span>
                                                    @endif
                                                    @if ($result['has_score'] ?? false)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-[#D4AF37]/10 text-[#D4AF37]">
                                                            Score: {{ $result['existing_score'] }} ({{ $result['existing_grade'] }})
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="font-poppins text-sm text-[#CCCCCC]/60 truncate">
                                                    {{ $result['address'] }}, {{ $result['city'] }}, {{ $result['state'] }}
                                                </p>
                                                @if ($result['rating'])
                                                    <div class="flex items-center mt-1 text-sm">
                                                        <span class="text-[#D4AF37]">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                @if ($i <= round($result['rating']))
                                                                    <span>&#9733;</span>
                                                                @else
                                                                    <span class="text-[#2A2A2A]">&#9733;</span>
                                                                @endif
                                                            @endfor
                                                        </span>
                                                        <span class="ml-1 text-[#CCCCCC]/50 font-poppins">
                                                            {{ number_format($result['rating'], 1) }}
                                                            @if ($result['review_count'])
                                                                ({{ number_format($result['review_count']) }} reviews)
                                                            @endif
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            @if ($result['image_url'])
                                                <img src="{{ $result['image_url'] }}" alt="{{ $result['name'] }}" class="w-16 h-16 object-cover rounded-lg ml-4 flex-shrink-0 ring-1 ring-[#2A2A2A]">
                                            @endif
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- ============================================================ --}}
        {{-- SCORE RESULT — Owner.com-inspired 3-column layout              --}}
        {{-- ============================================================ --}}
        @if ($scoreData && !$showAnalysis)
            @php
                $overallScore = $scoreData['overall_score'];
                $letterGrade = $scoreData['letter_grade'];
                $problemCount = count($scoreData['top_recommendations'] ?? []);
                // Estimated monthly gain: lower score = higher potential
                $estimatedGain = max(200, round((100 - $overallScore) * 45));
                // Grade description
                $gradeDesc = match(true) {
                    $overallScore >= 85 => 'Excellent',
                    $overallScore >= 70 => 'Good',
                    $overallScore >= 50 => 'Needs Work',
                    default => 'Poor',
                };
                // Grade color class
                $gradeColorClass = match(true) {
                    $overallScore >= 85 => 'text-[#D4AF37]',
                    $overallScore >= 70 => 'text-[#E8C67A]',
                    $overallScore >= 50 => 'text-amber-400',
                    default => 'text-red-400',
                };
                // Category aggregation for the 3-bar display
                $cats = $scoreData['categories'] ?? [];
                $profilePresence = 0; $profilePresenceMax = 45;
                $custEngagement = 0; $custEngagementMax = 35;
                $digitalMenu = 0; $digitalMenuMax = 20;
                foreach ($cats as $cat) {
                    $catName = strtolower($cat['name'] ?? '');
                    $catWeight = $cat['weight'] ?? 0;
                    $catScoreWeighted = round(($cat['score'] / 100) * $catWeight);
                    if (str_contains($catName, 'profile') || str_contains($catName, 'presence') || str_contains($catName, 'online')) {
                        $profilePresence += $catScoreWeighted;
                    } elseif (str_contains($catName, 'engagement') || str_contains($catName, 'authentic') || str_contains($catName, 'review')) {
                        $custEngagement += $catScoreWeighted;
                    } elseif (str_contains($catName, 'digital') || str_contains($catName, 'menu') || str_contains($catName, 'website')) {
                        $digitalMenu += $catScoreWeighted;
                    } else {
                        // Distribute evenly if unmatched
                        $profilePresence += round($catScoreWeighted * 0.4);
                        $custEngagement += round($catScoreWeighted * 0.35);
                        $digitalMenu += round($catScoreWeighted * 0.25);
                    }
                }
                $profilePresence = min($profilePresence, $profilePresenceMax);
                $custEngagement = min($custEngagement, $custEngagementMax);
                $digitalMenu = min($digitalMenu, $digitalMenuMax);
                // Competitor data from scan
                $competitors = $scanStepData['competitors'] ?? [];
                $restaurantCity = $selectedRestaurant['city'] ?? 'your city';
                $restaurantState = $selectedRestaurant['state'] ?? '';
                $restaurantName = $selectedRestaurant['name'] ?? 'Your Restaurant';
                // Determine primary category for keyword table
                $primaryCategory = 'Mexican restaurant';
            @endphp
            <div
                x-data="{
                    scoreAnimated: 0,
                    showGrade: false,
                    showColumns: false,
                    showCategories: false,
                    showKeywords: false,
                    showCTA: false,
                    showConfetti: false,
                    targetScore: {{ $overallScore }},
                    circleDrawn: false,
                    init() {
                        this.$nextTick(() => {
                            // Animate score circle SVG stroke with gradient
                            const circle = document.querySelector('#score-circle-progress');
                            if (circle) {
                                const circumference = 2 * Math.PI * 88;
                                const target = ({{ $overallScore }} / 100) * circumference;
                                gsap.fromTo(circle,
                                    { strokeDasharray: '0 ' + circumference },
                                    { strokeDasharray: target + ' ' + circumference, duration: 2.5, ease: 'power3.out', delay: 0.3 }
                                );
                            }

                            // Count up score
                            const startTime = Date.now();
                            const duration = 2200;
                            const tick = () => {
                                const progress = Math.min((Date.now() - startTime) / duration, 1);
                                const eased = 1 - Math.pow(1 - progress, 3);
                                this.scoreAnimated = Math.round(eased * this.targetScore);
                                if (progress < 1) requestAnimationFrame(tick);
                                else {
                                    this.scoreAnimated = this.targetScore;
                                    if (this.targetScore > 80) {
                                        this.showConfetti = true;
                                        setTimeout(() => { this.showConfetti = false; }, 3500);
                                    }
                                }
                            };
                            setTimeout(() => requestAnimationFrame(tick), 400);

                            // Show grade with bounce
                            setTimeout(() => {
                                this.showGrade = true;
                                const gradeEl = document.querySelector('#grade-text');
                                if (gradeEl) {
                                    gsap.fromTo(gradeEl, { scale: 0, opacity: 0 }, { scale: 1, opacity: 1, duration: 0.6, ease: 'back.out(2)' });
                                }
                            }, 2800);

                            // Show 3-column layout with stagger
                            setTimeout(() => {
                                this.showColumns = true;
                                gsap.fromTo('.result-column', { opacity: 0, y: 40 }, { opacity: 1, y: 0, duration: 0.5, stagger: 0.2, ease: 'power3.out' });
                            }, 3200);

                            // Problem items stagger
                            setTimeout(() => {
                                gsap.fromTo('.problem-item', { opacity: 0, x: -20 }, { opacity: 1, x: 0, duration: 0.35, stagger: 0.15, ease: 'power3.out' });
                            }, 3600);

                            // Competitor items stagger
                            setTimeout(() => {
                                gsap.fromTo('.comp-item', { opacity: 0, x: 20 }, { opacity: 1, x: 0, duration: 0.35, stagger: 0.12, ease: 'power3.out' });
                            }, 3800);

                            // Show category bars
                            setTimeout(() => {
                                this.showCategories = true;
                                gsap.fromTo('.cat-bar-item', { opacity: 0, x: -30 }, { opacity: 1, x: 0, duration: 0.4, stagger: 0.15, ease: 'power3.out' });
                            }, 4200);

                            // Show keyword table
                            setTimeout(() => {
                                this.showKeywords = true;
                                gsap.fromTo('.keyword-row', { opacity: 0, y: 15 }, { opacity: 1, y: 0, duration: 0.3, stagger: 0.1, ease: 'power3.out' });
                            }, 4800);

                            // Show CTA
                            setTimeout(() => {
                                this.showCTA = true;
                                gsap.fromTo('#final-cta', { opacity: 0, y: 30, scale: 0.97 }, { opacity: 1, y: 0, scale: 1, duration: 0.6, ease: 'back.out(1.7)' });
                            }, 5200);

                            // Red pulse for low scores
                            if (this.targetScore < 40) {
                                setTimeout(() => {
                                    const el = document.querySelector('#score-container');
                                    if (el) gsap.fromTo(el, { boxShadow: '0 0 0 0 rgba(139,30,30,0)' }, { boxShadow: '0 0 40px 10px rgba(139,30,30,0.3)', duration: 0.8, repeat: 2, yoyo: true });
                                }, 3000);
                            }
                        });
                    }
                }"
                class="max-w-6xl mx-auto bg-[#0B0B0B] rounded-xl -mt-12 relative z-10 overflow-hidden gold-border-glow"
                style="border: 1px solid rgba(212,175,55,0.15);"
            >
                {{-- Confetti overlay --}}
                <div x-show="showConfetti" x-cloak class="absolute inset-0 pointer-events-none overflow-hidden z-50">
                    <template x-for="i in 40" :key="'conf'+i">
                        <div class="confetti-piece absolute"
                            :style="'left:' + (Math.random() * 100) + '%; top:-10px; background:' + ['#D4AF37','#E8C67A','#FFF8DC','#D4AF37','#1F3D2B'][i % 5] + '; animation-duration:' + (2 + Math.random() * 2) + 's; animation-delay:' + (Math.random() * 0.5) + 's; width:' + (4 + Math.random() * 6) + 'px; height:' + (4 + Math.random() * 6) + 'px; border-radius:' + (Math.random() > 0.5 ? '50%' : '2px') + ';'">
                        </div>
                    </template>
                </div>

                {{-- Restaurant Header --}}
                <div class="border-b border-[#2A2A2A] px-6 py-4" style="background: linear-gradient(180deg, #1A1A1A 0%, #0B0B0B 100%);">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="font-playfair text-xl font-bold text-[#F5F5F5]">{{ $restaurantName }}</h2>
                            <p class="font-poppins text-sm text-[#CCCCCC]/60">
                                {{ $restaurantCity }}, {{ $restaurantState }}
                            </p>
                        </div>
                        <button
                            wire:click="resetSearch"
                            class="font-poppins text-[#D4AF37] hover:text-[#E8C67A] text-sm font-medium transition-colors"
                        >
                            New Search
                        </button>
                    </div>
                </div>

                <div class="p-6 md:p-8">
                    {{-- Partial Score Warning --}}
                    @if ($scoreData['is_partial'] ?? false)
                        <div class="mb-6 bg-[#D4AF37]/10 border border-[#D4AF37]/30 text-[#D4AF37] px-4 py-3 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <p class="font-poppins font-medium">Partial Score</p>
                                    <p class="font-poppins text-sm text-[#D4AF37]/80">{{ $scoreData['message'] ?? 'This score is based on public data. Add your restaurant to FAMER for a complete analysis.' }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- ======================================== --}}
                    {{-- 3-COLUMN LAYOUT: Score | Problems | Competitors --}}
                    {{-- ======================================== --}}
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10" x-show="showColumns || showGrade" x-cloak>

                        {{-- LEFT COLUMN: Score Circle --}}
                        <div class="result-column flex flex-col items-center text-center" style="opacity:0;">
                            <div id="score-container" class="relative inline-block mb-4">
                                {{-- Outer glow --}}
                                <div class="absolute inset-0 rounded-full glow-ring-anim" style="margin: -8px;"></div>

                                {{-- SVG Circle with gold gradient --}}
                                <svg class="w-44 h-44 md:w-52 md:h-52 transform -rotate-90" viewBox="0 0 200 200">
                                    <defs>
                                        <linearGradient id="scoreGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                            <stop offset="0%" stop-color="#D4AF37"/>
                                            <stop offset="50%" stop-color="#E8C67A"/>
                                            <stop offset="100%" stop-color="#D4AF37"/>
                                        </linearGradient>
                                    </defs>
                                    <circle cx="100" cy="100" r="88" stroke-width="10" stroke="#2A2A2A" fill="none"/>
                                    <circle cx="100" cy="100" r="94" stroke-width="1" stroke="rgba(212,175,55,0.15)" fill="none"/>
                                    <circle
                                        id="score-circle-progress"
                                        cx="100" cy="100" r="88"
                                        stroke-width="10"
                                        stroke="url(#scoreGradient)"
                                        fill="none"
                                        stroke-dasharray="0 553"
                                        stroke-linecap="round"
                                        style="filter: drop-shadow(0 0 8px rgba(212,175,55,0.4));"
                                    />
                                </svg>
                                {{-- Score Number + Grade --}}
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="font-playfair text-5xl md:text-6xl font-bold text-[#F5F5F5]" x-text="scoreAnimated"></span>
                                    <span id="grade-text" class="font-playfair text-2xl md:text-3xl font-bold {{ $gradeColorClass }}" style="opacity:0;">{{ $letterGrade }}</span>
                                </div>
                            </div>

                            <p class="font-poppins text-xs uppercase tracking-widest text-[#CCCCCC]/50 mb-1">Online Health Grade</p>
                            <p class="font-playfair text-lg font-bold {{ $gradeColorClass }}">{{ $gradeDesc }}</p>
                            <p class="font-poppins text-sm text-[#CCCCCC]/60 mt-2 max-w-[220px]">
                                {{ $scoreData['score_description'] }}
                            </p>

                            @if (!$isExternalRestaurant && isset($scoreData['area_rank']) && isset($scoreData['area_total']) && $scoreData['area_total'] > 0)
                                <div class="mt-3 inline-flex items-center px-3 py-1.5 bg-[#D4AF37]/10 rounded-full border border-[#D4AF37]/20">
                                    <span class="font-poppins text-sm text-[#D4AF37] font-medium">
                                        #{{ $scoreData['area_rank'] }} of {{ $scoreData['area_total'] }} in {{ $restaurantCity }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- CENTER COLUMN: Problems & Opportunities --}}
                        <div class="result-column" style="opacity:0;">
                            <div class="bg-[#1A1A1A] rounded-xl p-5 border border-[#2A2A2A] h-full">
                                {{-- Headline --}}
                                <p class="font-playfair text-lg font-bold text-[#F5F5F5] mb-1 leading-snug">
                                    You could be gaining
                                    <span class="text-[#D4AF37]">~${{ number_format($estimatedGain) }}/mo</span>
                                    by fixing {{ $problemCount }} {{ $problemCount === 1 ? 'problem' : 'problems' }}
                                </p>
                                <p class="font-poppins text-xs text-[#CCCCCC]/40 mb-4">Based on your FAMER Score analysis</p>

                                {{-- Restaurant mini card --}}
                                <div class="flex items-center gap-3 mb-4 pb-3 border-b border-[#2A2A2A]">
                                    @if ($scanStepData['restaurant']['image'] ?? null)
                                        <img src="{{ $scanStepData['restaurant']['image'] }}" alt="" class="w-10 h-10 rounded-lg object-cover ring-1 ring-[#2A2A2A]">
                                    @else
                                        <div class="w-10 h-10 rounded-lg bg-[#2A2A2A] flex items-center justify-center">
                                            <svg class="w-5 h-5 text-[#CCCCCC]/20" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="font-poppins text-sm font-semibold text-[#F5F5F5] truncate">{{ $restaurantName }}</p>
                                        <p class="font-poppins text-xs text-[#CCCCCC]/40">{{ $restaurantCity }}, {{ $restaurantState }}</p>
                                    </div>
                                </div>

                                {{-- Problems list --}}
                                <div class="space-y-2.5 mb-4">
                                    @foreach ($scoreData['top_recommendations'] as $idx => $rec)
                                        <div class="problem-item flex items-start gap-2.5" style="opacity:0;">
                                            <div class="flex-shrink-0 mt-0.5">
                                                @if($rec['priority'] === 'critical')
                                                    <div class="w-5 h-5 rounded-full bg-[#8B1E1E]/30 flex items-center justify-center">
                                                        <svg class="w-3 h-3 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                        </svg>
                                                    </div>
                                                @elseif($rec['priority'] === 'high')
                                                    <div class="w-5 h-5 rounded-full bg-amber-500/20 flex items-center justify-center">
                                                        <svg class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                        </svg>
                                                    </div>
                                                @else
                                                    <div class="w-5 h-5 rounded-full bg-[#2A2A2A] flex items-center justify-center">
                                                        <svg class="w-3 h-3 text-[#CCCCCC]/40" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-poppins text-sm font-semibold text-[#F5F5F5] leading-tight">{{ $rec['title'] }}</p>
                                                <p class="font-poppins text-xs text-[#CCCCCC]/50 mt-0.5 leading-snug">{{ $rec['description'] }}</p>
                                                @if(isset($rec['impact']))
                                                    <span class="inline-block mt-1 font-poppins text-[10px] font-semibold text-[#D4AF37] bg-[#D4AF37]/10 px-2 py-0.5 rounded-full">{{ $rec['impact'] }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Gold CTA --}}
                                <button
                                    wire:click="requestFullReport"
                                    class="w-full py-3 font-poppins font-bold rounded-lg text-sm transition-all text-[#0B0B0B] hover:scale-[1.02] transform"
                                    style="background: linear-gradient(135deg, #D4AF37, #E8C67A); box-shadow: 0 4px 15px rgba(212,175,55,0.3);"
                                >
                                    Fix it with FAMER Premium
                                </button>
                            </div>
                        </div>

                        {{-- RIGHT COLUMN: Competitor Ranking --}}
                        <div class="result-column" style="opacity:0;">
                            <div class="bg-[#1A1A1A] rounded-xl p-5 border border-[#2A2A2A] h-full">
                                @if (!empty($competitors))
                                    <p class="font-playfair text-lg font-bold text-[#F5F5F5] mb-1">
                                        You're ranking against {{ count($competitors) }} competitors
                                    </p>
                                    <p class="font-poppins text-xs text-[#CCCCCC]/40 mb-4">In {{ $restaurantCity }}, {{ $restaurantState }}</p>

                                    <div class="space-y-3">
                                        {{-- Current restaurant (highlighted) --}}
                                        <div class="comp-item flex items-center gap-3 p-2.5 rounded-lg bg-[#D4AF37]/10 border border-[#D4AF37]/20" style="opacity:0;">
                                            <div class="w-8 h-8 rounded-full bg-[#D4AF37] flex items-center justify-center flex-shrink-0">
                                                <svg class="w-4 h-4 text-[#0B0B0B]" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="font-poppins text-sm font-bold text-[#D4AF37] truncate">{{ $restaurantName }}</p>
                                                <div class="flex items-center gap-2 mt-0.5">
                                                    <div class="flex">
                                                        @for ($s = 1; $s <= 5; $s++)
                                                            <svg class="w-3 h-3 {{ $s <= round($scanStepData['google_rating'] ?? 0) ? 'text-[#D4AF37]' : 'text-[#2A2A2A]' }}" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                            </svg>
                                                        @endfor
                                                    </div>
                                                    <span class="font-poppins text-xs text-[#D4AF37]">{{ $scanStepData['google_rating'] ?? 'N/A' }}</span>
                                                </div>
                                                {{-- Mini score bar --}}
                                                <div class="mt-1.5 w-full bg-[#2A2A2A] rounded-full h-1.5">
                                                    <div class="h-1.5 rounded-full" style="width: {{ $overallScore }}%; background: linear-gradient(90deg, #D4AF37, #E8C67A);"></div>
                                                </div>
                                            </div>
                                            <span class="font-poppins text-xs font-bold text-[#D4AF37]">{{ $overallScore }}</span>
                                        </div>

                                        {{-- Competitors --}}
                                        @foreach ($competitors as $ci => $comp)
                                            @php
                                                $compRating = $comp['rating'] ?? 0;
                                                $compReviews = $comp['reviews'] ?? 0;
                                                // Estimate a score for competitors based on rating
                                                $compEstScore = min(95, max(15, round(($compRating / 5) * 75 + min($compReviews, 200) / 200 * 20)));
                                            @endphp
                                            <div class="comp-item flex items-center gap-3 p-2.5 rounded-lg hover:bg-[#2A2A2A]/50 transition-colors" style="opacity:0;">
                                                <div class="w-8 h-8 rounded-full bg-[#2A2A2A] flex items-center justify-center flex-shrink-0">
                                                    <span class="font-poppins text-xs font-bold text-[#CCCCCC]/50">{{ $ci + 1 }}</span>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-poppins text-sm font-medium text-[#F5F5F5] truncate">{{ $comp['name'] }}</p>
                                                    <div class="flex items-center gap-2 mt-0.5">
                                                        <div class="flex">
                                                            @for ($s = 1; $s <= 5; $s++)
                                                                <svg class="w-3 h-3 {{ $s <= round($compRating) ? 'text-[#D4AF37]' : 'text-[#2A2A2A]' }}" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                                </svg>
                                                            @endfor
                                                        </div>
                                                        <span class="font-poppins text-xs text-[#CCCCCC]/40">{{ $compRating ?: 'N/A' }}</span>
                                                        <span class="font-poppins text-[10px] text-[#CCCCCC]/30">({{ $compReviews }})</span>
                                                    </div>
                                                    <div class="mt-1.5 w-full bg-[#2A2A2A] rounded-full h-1.5">
                                                        <div class="h-1.5 rounded-full bg-[#CCCCCC]/20" style="width: {{ $compEstScore }}%;"></div>
                                                    </div>
                                                </div>
                                                <span class="font-poppins text-xs font-medium text-[#CCCCCC]/40">~{{ $compEstScore }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="flex flex-col items-center justify-center h-full text-center py-8">
                                        <div class="w-16 h-16 rounded-full bg-[#D4AF37]/10 flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </div>
                                        <p class="font-playfair text-lg font-bold text-[#F5F5F5] mb-1">Be the first to dominate your area</p>
                                        <p class="font-poppins text-sm text-[#CCCCCC]/50">No competitors found in {{ $restaurantCity }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ======================================== --}}
                    {{-- CATEGORY SCORES BAR (horizontal)          --}}
                    {{-- ======================================== --}}
                    @if (!($scoreData['is_partial'] ?? false) && $scoreData['categories'])
                        <div class="mb-10" x-show="showCategories" x-cloak>
                            <h3 class="font-playfair text-lg font-semibold text-[#F5F5F5] mb-5">Score Breakdown</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @php
                                    $catGroups = [
                                        ['label' => 'Profile & Presence', 'score' => $profilePresence, 'max' => $profilePresenceMax],
                                        ['label' => 'Customer Engagement', 'score' => $custEngagement, 'max' => $custEngagementMax],
                                        ['label' => 'Digital & Menu', 'score' => $digitalMenu, 'max' => $digitalMenuMax],
                                    ];
                                @endphp
                                @foreach ($catGroups as $cg)
                                    @php
                                        $cgPct = $cg['max'] > 0 ? round(($cg['score'] / $cg['max']) * 100) : 0;
                                        $cgLabel = $cgPct >= 70 ? 'Good' : ($cgPct >= 40 ? 'Fair' : 'Poor');
                                        $cgColor = $cgPct >= 70 ? 'linear-gradient(90deg, #D4AF37, #E8C67A)' : ($cgPct >= 40 ? 'rgba(204,204,204,0.3)' : 'rgba(204,204,204,0.15)');
                                        $cgLabelColor = $cgPct >= 70 ? 'text-[#D4AF37]' : ($cgPct >= 40 ? 'text-[#CCCCCC]/70' : 'text-red-400');
                                    @endphp
                                    <div class="cat-bar-item bg-[#1A1A1A] rounded-xl p-4 border border-[#2A2A2A]" style="opacity:0;">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="font-poppins text-sm font-medium text-[#CCCCCC]">{{ $cg['label'] }}</span>
                                            <span class="font-poppins text-sm font-bold {{ $cgLabelColor }}">{{ $cgLabel }}</span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="flex-1 bg-[#2A2A2A] rounded-full h-3">
                                                <div class="h-3 rounded-full" style="width: {{ $cgPct }}%; background: {{ $cgColor }}; animation: bar-fill 0.8s ease-out forwards;"></div>
                                            </div>
                                            <span class="font-poppins text-sm font-semibold text-[#F5F5F5] w-16 text-right">{{ $cg['score'] }}/{{ $cg['max'] }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Detailed category breakdown below --}}
                            <div class="mt-6 space-y-3">
                                @foreach ($scoreData['categories'] as $category)
                                    <div class="cat-bar-item" style="opacity:0;">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="font-poppins text-sm font-medium text-[#CCCCCC]">
                                                {{ $category['name'] }}
                                                <span class="text-[#CCCCCC]/40 text-xs">({{ $category['weight'] }}%)</span>
                                            </span>
                                            <span class="font-poppins text-sm font-semibold
                                                @if($category['score'] >= 80) text-[#D4AF37]
                                                @elseif($category['score'] >= 60) text-[#E8C67A]
                                                @elseif($category['score'] >= 40) text-[#CCCCCC]/70
                                                @else text-[#CCCCCC]/40
                                                @endif
                                            ">
                                                {{ $category['score'] }}/100
                                            </span>
                                        </div>
                                        <div class="w-full bg-[#2A2A2A] rounded-full h-2.5">
                                            <div
                                                class="h-2.5 rounded-full"
                                                style="width: {{ $category['score'] }}%; animation: bar-fill 0.8s ease-out forwards;
                                                    @if($category['score'] >= 80) background: linear-gradient(90deg, #D4AF37, #E8C67A);
                                                    @elseif($category['score'] >= 60) background: linear-gradient(90deg, #B8963A, #D4AF37);
                                                    @elseif($category['score'] >= 40) background: rgba(204,204,204,0.3);
                                                    @else background: rgba(204,204,204,0.15);
                                                    @endif
                                                "
                                            ></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- ======================================== --}}
                    {{-- KEYWORD / RANKING TABLE                    --}}
                    {{-- ======================================== --}}
                    <div class="mb-10" x-show="showKeywords" x-cloak>
                        <h3 class="font-playfair text-lg font-semibold text-[#F5F5F5] mb-2">How you rank in your area</h3>
                        <p class="font-poppins text-xs text-[#CCCCCC]/40 mb-4">Estimated search visibility based on your digital presence</p>

                        <div class="bg-[#1A1A1A] rounded-xl border border-[#2A2A2A] overflow-hidden">
                            <div class="grid grid-cols-3 gap-0 px-4 py-2.5 border-b border-[#2A2A2A]" style="background: #151515;">
                                <span class="font-poppins text-xs font-semibold text-[#CCCCCC]/50 uppercase tracking-wider">Search Term</span>
                                <span class="font-poppins text-xs font-semibold text-[#CCCCCC]/50 uppercase tracking-wider text-center">Your Rank</span>
                                <span class="font-poppins text-xs font-semibold text-[#CCCCCC]/50 uppercase tracking-wider text-right">Top Competitor</span>
                            </div>
                            @php
                                $topCompName = !empty($competitors) ? ($competitors[0]['name'] ?? 'N/A') : 'N/A';
                                // Generate illustrative keyword rankings based on score
                                $baseRank = max(1, round((100 - $overallScore) / 8));
                                $keywordRows = [
                                    ['term' => "Mexican restaurant in {$restaurantCity}", 'rank' => $baseRank, 'comp' => $topCompName],
                                    ['term' => "{$primaryCategory} in {$restaurantCity}", 'rank' => max(1, $baseRank - 1), 'comp' => $topCompName],
                                    ['term' => "Best {$primaryCategory} near {$restaurantCity}", 'rank' => $baseRank + 2, 'comp' => $topCompName],
                                    ['term' => "Mexican food delivery {$restaurantCity}", 'rank' => $overallScore < 50 ? 0 : $baseRank + 3, 'comp' => $topCompName],
                                    ['term' => "Tacos near me {$restaurantCity}", 'rank' => $overallScore < 40 ? 0 : $baseRank + 1, 'comp' => $topCompName],
                                ];
                            @endphp
                            @foreach ($keywordRows as $kr)
                                <div class="keyword-row grid grid-cols-3 gap-0 px-4 py-3 border-b border-[#2A2A2A]/50 last:border-b-0 hover:bg-[#2A2A2A]/20 transition-colors" style="opacity:0;">
                                    <span class="font-poppins text-sm text-[#F5F5F5]">{{ $kr['term'] }}</span>
                                    <span class="font-poppins text-sm font-semibold text-center
                                        {{ $kr['rank'] === 0 ? 'text-red-400' : ($kr['rank'] <= 3 ? 'text-[#D4AF37]' : ($kr['rank'] <= 7 ? 'text-[#E8C67A]' : 'text-[#CCCCCC]/50')) }}
                                    ">
                                        {{ $kr['rank'] === 0 ? 'Not ranking' : '#' . $kr['rank'] }}
                                    </span>
                                    <span class="font-poppins text-sm text-[#CCCCCC]/40 text-right truncate">{{ Str::limit($kr['comp'], 20) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ======================================== --}}
                    {{-- FINAL CTA SECTION                          --}}
                    {{-- ======================================== --}}
                    <div x-show="showCTA" x-cloak>
                        <div id="final-cta" class="rounded-xl p-8 text-center relative overflow-hidden" style="opacity:0; background: linear-gradient(135deg, rgba(212,175,55,0.12), rgba(212,175,55,0.04)); border: 1px solid rgba(212,175,55,0.2);">
                            {{-- Background shimmer --}}
                            <div class="absolute inset-0 pointer-events-none" style="background: linear-gradient(90deg, transparent 0%, rgba(212,175,55,0.03) 50%, transparent 100%); background-size: 200% 100%; animation: shimmer 3s ease-in-out infinite;"></div>

                            @if (!$emailSubmitted)
                                <h3 class="font-playfair text-2xl font-bold mb-2 text-[#D4AF37] relative z-10">Unlock Your Full Report</h3>
                                <p class="font-poppins text-[#CCCCCC]/60 mb-6 relative z-10 max-w-lg mx-auto">
                                    Get detailed competitive analysis, all {{ count($scoreData['all_recommendations'] ?? $scoreData['top_recommendations']) }} recommendations, and a custom action plan to boost your score.
                                </p>
                                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 relative z-10">
                                    <button
                                        wire:click="requestFullReport"
                                        class="px-10 py-4 font-poppins font-bold rounded-xl text-lg transition-all text-[#0B0B0B] hover:scale-[1.03] transform pulse-gold-ring"
                                        style="background: linear-gradient(135deg, #D4AF37, #E8C67A); box-shadow: 0 4px 25px rgba(212,175,55,0.4);"
                                    >
                                        Get Free Report
                                    </button>
                                    @if (!($selectedRestaurant['is_claimed'] ?? true))
                                        <a
                                            href="{{ route('claim.restaurant') }}"
                                            class="inline-flex items-center px-8 py-4 font-poppins font-semibold rounded-xl text-[#D4AF37] border border-[#D4AF37]/30 hover:bg-[#D4AF37]/10 transition-all"
                                        >
                                            Claim Your Restaurant
                                            <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            @else
                                <div class="relative z-10">
                                    <div class="w-16 h-16 rounded-full bg-[#1F3D2B]/30 flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <h3 class="font-playfair text-2xl font-bold mb-2 text-[#D4AF37]">Report Sent!</h3>
                                    <p class="font-poppins text-[#CCCCCC]/60">Check your email for the full FAMER Score report.</p>
                                </div>
                            @endif

                            {{-- Trust badges --}}
                            <div class="flex items-center justify-center gap-6 mt-6 relative z-10">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-[#D4AF37]/60" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="font-poppins text-xs text-[#CCCCCC]/40">Free analysis</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-[#D4AF37]/60" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="font-poppins text-xs text-[#CCCCCC]/40">No credit card</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-[#D4AF37]/60" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="font-poppins text-xs text-[#CCCCCC]/40">Instant results</span>
                                </div>
                            </div>
                        </div>

                        {{-- CTA for Claiming (separate, below main CTA) --}}
                        @if (!($selectedRestaurant['is_claimed'] ?? true) && !$emailSubmitted)
                            <div class="mt-6 bg-[#1F3D2B]/20 border border-[#1F3D2B]/40 rounded-xl p-6 text-center">
                                <h3 class="font-playfair text-lg font-bold text-[#F5F5F5] mb-2">
                                    Are you the owner of {{ $restaurantName }}?
                                </h3>
                                <p class="font-poppins text-[#CCCCCC]/60 mb-4">
                                    Claim your restaurant to edit your profile, respond to reviews, and access marketing tools.
                                </p>
                                <a
                                    href="{{ route('claim.restaurant') }}"
                                    class="inline-flex items-center px-6 py-3 font-poppins font-bold rounded-lg text-[#0B0B0B] transition-all"
                                    style="background: linear-gradient(135deg, #D4AF37, #E8C67A); box-shadow: 0 4px 15px rgba(212,175,55,0.2);"
                                >
                                    Claim Restaurant
                                    <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

    </div>{{-- close max-w-6xl container --}}

    {{-- ============================================================ --}}
    {{-- PREMIUM LANDING SECTIONS (idle state)                        --}}
    {{-- ============================================================ --}}
    @if (!$scoreData && !$showAnalysis)

            {{-- ======== 1. SOCIAL PROOF STRIP ======== --}}
            <div class="famer-reveal mt-16 bg-[#1A1A1A] border-y border-[#2A2A2A]">
                <div class="max-w-6xl mx-auto px-4 py-4 flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-8">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-poppins font-semibold text-[#0B0B0B] tracking-wide" style="background: linear-gradient(135deg, #D4AF37, #E8C67A);">
                        #1 Mexican Restaurant Discovery Platform
                    </span>
                    <div class="flex items-center gap-6 text-sm font-poppins text-[#CCCCCC]/70">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-[#D4AF37]" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/></svg>
                            <strong class="text-[#F5F5F5]">{{ number_format($totalRestaurantsRounded) }}+</strong> restaurants analyzed
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-[#D4AF37]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>
                            <strong class="text-[#F5F5F5]">8</strong> review platforms
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-[#D4AF37]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                            <strong class="text-[#F5F5F5]">50</strong> states covered
                        </span>
                    </div>
                </div>
            </div>

            {{-- ======== 2. HOW THE FAMER SCORE WORKS ======== --}}
            <div class="famer-reveal bg-[#0B0B0B] py-20">
                <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h2 class="font-playfair text-3xl md:text-4xl font-bold text-center mb-4 gold-gradient-text">How the FAMER Score Works</h2>
                    <p class="font-poppins text-[#CCCCCC]/60 text-center mb-14 max-w-2xl mx-auto">Three simple steps to understand your restaurant's digital presence</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12">
                        {{-- Step 1 --}}
                        <div class="famer-reveal-step text-center">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-5 relative" style="background: linear-gradient(135deg, #D4AF37, #E8C67A); box-shadow: 0 0 30px rgba(212,175,55,0.2);">
                                <span class="font-playfair text-2xl font-bold text-[#0B0B0B]">1</span>
                            </div>
                            <div class="w-12 h-12 mx-auto mb-4 flex items-center justify-center">
                                <svg class="w-10 h-10 text-[#D4AF37]/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <h3 class="font-playfair text-xl font-bold text-[#F5F5F5] mb-2">Search Your Restaurant</h3>
                            <p class="font-poppins text-sm text-[#CCCCCC]/50 leading-relaxed">Enter your restaurant name and location. We'll find it across Google, Yelp, and our database.</p>
                        </div>

                        {{-- Step 2 --}}
                        <div class="famer-reveal-step text-center">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-5 relative" style="background: linear-gradient(135deg, #D4AF37, #E8C67A); box-shadow: 0 0 30px rgba(212,175,55,0.2);">
                                <span class="font-playfair text-2xl font-bold text-[#0B0B0B]">2</span>
                            </div>
                            <div class="w-12 h-12 mx-auto mb-4 flex items-center justify-center">
                                <svg class="w-10 h-10 text-[#D4AF37]/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <h3 class="font-playfair text-xl font-bold text-[#F5F5F5] mb-2">AI Analyzes 7 Categories</h3>
                            <p class="font-poppins text-sm text-[#CCCCCC]/50 leading-relaxed">Google, Yelp, photos, website, mobile, competitors, and reviews — all scanned in under 60 seconds.</p>
                        </div>

                        {{-- Step 3 --}}
                        <div class="famer-reveal-step text-center">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-5 relative" style="background: linear-gradient(135deg, #D4AF37, #E8C67A); box-shadow: 0 0 30px rgba(212,175,55,0.2);">
                                <span class="font-playfair text-2xl font-bold text-[#0B0B0B]">3</span>
                            </div>
                            <div class="w-12 h-12 mx-auto mb-4 flex items-center justify-center">
                                <svg class="w-10 h-10 text-[#D4AF37]/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                            <h3 class="font-playfair text-xl font-bold text-[#F5F5F5] mb-2">Get Your Score & Action Plan</h3>
                            <p class="font-poppins text-sm text-[#CCCCCC]/50 leading-relaxed">Detailed report with specific improvements ranked by impact. Know exactly what to fix first.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ======== 3. WHAT WE SCAN ======== --}}
            <div class="famer-reveal bg-[#1A1A1A] py-20">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h2 class="font-playfair text-3xl md:text-4xl font-bold text-center mb-3 gold-gradient-text">We scan more than anyone else</h2>
                    <p class="font-poppins text-[#CCCCCC]/60 text-center mb-6 max-w-2xl mx-auto">FAMER analyzes 7 categories vs the industry standard of 6</p>

                    <div class="flex items-center justify-center gap-6 mb-12">
                        <div class="flex items-center gap-2 px-4 py-2 rounded-full bg-[#D4AF37]/10 border border-[#D4AF37]/20">
                            <span class="font-poppins font-bold text-[#D4AF37] text-lg">7</span>
                            <span class="font-poppins text-sm text-[#CCCCCC]/70">FAMER categories</span>
                        </div>
                        <span class="font-poppins text-[#CCCCCC]/30 text-sm">vs</span>
                        <div class="flex items-center gap-2 px-4 py-2 rounded-full bg-[#2A2A2A] border border-[#2A2A2A]">
                            <span class="font-poppins font-bold text-[#CCCCCC]/50 text-lg">6</span>
                            <span class="font-poppins text-sm text-[#CCCCCC]/40">Owner.com</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        {{-- Card 1 --}}
                        <div class="famer-reveal-step bg-[#0B0B0B] rounded-xl p-5 border border-[#2A2A2A] hover:border-[#D4AF37]/30 transition-all group">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-3" style="background: rgba(212,175,55,0.1);">
                                <svg class="w-6 h-6 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <h4 class="font-poppins font-semibold text-[#F5F5F5] mb-1">Competitors & Local Market</h4>
                            <p class="font-poppins text-xs text-[#CCCCCC]/40 mb-3 leading-relaxed">Map nearby competitors, compare ratings, and find your market position.</p>
                            <span class="font-poppins text-[10px] text-[#D4AF37]/50 uppercase tracking-wider">Powered by Google Maps</span>
                        </div>
                        {{-- Card 2 --}}
                        <div class="famer-reveal-step bg-[#0B0B0B] rounded-xl p-5 border border-[#2A2A2A] hover:border-[#D4AF37]/30 transition-all group">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-3" style="background: rgba(212,175,55,0.1);">
                                <svg class="w-6 h-6 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9"/></svg>
                            </div>
                            <h4 class="font-poppins font-semibold text-[#F5F5F5] mb-1">Google Business Profile</h4>
                            <p class="font-poppins text-xs text-[#CCCCCC]/40 mb-3 leading-relaxed">Rating, review count, photos, hours, attributes, and completeness.</p>
                            <span class="font-poppins text-[10px] text-[#D4AF37]/50 uppercase tracking-wider">Powered by Google Places</span>
                        </div>
                        {{-- Card 3 --}}
                        <div class="famer-reveal-step bg-[#0B0B0B] rounded-xl p-5 border border-[#2A2A2A] hover:border-[#D4AF37]/30 transition-all group relative">
                            <span class="absolute top-3 right-3 px-2 py-0.5 rounded text-[10px] font-poppins font-semibold text-[#0B0B0B]" style="background: linear-gradient(135deg, #D4AF37, #E8C67A);">FAMER exclusive</span>
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-3" style="background: rgba(212,175,55,0.1);">
                                <svg class="w-6 h-6 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            </div>
                            <h4 class="font-poppins font-semibold text-[#F5F5F5] mb-1">Yelp Profile</h4>
                            <p class="font-poppins text-xs text-[#CCCCCC]/40 mb-3 leading-relaxed">Yelp rating, review volume, response rate, and category placement.</p>
                            <span class="font-poppins text-[10px] text-[#D4AF37]/50 uppercase tracking-wider">Powered by Yelp Fusion</span>
                        </div>
                        {{-- Card 4 --}}
                        <div class="famer-reveal-step bg-[#0B0B0B] rounded-xl p-5 border border-[#2A2A2A] hover:border-[#D4AF37]/30 transition-all group">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-3" style="background: rgba(212,175,55,0.1);">
                                <svg class="w-6 h-6 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                            </div>
                            <h4 class="font-poppins font-semibold text-[#F5F5F5] mb-1">Review Sentiment Analysis</h4>
                            <p class="font-poppins text-xs text-[#CCCCCC]/40 mb-3 leading-relaxed">AI-powered analysis of what customers love and complain about.</p>
                            <span class="font-poppins text-[10px] text-[#D4AF37]/50 uppercase tracking-wider">Powered by AI</span>
                        </div>
                        {{-- Card 5 --}}
                        <div class="famer-reveal-step bg-[#0B0B0B] rounded-xl p-5 border border-[#2A2A2A] hover:border-[#D4AF37]/30 transition-all group">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-3" style="background: rgba(212,175,55,0.1);">
                                <svg class="w-6 h-6 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <h4 class="font-poppins font-semibold text-[#F5F5F5] mb-1">Photo Quality Assessment</h4>
                            <p class="font-poppins text-xs text-[#CCCCCC]/40 mb-3 leading-relaxed">Photo count, quality indicators, and visual appeal scoring.</p>
                            <span class="font-poppins text-[10px] text-[#D4AF37]/50 uppercase tracking-wider">Powered by Google + Yelp</span>
                        </div>
                        {{-- Card 6 --}}
                        <div class="famer-reveal-step bg-[#0B0B0B] rounded-xl p-5 border border-[#2A2A2A] hover:border-[#D4AF37]/30 transition-all group">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-3" style="background: rgba(212,175,55,0.1);">
                                <svg class="w-6 h-6 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <h4 class="font-poppins font-semibold text-[#F5F5F5] mb-1">Website Performance</h4>
                            <p class="font-poppins text-xs text-[#CCCCCC]/40 mb-3 leading-relaxed">Speed, SEO basics, SSL, and overall web presence quality.</p>
                            <span class="font-poppins text-[10px] text-[#D4AF37]/50 uppercase tracking-wider">Powered by Lighthouse</span>
                        </div>
                        {{-- Card 7 --}}
                        <div class="famer-reveal-step bg-[#0B0B0B] rounded-xl p-5 border border-[#2A2A2A] hover:border-[#D4AF37]/30 transition-all group">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-3" style="background: rgba(212,175,55,0.1);">
                                <svg class="w-6 h-6 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                            <h4 class="font-poppins font-semibold text-[#F5F5F5] mb-1">Mobile Experience</h4>
                            <p class="font-poppins text-xs text-[#CCCCCC]/40 mb-3 leading-relaxed">Mobile-friendliness, tap targets, viewport, and responsive design.</p>
                            <span class="font-poppins text-[10px] text-[#D4AF37]/50 uppercase tracking-wider">Powered by PageSpeed</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ======== 4. FEATURE SHOWCASE (Tabbed) ======== --}}
            <div class="famer-reveal bg-[#0B0B0B] py-20" x-data="{
                activeTab: 0,
                tabs: [
                    { title: 'Rank Higher', desc: 'Appear in top search results and city rankings. Our multi-platform scoring helps you understand exactly what to improve to outrank competitors.', icon: 'chart' },
                    { title: 'Get More Reviews', desc: 'We analyze sentiment across Google, Yelp, TripAdvisor, Facebook, and more. Know what customers really think and how to earn 5-star reviews.', icon: 'star' },
                    { title: 'Digital Menu & Orders', desc: 'Premium members get digital menus with QR codes, online ordering, and reservation systems. Everything a modern restaurant needs.', icon: 'menu' },
                    { title: 'Grow Your Brand', desc: 'From featured badges to loyalty programs, build a brand that customers remember. Stand out in a crowded market with verified quality.', icon: 'brand' }
                ],
                progress: 0,
                interval: null,
                init() {
                    this.startAutoAdvance();
                },
                startAutoAdvance() {
                    this.progress = 0;
                    if (this.interval) clearInterval(this.interval);
                    this.interval = setInterval(() => {
                        this.progress += 2;
                        if (this.progress >= 100) {
                            this.activeTab = (this.activeTab + 1) % this.tabs.length;
                            this.progress = 0;
                        }
                    }, 100);
                },
                selectTab(idx) {
                    this.activeTab = idx;
                    this.progress = 0;
                    if (this.interval) clearInterval(this.interval);
                    this.startAutoAdvance();
                }
            }" x-init="init()">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h2 class="font-playfair text-3xl md:text-4xl font-bold text-center mb-4 gold-gradient-text">Everything you need to dominate your market</h2>
                    <p class="font-poppins text-[#CCCCCC]/60 text-center mb-12 max-w-2xl mx-auto">Tools and insights designed specifically for Mexican restaurants</p>

                    {{-- Tab Bar --}}
                    <div class="flex flex-col sm:flex-row border-b border-[#2A2A2A] mb-8 gap-0">
                        <template x-for="(tab, idx) in tabs" :key="idx">
                            <button
                                @click="selectTab(idx)"
                                :class="activeTab === idx ? 'text-[#D4AF37] border-transparent' : 'text-[#CCCCCC]/50 border-transparent hover:text-[#CCCCCC]/80'"
                                class="relative flex-1 py-3 px-4 font-poppins font-semibold text-sm transition-colors text-center"
                            >
                                <span x-text="tab.title"></span>
                                {{-- Gold progress bar under active tab --}}
                                <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#2A2A2A] overflow-hidden rounded-full">
                                    <div
                                        x-show="activeTab === idx"
                                        class="h-full rounded-full"
                                        style="background: linear-gradient(90deg, #D4AF37, #E8C67A);"
                                        :style="'width: ' + (activeTab === idx ? progress : 0) + '%'"
                                        x-transition
                                    ></div>
                                </div>
                            </button>
                        </template>
                    </div>

                    {{-- Tab Content --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center min-h-[320px]">
                        {{-- Left: Text Content --}}
                        <div>
                            <h3 class="font-playfair text-2xl font-bold text-[#F5F5F5] mb-4" x-text="tabs[activeTab].title"></h3>
                            <p class="font-poppins text-[#CCCCCC]/60 leading-relaxed mb-6" x-text="tabs[activeTab].desc"></p>
                            <a href="#" onclick="window.scrollTo({top:0,behavior:'smooth'});return false;" class="inline-flex items-center gap-2 font-poppins font-semibold text-sm text-[#D4AF37] hover:text-[#E8C67A] transition-colors">
                                Try it free
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                        </div>
                        {{-- Right: Image + Mockup --}}
                        <div class="relative">
                            {{-- Rank Higher --}}
                            <div x-show="activeTab === 0" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                                <div class="relative rounded-xl overflow-hidden border border-[#2A2A2A] hover:border-[#D4AF37]/30 transition-all">
                                    <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=600&h=400&fit=crop"
                                         alt="Elegant Mexican restaurant interior"
                                         class="w-full h-[280px] object-cover rounded-xl"
                                         loading="lazy"
                                         onerror="this.style.display='none'">
                                    <div class="absolute inset-0 bg-gradient-to-t from-[#0B0B0B]/90 via-[#0B0B0B]/40 to-transparent"></div>
                                    <div class="absolute bottom-0 left-0 right-0 p-5">
                                        <div class="bg-[#1A1A1A]/90 backdrop-blur-sm rounded-lg p-4 border border-[#2A2A2A]">
                                            <div class="flex items-center gap-3 mb-2">
                                                <div class="w-7 h-7 rounded-full flex items-center justify-center" style="background: linear-gradient(135deg, #D4AF37, #E8C67A);"><span class="text-[#0B0B0B] font-bold text-xs">1</span></div>
                                                <div class="flex-1"><div class="h-2.5 bg-[#D4AF37]/30 rounded-full w-3/4"></div></div>
                                                <span class="font-poppins text-[#D4AF37] font-bold text-sm">92</span>
                                            </div>
                                            <div class="flex items-center gap-3 mb-2">
                                                <div class="w-7 h-7 rounded-full flex items-center justify-center bg-[#2A2A2A]"><span class="text-[#CCCCCC]/50 font-bold text-xs">2</span></div>
                                                <div class="flex-1"><div class="h-2.5 bg-[#2A2A2A] rounded-full w-1/2"></div></div>
                                                <span class="font-poppins text-[#CCCCCC]/50 font-bold text-sm">71</span>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <div class="w-7 h-7 rounded-full flex items-center justify-center bg-[#2A2A2A]"><span class="text-[#CCCCCC]/50 font-bold text-xs">3</span></div>
                                                <div class="flex-1"><div class="h-2.5 bg-[#2A2A2A] rounded-full w-2/5"></div></div>
                                                <span class="font-poppins text-[#CCCCCC]/50 font-bold text-sm">58</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Get More Reviews --}}
                            <div x-show="activeTab === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                                <div class="relative rounded-xl overflow-hidden border border-[#2A2A2A] hover:border-[#D4AF37]/30 transition-all">
                                    <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=600&h=400&fit=crop"
                                         alt="Restaurant dining experience with happy customers"
                                         class="w-full h-[280px] object-cover rounded-xl"
                                         loading="lazy"
                                         onerror="this.style.display='none'">
                                    <div class="absolute inset-0 bg-gradient-to-t from-[#0B0B0B]/90 via-[#0B0B0B]/40 to-transparent"></div>
                                    <div class="absolute bottom-0 left-0 right-0 p-5 space-y-2">
                                        <div class="bg-[#1A1A1A]/90 backdrop-blur-sm rounded-lg p-3 border border-[#2A2A2A]">
                                            <div class="flex items-center gap-2 mb-1">
                                                <div class="flex text-[#D4AF37] text-xs">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                                                <span class="font-poppins text-xs text-[#CCCCCC]/50">Google</span>
                                            </div>
                                            <p class="font-poppins text-xs text-[#CCCCCC]/60">"Best tacos in town! Amazing service..."</p>
                                        </div>
                                        <div class="bg-[#1A1A1A]/90 backdrop-blur-sm rounded-lg p-3 border border-[#2A2A2A]">
                                            <div class="flex items-center gap-2 mb-1">
                                                <div class="flex text-[#D4AF37] text-xs">&#9733;&#9733;&#9733;&#9733;<span class="text-[#2A2A2A]">&#9733;</span></div>
                                                <span class="font-poppins text-xs text-[#CCCCCC]/50">Yelp</span>
                                            </div>
                                            <p class="font-poppins text-xs text-[#CCCCCC]/60">"Authentic flavors, great atmosphere..."</p>
                                        </div>
                                        <div class="bg-[#1A1A1A]/90 backdrop-blur-sm rounded-lg p-3 border border-[#2A2A2A]">
                                            <div class="flex items-center gap-2 mb-1">
                                                <div class="flex text-[#D4AF37] text-xs">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                                                <span class="font-poppins text-xs text-[#CCCCCC]/50">TripAdvisor</span>
                                            </div>
                                            <p class="font-poppins text-xs text-[#CCCCCC]/60">"A must-visit! We come back every week..."</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Digital Menu & Orders --}}
                            <div x-show="activeTab === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                                <div class="relative rounded-xl overflow-hidden border border-[#2A2A2A] hover:border-[#D4AF37]/30 transition-all">
                                    <img src="https://images.unsplash.com/photo-1565299585323-38d6b0865b47?w=600&h=400&fit=crop"
                                         alt="Delicious Mexican tacos"
                                         class="w-full h-[280px] object-cover rounded-xl"
                                         loading="lazy"
                                         onerror="this.style.display='none'">
                                    <div class="absolute inset-0 bg-gradient-to-t from-[#0B0B0B]/90 via-[#0B0B0B]/40 to-transparent"></div>
                                    <div class="absolute bottom-0 left-0 right-0 p-5">
                                        <div class="bg-[#1A1A1A]/90 backdrop-blur-sm rounded-lg p-4 border border-[#2A2A2A]">
                                            <div class="flex items-center justify-between mb-3">
                                                <span class="font-playfair font-bold text-[#F5F5F5] text-sm">Digital Menu</span>
                                                <svg class="w-5 h-5 text-[#CCCCCC]/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                            </div>
                                            <div class="flex justify-between items-center py-1.5 border-b border-[#2A2A2A]">
                                                <span class="font-poppins text-xs text-[#CCCCCC]/70">Tacos al Pastor</span>
                                                <span class="font-poppins text-xs text-[#D4AF37]">$12.99</span>
                                            </div>
                                            <div class="flex justify-between items-center py-1.5 border-b border-[#2A2A2A]">
                                                <span class="font-poppins text-xs text-[#CCCCCC]/70">Enchiladas Suizas</span>
                                                <span class="font-poppins text-xs text-[#D4AF37]">$15.99</span>
                                            </div>
                                            <div class="flex justify-between items-center py-1.5">
                                                <span class="font-poppins text-xs text-[#CCCCCC]/70">Chiles Rellenos</span>
                                                <span class="font-poppins text-xs text-[#D4AF37]">$14.99</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Grow Your Brand --}}
                            <div x-show="activeTab === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                                <div class="relative rounded-xl overflow-hidden border border-[#2A2A2A] hover:border-[#D4AF37]/30 transition-all">
                                    <img src="https://images.unsplash.com/photo-1552566626-52f8b828add9?w=600&h=400&fit=crop"
                                         alt="Restaurant exterior at night with warm lighting"
                                         class="w-full h-[280px] object-cover rounded-xl"
                                         loading="lazy"
                                         onerror="this.style.display='none'">
                                    <div class="absolute inset-0 bg-gradient-to-t from-[#0B0B0B]/90 via-[#0B0B0B]/40 to-transparent"></div>
                                    <div class="absolute bottom-0 left-0 right-0 p-5 text-center">
                                        <div class="bg-[#1A1A1A]/90 backdrop-blur-sm rounded-lg p-4 border border-[#2A2A2A]">
                                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full mb-3" style="background: linear-gradient(135deg, rgba(212,175,55,0.15), rgba(212,175,55,0.05)); border: 1px solid rgba(212,175,55,0.3);">
                                                <svg class="w-4 h-4 text-[#D4AF37]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                <span class="font-poppins font-semibold text-[#D4AF37] text-xs">FAMER Verified</span>
                                            </div>
                                            <div class="flex items-center justify-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-[#D4AF37]/10 flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-[#D4AF37]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                </div>
                                                <div class="w-8 h-8 rounded-full bg-[#1F3D2B]/30 flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0c.486.398 1.081.636 1.745.723a3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                </div>
                                                <div class="w-8 h-8 rounded-full bg-[#8B1E1E]/20 flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg>
                                                </div>
                                            </div>
                                            <p class="font-poppins text-xs text-[#CCCCCC]/50 mt-2">Badges, loyalty, and verified status</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ======== 5. PLATFORM COVERAGE ======== --}}
            <div class="famer-reveal bg-[#1A1A1A] py-20">
                <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h2 class="font-playfair text-3xl md:text-4xl font-bold mb-3 gold-gradient-text">We analyze data from 8+ platforms</h2>
                    <p class="font-poppins text-[#CCCCCC]/50 mb-12">The most comprehensive restaurant analysis available</p>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
                        @php
                            $platforms = ['Google', 'Yelp', 'TripAdvisor', 'Facebook', 'Foursquare', 'Apple Maps', 'Uber Eats', 'OpenTable'];
                            $platformIcons = [
                                'Google' => '<path d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0 5.867 0 .307 5.387.307 12s5.56 12 12.173 12c3.573 0 6.267-1.173 8.373-3.36 2.16-2.16 2.84-5.213 2.84-7.667 0-.76-.053-1.467-.173-2.053H12.48z"/>',
                                'Yelp' => '<path d="M20.16 12.594l-4.995 1.433c-.96.276-1.74-.8-1.176-1.63l2.905-4.308a1.072 1.072 0 011.596-.206l2.69 2.282c.67.57.37 1.63-.42 1.83zm-5.69 3.634l4.942 1.63c.972.32 1.028 1.696.084 2.093l-3.39 1.43c-.6.253-1.29-.063-1.537-.701l-1.298-3.346c-.377-.974.436-1.803 1.198-1.106zM12.31 3.57L10.02 9.46c-.358.923-1.632.883-1.93-.063L6.27 3.87C5.94 2.798 6.78 1.752 7.88 1.843l3.454.286c1.08.09 1.447 1.113.976 1.44zM8.055 11.48l-5.13-.625C1.884 10.728 1.47 9.38 2.324 8.7l3.2-2.553c.67-.534 1.622-.196 1.748.618l.69 4.43c.14.9-.793 1.455-1.508 1.14zm.307 2.37l2.36 4.63c.47.925-.285 1.98-1.317 1.84l-3.5-.475c-.66-.09-1.1-.735-.992-1.42l.71-4.156c.163-.95 1.387-1.18 1.89-.418z"/>',
                                'TripAdvisor' => '<circle cx="8.5" cy="12" r="2.5"/><circle cx="15.5" cy="12" r="2.5"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/>',
                                'Facebook' => '<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>',
                                'Foursquare' => '<path d="M17.727 2.2H6.273c-.834 0-1.51.676-1.51 1.51v16.96c0 .558.31 1.065.806 1.318.218.112.455.168.692.168.291 0 .579-.084.826-.25l5.19-3.436a.74.74 0 01.416-.128h2.574c.745 0 1.385-.534 1.503-1.269l1.94-12.29A1.507 1.507 0 0017.727 2.2zM14.91 8.94l-.362 2.018a.385.385 0 01-.378.321H11.1a.748.748 0 00-.748.748v.47c0 .413.335.748.748.748h2.674a.385.385 0 01.38.449l-.362 2.018a.385.385 0 01-.379.321h-2.074a.74.74 0 00-.416.128l-2.11 1.397V6.43c0-.413.336-.748.749-.748h5.65a.385.385 0 01.38.449l-.282 1.488a.385.385 0 01-.379.321h-3.097a.748.748 0 00-.748.748v.474c0 .413.335.748.748.748h2.874c.23 0 .415.2.38.43z"/>',
                                'Apple Maps' => '<path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 18l-4-8h3V5l4 8h-3v7z"/>',
                                'Uber Eats' => '<path d="M2 12c0 5.523 4.477 10 10 10s10-4.477 10-10S17.523 2 12 2 2 6.477 2 12zm4.5-2.5h3v7h-3v-7zm4 0h3v7h-3v-7zm4 0h3v7h-3v-7zM6.5 7h11v1.5h-11V7z"/>',
                                'OpenTable' => '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>',
                            ];
                        @endphp
                        @foreach ($platforms as $platform)
                            <div class="flex flex-col items-center gap-2 py-4 group cursor-default">
                                <div class="w-10 h-10 flex items-center justify-center text-[#CCCCCC]/60 group-hover:text-[#D4AF37] transition-colors">
                                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">{!! $platformIcons[$platform] !!}</svg>
                                </div>
                                <span class="font-poppins text-sm text-[#CCCCCC]/70 group-hover:text-[#D4AF37] transition-colors">{{ $platform }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ======== 6. SUCCESS METRICS ======== --}}
            <div class="famer-reveal bg-[#0B0B0B] py-20">
                <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h2 class="font-playfair text-3xl md:text-4xl font-bold text-center mb-4 gold-gradient-text">Restaurants that improved their FAMER Score</h2>
                    <p class="font-poppins text-[#CCCCCC]/50 text-center mb-12">Real results from restaurants that took action</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @php
                            $successCards = [
                                ['name' => 'La Casa de Tonantzin', 'city' => 'Austin, TX', 'before' => 35, 'after' => 78, 'metric' => '+120% more visibility', 'image' => 'https://images.unsplash.com/photo-1504544750208-dc0358e63f7f?w=400&h=300&fit=crop'],
                                ['name' => 'Taqueria Don Ramon', 'city' => 'Chicago, IL', 'before' => 42, 'after' => 85, 'metric' => '+89% more reviews', 'image' => 'https://images.unsplash.com/photo-1613514785940-daed07799d9b?w=400&h=300&fit=crop'],
                                ['name' => 'El Rinconcito Oaxaqueno', 'city' => 'Phoenix, AZ', 'before' => 28, 'after' => 91, 'metric' => '+200% more customers', 'image' => 'https://images.unsplash.com/photo-1551504734-5ee1c4a1479b?w=400&h=300&fit=crop'],
                            ];
                        @endphp
                        @foreach ($successCards as $card)
                            <div class="famer-reveal-step relative rounded-xl overflow-hidden border border-[#2A2A2A] hover:border-[#D4AF37]/20 transition-all group">
                                {{-- Background image --}}
                                <img src="{{ $card['image'] }}"
                                     alt="{{ $card['name'] }}"
                                     class="absolute inset-0 w-full h-full object-cover"
                                     loading="lazy"
                                     onerror="this.style.display='none'">
                                {{-- Dark overlay --}}
                                <div class="absolute inset-0 bg-gradient-to-b from-[#0B0B0B]/90 to-[#0B0B0B]/70"></div>
                                {{-- Content --}}
                                <div class="relative z-10 p-6">
                                    <h4 class="font-playfair font-bold text-[#F5F5F5] mb-1">{{ $card['name'] }}</h4>
                                    <p class="font-poppins text-xs text-[#CCCCCC]/40 mb-5">{{ $card['city'] }}</p>

                                    <div class="flex items-center justify-center gap-3 mb-4">
                                        <div class="text-center">
                                            <span class="block font-playfair text-3xl font-bold text-[#CCCCCC]/40">{{ $card['before'] }}</span>
                                            <span class="font-poppins text-[10px] text-[#CCCCCC]/30 uppercase tracking-wider">Before</span>
                                        </div>
                                        <svg class="w-8 h-8 text-[#D4AF37] famer-score-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                        <div class="text-center">
                                            <span class="block font-playfair text-3xl font-bold text-[#D4AF37]">{{ $card['after'] }}</span>
                                            <span class="font-poppins text-[10px] text-[#D4AF37]/50 uppercase tracking-wider">After</span>
                                        </div>
                                    </div>

                                    <div class="text-center py-2 rounded-lg" style="background: rgba(212,175,55,0.06); border: 1px solid rgba(212,175,55,0.1);">
                                        <span class="font-poppins font-semibold text-sm text-[#D4AF37]">{{ $card['metric'] }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ======== 7. COMPARISON TABLE ======== --}}
            <div class="famer-reveal bg-[#1A1A1A] py-20">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h2 class="font-playfair text-3xl md:text-4xl font-bold text-center mb-4 gold-gradient-text">FAMER Score vs the competition</h2>
                    <p class="font-poppins text-[#CCCCCC]/50 text-center mb-12">See why we're the best choice for Mexican restaurants</p>

                    <div class="overflow-x-auto">
                        <table class="w-full font-poppins text-sm">
                            <thead>
                                <tr class="border-b border-[#2A2A2A]">
                                    <th class="text-left py-4 px-4 text-[#CCCCCC]/50 font-medium">Feature</th>
                                    <th class="text-center py-4 px-4 font-bold rounded-t-lg" style="background: rgba(212,175,55,0.08); color: #D4AF37;">FAMER</th>
                                    <th class="text-center py-4 px-4 text-[#CCCCCC]/50 font-medium">Owner.com</th>
                                    <th class="text-center py-4 px-4 text-[#CCCCCC]/50 font-medium">Others</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $comparisonRows = [
                                        ['Mexican restaurant focus', true, false, false],
                                        ['Yelp analysis', true, false, false],
                                        ['8+ review platforms', true, 'Google only', 'Varies'],
                                        ['Free score', true, true, 'Some'],
                                        ['Competitor mapping', true, true, false],
                                        ['City rankings', true, false, false],
                                        ['Spanish support', true, false, false],
                                        ['Action plan', true, 'Paid', false],
                                    ];
                                @endphp
                                @foreach ($comparisonRows as $row)
                                    <tr class="border-b border-[#2A2A2A]/50 hover:bg-[#2A2A2A]/20 transition-colors">
                                        <td class="py-3 px-4 text-[#CCCCCC]/70">{{ $row[0] }}</td>
                                        <td class="py-3 px-4 text-center" style="background: rgba(212,175,55,0.04);">
                                            @if ($row[1] === true)
                                                <svg class="w-5 h-5 text-[#D4AF37] mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            @else
                                                <span class="text-[#D4AF37]">{{ $row[1] }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            @if ($row[2] === true)
                                                <svg class="w-5 h-5 text-[#CCCCCC]/40 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            @elseif ($row[2] === false)
                                                <svg class="w-5 h-5 text-[#CCCCCC]/20 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                            @else
                                                <span class="text-[#CCCCCC]/40">{{ $row[2] }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            @if ($row[3] === true)
                                                <svg class="w-5 h-5 text-[#CCCCCC]/40 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            @elseif ($row[3] === false)
                                                <svg class="w-5 h-5 text-[#CCCCCC]/20 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                            @else
                                                <span class="text-[#CCCCCC]/40">{{ $row[3] }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ======== 7.5 GIVE YOUR RESTAURANT THE BEST TOOLS ======== --}}
            <section class="famer-reveal py-20" style="background-color: #0B0B0B;">
                <div class="max-w-7xl mx-auto px-4">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                        <div>
                            <h2 class="font-playfair text-3xl md:text-4xl font-bold text-[#F5F5F5] mb-6">
                                Give your Mexican restaurant the visibility it deserves
                            </h2>
                            <p class="font-poppins text-[#CCCCCC] text-lg mb-8">
                                Join {{ number_format($totalRestaurantsRounded) }}+ restaurants already on FAMER. Get discovered by customers who love authentic Mexican food.
                            </p>
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#D4AF37]/10 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <span class="font-poppins text-[#F5F5F5]">Free listing on the #1 Mexican restaurant platform</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#D4AF37]/10 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <span class="font-poppins text-[#F5F5F5]">Ranked across 8+ review platforms</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#D4AF37]/10 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <span class="font-poppins text-[#F5F5F5]">Premium tools starting at $9.99/month</span>
                                </div>
                            </div>
                            <div class="mt-8">
                                <a href="/claim" class="inline-flex items-center px-8 py-4 bg-[#D4AF37] text-[#0B0B0B] font-bold rounded-xl hover:bg-[#E8C67A] transition-all">
                                    Claim Your Restaurant Free
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                </a>
                            </div>
                        </div>
                        <div class="relative">
                            <img src="https://images.unsplash.com/photo-1600891964599-f61ba0e24092?w=700&h=500&fit=crop"
                                 alt="Mexican restaurant owner"
                                 class="rounded-2xl w-full object-cover shadow-2xl shadow-[#D4AF37]/10"
                                 loading="lazy"
                                 onerror="this.style.display='none'">
                            <div class="absolute -bottom-4 -left-4 bg-[#1A1A1A] rounded-xl px-5 py-3 border border-[#D4AF37]/20 shadow-lg">
                                <div class="flex items-center gap-2">
                                    <span class="text-[#D4AF37] font-bold text-xl">{{ number_format($totalRestaurantsRounded) }}+</span>
                                    <span class="text-[#CCCCCC] text-sm">Restaurants</span>
                                </div>
                            </div>
                            <div class="absolute -top-4 -right-4 bg-[#1A1A1A] rounded-xl px-5 py-3 border border-[#D4AF37]/20 shadow-lg">
                                <div class="flex items-center gap-2">
                                    <span class="text-[#D4AF37] font-bold text-xl">50</span>
                                    <span class="text-[#CCCCCC] text-sm">States</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ======== 8. FINAL CTA ======== --}}
            <div class="famer-reveal bg-[#0B0B0B] py-24 relative overflow-hidden">
                {{-- Subtle gold radial glow --}}
                <div class="absolute inset-0 pointer-events-none" style="background: radial-gradient(ellipse at center, rgba(212,175,55,0.06) 0%, transparent 60%);"></div>

                <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                    <h2 class="font-playfair text-3xl md:text-5xl font-bold mb-4 gold-gradient-text">Find out your score in 60 seconds</h2>
                    <p class="font-poppins text-lg text-[#CCCCCC]/60 mb-8">Free. No credit card. Instant results.</p>

                    <a href="#" onclick="window.scrollTo({top:0,behavior:'smooth'});return false;"
                       class="inline-block px-10 py-4 font-poppins font-bold text-lg rounded-xl text-[#0B0B0B] transition-all hover:scale-105"
                       style="background: linear-gradient(135deg, #D4AF37, #E8C67A); box-shadow: 0 4px 25px rgba(212,175,55,0.3);"
                       onmouseover="this.style.boxShadow='0 8px 40px rgba(212,175,55,0.5)'"
                       onmouseout="this.style.boxShadow='0 4px 25px rgba(212,175,55,0.3)'"
                    >
                        Get Your FAMER Score
                    </a>

                    <div class="flex items-center justify-center gap-6 mt-8 text-sm font-poppins text-[#CCCCCC]/40">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-[#D4AF37]/50" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/></svg>
                            {{ number_format($totalRestaurantsRounded) }}+ restaurants
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-[#D4AF37]/50" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                            50 states
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-[#D4AF37]/50" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>
                            8 platforms
                        </span>
                    </div>
                </div>
            </div>

        @endif

        {{-- GSAP Scroll Reveal Script --}}
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Intersection Observer for fade-in reveals
            const revealObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const el = entry.target;

                        if (el.classList.contains('famer-reveal')) {
                            gsap.fromTo(el,
                                { opacity: 0, y: 40 },
                                { opacity: 1, y: 0, duration: 0.8, ease: 'power3.out' }
                            );
                        }

                        if (el.classList.contains('famer-reveal-step')) {
                            // Stagger siblings
                            const parent = el.parentElement;
                            const siblings = parent.querySelectorAll('.famer-reveal-step');
                            const idx = Array.from(siblings).indexOf(el);
                            gsap.fromTo(el,
                                { opacity: 0, y: 30 },
                                { opacity: 1, y: 0, duration: 0.6, delay: idx * 0.12, ease: 'power3.out' }
                            );
                        }

                        if (el.classList.contains('famer-score-arrow')) {
                            gsap.fromTo(el,
                                { x: -10, opacity: 0 },
                                { x: 0, opacity: 1, duration: 0.5, delay: 0.3, ease: 'power2.out' }
                            );
                        }

                        revealObserver.unobserve(el);
                    }
                });
            }, { threshold: 0.15 });

            // Observe all reveal elements
            document.querySelectorAll('.famer-reveal, .famer-reveal-step, .famer-score-arrow').forEach(function(el) {
                gsap.set(el, { opacity: 0 });
                revealObserver.observe(el);
            });
        });
        </script>

    {{-- ============================================================ --}}
    {{-- EMAIL CAPTURE MODAL                                           --}}
    {{-- ============================================================ --}}
    @if ($showEmailCapture)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-[#1A1A1A] rounded-xl max-w-md w-full p-6" style="border: 1px solid rgba(212,175,55,0.2); box-shadow: 0 0 40px rgba(212,175,55,0.1);">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-playfair text-xl font-bold text-[#F5F5F5]">Get Full Report</h3>
                    <button
                        wire:click="$set('showEmailCapture', false)"
                        class="text-[#CCCCCC]/40 hover:text-[#CCCCCC]"
                    >
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>

                <p class="font-poppins text-[#CCCCCC]/60 mb-6">
                    Enter your email to receive the full report with all recommendations.
                </p>

                <div class="space-y-4">
                    <div>
                        <label for="leadEmail" class="block font-poppins text-sm font-medium text-[#CCCCCC] mb-1">
                            Email <span class="text-[#D4AF37]">*</span>
                        </label>
                        <input
                            type="email"
                            id="leadEmail"
                            wire:model="leadEmail"
                            class="w-full rounded-lg bg-[#2A2A2A] border-[#2A2A2A] text-[#F5F5F5] placeholder-[#CCCCCC]/40 shadow-sm focus:border-[#D4AF37] focus:ring-[#D4AF37] font-poppins"
                            placeholder="you@email.com"
                        >
                        @error('leadEmail')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="leadName" class="block font-poppins text-sm font-medium text-[#CCCCCC] mb-1">
                            Name (optional)
                        </label>
                        <input
                            type="text"
                            id="leadName"
                            wire:model="leadName"
                            class="w-full rounded-lg bg-[#2A2A2A] border-[#2A2A2A] text-[#F5F5F5] placeholder-[#CCCCCC]/40 shadow-sm focus:border-[#D4AF37] focus:ring-[#D4AF37] font-poppins"
                            placeholder="Your name"
                        >
                    </div>

                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            id="isOwner"
                            wire:model="isOwner"
                            class="rounded bg-[#2A2A2A] border-[#2A2A2A] text-[#D4AF37] focus:ring-[#D4AF37]"
                        >
                        <label for="isOwner" class="ml-2 font-poppins text-sm text-[#CCCCCC]/70">
                            I am the owner/manager of this restaurant
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            id="marketingConsent"
                            wire:model="marketingConsent"
                            class="rounded bg-[#2A2A2A] border-[#2A2A2A] text-[#D4AF37] focus:ring-[#D4AF37]"
                        >
                        <label for="marketingConsent" class="ml-2 font-poppins text-sm text-[#CCCCCC]/70">
                            I agree to receive tips and promotions from FAMER
                        </label>
                    </div>

                    <button
                        wire:click="submitEmailForReport"
                        wire:loading.attr="disabled"
                        class="w-full py-3 font-poppins font-bold rounded-lg transition-all disabled:opacity-50 text-[#0B0B0B]"
                        style="background: linear-gradient(135deg, #D4AF37, #E8C67A); box-shadow: 0 4px 15px rgba(212,175,55,0.3);"
                    >
                        <span wire:loading.remove wire:target="submitEmailForReport">
                            Send Report
                        </span>
                        <span wire:loading wire:target="submitEmailForReport" class="inline-flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-[#0B0B0B]" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Sending...
                        </span>
                    </button>
                </div>

                <p class="mt-4 font-poppins text-xs text-[#CCCCCC]/30 text-center">
                    We never share your information with third parties.
                </p>
            </div>
        </div>
    @endif
</div>
