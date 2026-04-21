<div
    x-data="{
        cartOpen: false,
        isMobile: window.innerWidth < 1024,
        init() {
            window.addEventListener('resize', () => {
                this.isMobile = window.innerWidth < 1024;
            });
        }
    }"
    class="min-h-screen bg-[#0B0B0B] text-[#F5F5F5] font-[Poppins,sans-serif]"
>

    {{-- ─── STEP INDICATOR ──────────────────────────────────────────── --}}
    <div class="sticky top-0 z-30 bg-[#0B0B0B]/95 backdrop-blur border-b border-[#2A2A2A]">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between gap-4">

            {{-- Restaurant name --}}
            <div class="flex items-center gap-2 min-w-0">
                <span class="text-[#D4AF37] font-semibold truncate text-sm sm:text-base">
                    {{ $restaurant->name }}
                </span>
            </div>

            {{-- Steps --}}
            <ol class="flex items-center gap-1 sm:gap-3 text-xs sm:text-sm flex-shrink-0">
                @foreach(['menu' => 'Menu', 'checkout' => 'Checkout', 'confirmation' => 'Confirm'] as $key => $label)
                    @php
                        $steps    = ['menu', 'checkout', 'confirmation'];
                        $current  = array_search($step, $steps);
                        $thisStep = array_search($key, $steps);
                        $done     = $thisStep < $current;
                        $active   = $thisStep === $current;
                    @endphp
                    <li class="flex items-center gap-1 sm:gap-2">
                        <span @class([
                            'w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold transition-all',
                            'bg-[#D4AF37] text-[#0B0B0B]' => $active || $done,
                            'bg-[#2A2A2A] text-[#666]'    => !$active && !$done,
                        ])>
                            @if($done)
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            @else
                                {{ $thisStep + 1 }}
                            @endif
                        </span>
                        <span @class([
                            'hidden sm:inline transition-colors',
                            'text-[#D4AF37] font-semibold' => $active,
                            'text-[#F5F5F5]'               => $done,
                            'text-[#555]'                  => !$active && !$done,
                        ])>{{ $label }}</span>
                    </li>
                    @if(!$loop->last)
                        <li class="text-[#2A2A2A]">›</li>
                    @endif
                @endforeach
            </ol>

            {{-- Cart toggle (mobile) --}}
            @if($step === 'menu')
            <button
                @click="cartOpen = !cartOpen"
                class="lg:hidden relative flex items-center gap-1 bg-[#1A1A1A] border border-[#2A2A2A] rounded-full px-3 py-1.5 text-sm hover:border-[#D4AF37] transition-colors"
            >
                <svg class="w-4 h-4 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m5-9v9m4-9v9m5-4H7"/>
                </svg>
                @if($this->cartCount > 0)
                    <span class="font-semibold text-[#D4AF37]">{{ $this->cartCount }}</span>
                    <span class="text-[#F5F5F5]/70">${{ number_format($this->cartTotal, 2) }}</span>
                @else
                    <span class="text-[#555]">Cart</span>
                @endif
            </button>
            @endif
        </div>
    </div>

    {{-- ─── MAIN LAYOUT ─────────────────────────────────────────────── --}}
    <div class="max-w-6xl mx-auto px-4 py-6">

        {{-- ════════════════════════════════════════════════════════════ --}}
        {{-- STEP: MENU                                                  --}}
        {{-- ════════════════════════════════════════════════════════════ --}}
        @if($step === 'menu')
        <div class="flex flex-col lg:flex-row gap-6">

            {{-- ── MENU CONTENT ──────────────────────────────────────── --}}
            <div class="flex-1 min-w-0">

                {{-- Category Nav --}}
                @if($this->categories->count() > 1)
                <div class="mb-6 overflow-x-auto scrollbar-none -mx-4 px-4">
                    <div class="flex gap-2 w-max">
                        <button
                            wire:click="setActiveCategory(null)"
                            @class([
                                'px-4 py-1.5 rounded-full text-sm font-medium border transition-all whitespace-nowrap',
                                'bg-[#D4AF37] text-[#0B0B0B] border-[#D4AF37]' => $activeCategoryId === null,
                                'bg-transparent text-[#F5F5F5]/70 border-[#2A2A2A] hover:border-[#D4AF37]' => $activeCategoryId !== null,
                            ])
                        >All</button>
                        @foreach($this->categories as $cat)
                            <button
                                wire:click="setActiveCategory({{ $cat->id }})"
                                @class([
                                    'px-4 py-1.5 rounded-full text-sm font-medium border transition-all whitespace-nowrap',
                                    'bg-[#D4AF37] text-[#0B0B0B] border-[#D4AF37]' => $activeCategoryId === $cat->id,
                                    'bg-transparent text-[#F5F5F5]/70 border-[#2A2A2A] hover:border-[#D4AF37]' => $activeCategoryId !== $cat->id,
                                ])
                            >{{ $cat->name }}</button>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Categories + Items --}}
                @forelse($this->filteredCategories as $category)
                <section class="mb-10" id="cat-{{ $category->id }}">
                    <div class="flex items-center gap-3 mb-4">
                        @if($category->icon)
                            <span class="text-xl">{{ $category->icon }}</span>
                        @endif
                        <h2 class="text-lg font-semibold text-[#F5F5F5] font-[Playfair_Display,serif]">
                            {{ $category->name }}
                        </h2>
                        <div class="flex-1 h-px bg-[#2A2A2A]"></div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($category->activeItems as $item)
                        <div class="group bg-[#111111] border border-[#2A2A2A] rounded-xl overflow-hidden hover:border-[#D4AF37]/40 transition-all duration-200 flex flex-col">

                            {{-- Item Image --}}
                            @if($item->image)
                            <div class="relative h-36 overflow-hidden flex-shrink-0">
                                <img
                                    src="{{ $item->image }}"
                                    alt="{{ $item->name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                    loading="lazy"
                                >
                                @if($item->is_popular)
                                    <span class="absolute top-2 left-2 bg-[#D4AF37] text-[#0B0B0B] text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wide">Popular</span>
                                @elseif($item->is_new)
                                    <span class="absolute top-2 left-2 bg-[#1F3D2B] text-green-400 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wide">New</span>
                                @endif
                                @if($item->hasDiscount())
                                    <span class="absolute top-2 right-2 bg-[#8B1E1E] text-white text-[10px] font-bold px-2 py-0.5 rounded-full">-{{ $item->discount_percentage }}%</span>
                                @endif
                            </div>
                            @endif

                            {{-- Item Body --}}
                            <div class="p-3 flex flex-col flex-1">
                                <div class="flex items-start justify-between gap-2 mb-1">
                                    <h3 class="font-semibold text-[#F5F5F5] text-sm leading-tight">{{ $item->name }}</h3>
                                    <div class="flex-shrink-0 text-right">
                                        @if($item->hasDiscount())
                                            <div class="text-[10px] text-[#666] line-through">${{ number_format($item->price, 2) }}</div>
                                            <div class="text-[#D4AF37] font-bold text-sm">${{ number_format($item->sale_price, 2) }}</div>
                                        @else
                                            <div class="text-[#D4AF37] font-bold text-sm">${{ number_format($item->price, 2) }}</div>
                                        @endif
                                    </div>
                                </div>

                                @if($item->description)
                                    <p class="text-[#888] text-xs leading-relaxed mb-2 flex-1 line-clamp-2">{{ $item->description }}</p>
                                @endif

                                {{-- Dietary Tags --}}
                                @if(!empty($item->dietary_tags))
                                <div class="flex flex-wrap gap-1 mb-3">
                                    @foreach($item->dietary_tags as $tag)
                                        @php
                                            $tagLabels = ['vegetarian' => '🥬', 'vegan' => '🌱', 'gluten-free' => '🌾', 'dairy-free' => '🥛', 'spicy' => '🌶️'];
                                        @endphp
                                        <span class="text-[10px] bg-[#1A1A1A] text-[#888] border border-[#2A2A2A] rounded px-1.5 py-0.5">
                                            {{ $tagLabels[$tag] ?? $tag }}
                                        </span>
                                    @endforeach
                                </div>
                                @endif

                                {{-- Cart Controls --}}
                                <div class="mt-auto">
                                    @if(isset($cart[$item->id]))
                                    <div class="flex items-center justify-between bg-[#1A1A1A] border border-[#D4AF37]/30 rounded-lg px-2 py-1">
                                        <button
                                            wire:click="removeFromCart({{ $item->id }})"
                                            wire:loading.attr="disabled"
                                            class="w-7 h-7 flex items-center justify-center text-[#D4AF37] hover:bg-[#D4AF37]/10 rounded transition-colors font-bold text-lg"
                                        >−</button>
                                        <span class="font-semibold text-[#F5F5F5] text-sm min-w-[1.5rem] text-center">{{ $cart[$item->id]['quantity'] }}</span>
                                        <button
                                            wire:click="addToCart({{ $item->id }})"
                                            wire:loading.attr="disabled"
                                            class="w-7 h-7 flex items-center justify-center text-[#D4AF37] hover:bg-[#D4AF37]/10 rounded transition-colors font-bold text-lg"
                                        >+</button>
                                    </div>
                                    @else
                                    <button
                                        wire:click="addToCart({{ $item->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="addToCart({{ $item->id }})"
                                        class="w-full flex items-center justify-center gap-1.5 bg-[#D4AF37]/10 border border-[#D4AF37]/30 text-[#D4AF37] rounded-lg py-1.5 text-sm font-medium hover:bg-[#D4AF37]/20 hover:border-[#D4AF37] transition-all active:scale-95"
                                    >
                                        <span wire:loading.remove wire:target="addToCart({{ $item->id }})">+ Add</span>
                                        <span wire:loading wire:target="addToCart({{ $item->id }})" class="inline-flex items-center gap-1">
                                            <svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                        </span>
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </section>
                @empty
                <div class="text-center py-20 text-[#555]">
                    <svg class="w-12 h-12 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <p class="text-sm">No menu items available right now.</p>
                </div>
                @endforelse
            </div>

            {{-- ── DESKTOP SIDEBAR CART ──────────────────────────────── --}}
            <aside class="hidden lg:flex flex-col w-80 flex-shrink-0">
                <div class="sticky top-[72px]">
                    <div class="bg-[#111111] border border-[#2A2A2A] rounded-xl overflow-hidden">
                        <div class="px-4 py-3 border-b border-[#2A2A2A] flex items-center justify-between">
                            <h3 class="font-semibold text-[#F5F5F5] flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m5-9v9m4-9v9m5-4H7"/></svg>
                                Your Order
                            </h3>
                            @if($this->cartCount > 0)
                                <span class="bg-[#D4AF37] text-[#0B0B0B] text-xs font-bold px-2 py-0.5 rounded-full">{{ $this->cartCount }}</span>
                            @endif
                        </div>

                        @if(empty($cart))
                        <div class="px-4 py-10 text-center text-[#555]">
                            <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            <p class="text-sm">Your cart is empty</p>
                            <p class="text-xs mt-1">Add items from the menu</p>
                        </div>
                        @else
                        <div class="divide-y divide-[#2A2A2A] max-h-80 overflow-y-auto">
                            @foreach($cart as $itemId => $cartItem)
                            <div class="px-4 py-3 flex items-center gap-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-[#F5F5F5] truncate">{{ $cartItem['name'] }}</p>
                                    <p class="text-xs text-[#D4AF37]">${{ number_format($cartItem['price'], 2) }} ea.</p>
                                </div>
                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    <button wire:click="removeFromCart({{ $itemId }})" class="w-6 h-6 flex items-center justify-center text-[#888] hover:text-[#D4AF37] transition-colors rounded">−</button>
                                    <span class="text-sm font-semibold text-[#F5F5F5] w-5 text-center">{{ $cartItem['quantity'] }}</span>
                                    <button wire:click="addToCart({{ $itemId }})" class="w-6 h-6 flex items-center justify-center text-[#888] hover:text-[#D4AF37] transition-colors rounded">+</button>
                                    <button wire:click="deleteFromCart({{ $itemId }})" class="w-6 h-6 flex items-center justify-center text-[#555] hover:text-red-500 transition-colors rounded ml-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                                <div class="w-14 text-right flex-shrink-0">
                                    <p class="text-sm font-semibold text-[#F5F5F5]">${{ number_format($cartItem['price'] * $cartItem['quantity'], 2) }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        {{-- Totals --}}
                        <div class="px-4 py-3 border-t border-[#2A2A2A] space-y-1.5">
                            <div class="flex justify-between text-sm text-[#888]">
                                <span>Subtotal</span>
                                <span>${{ number_format($this->cartSubtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-[#888]">
                                <span>Tax (8.875%)</span>
                                <span>${{ number_format($this->cartTax, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-[#F5F5F5] font-semibold pt-1 border-t border-[#2A2A2A]">
                                <span>Total</span>
                                <span class="text-[#D4AF37]">${{ number_format($this->cartTotal, 2) }}</span>
                            </div>
                        </div>

                        <div class="px-4 pb-4">
                            <button
                                wire:click="goToCheckout"
                                class="w-full bg-[#D4AF37] hover:bg-[#C9A227] text-[#0B0B0B] font-bold py-2.5 rounded-lg transition-colors active:scale-95 text-sm"
                            >
                                Proceed to Checkout →
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </aside>

        </div>

        {{-- ── MOBILE BOTTOM BAR ─────────────────────────────────────── --}}
        @if(!empty($cart))
        <div
            class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-[#111111] border-t border-[#2A2A2A] px-4 py-3 safe-bottom"
        >
            {{-- Mobile Cart Drawer --}}
            <div
                x-show="cartOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-4"
                class="mb-3 bg-[#0B0B0B] border border-[#2A2A2A] rounded-xl overflow-hidden"
            >
                <div class="px-4 py-2.5 border-b border-[#2A2A2A] flex items-center justify-between">
                    <span class="text-sm font-semibold text-[#F5F5F5]">Order Summary</span>
                    <button @click="cartOpen = false" class="text-[#555] hover:text-[#F5F5F5]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="divide-y divide-[#2A2A2A] max-h-60 overflow-y-auto">
                    @foreach($cart as $itemId => $cartItem)
                    <div class="px-4 py-2.5 flex items-center gap-2">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-[#F5F5F5] truncate">{{ $cartItem['name'] }}</p>
                        </div>
                        <div class="flex items-center gap-1 flex-shrink-0">
                            <button wire:click="removeFromCart({{ $itemId }})" class="w-6 h-6 flex items-center justify-center text-[#888] hover:text-[#D4AF37] rounded">−</button>
                            <span class="text-sm font-semibold w-4 text-center text-[#F5F5F5]">{{ $cartItem['quantity'] }}</span>
                            <button wire:click="addToCart({{ $itemId }})" class="w-6 h-6 flex items-center justify-center text-[#888] hover:text-[#D4AF37] rounded">+</button>
                        </div>
                        <span class="text-sm font-semibold text-[#D4AF37] w-14 text-right">${{ number_format($cartItem['price'] * $cartItem['quantity'], 2) }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="px-4 py-2.5 border-t border-[#2A2A2A] space-y-1">
                    <div class="flex justify-between text-xs text-[#888]">
                        <span>Subtotal</span><span>${{ number_format($this->cartSubtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-xs text-[#888]">
                        <span>Tax (8.875%)</span><span>${{ number_format($this->cartTax, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm font-bold text-[#F5F5F5] pt-1">
                        <span>Total</span><span class="text-[#D4AF37]">${{ number_format($this->cartTotal, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Bottom bar row --}}
            <div class="flex items-center gap-3">
                <button @click="cartOpen = !cartOpen" class="flex items-center gap-2 text-sm text-[#F5F5F5]/70">
                    <span class="bg-[#D4AF37] text-[#0B0B0B] font-bold text-xs w-5 h-5 rounded-full flex items-center justify-center">{{ $this->cartCount }}</span>
                    <span>items</span>
                </button>
                <button
                    wire:click="goToCheckout"
                    class="flex-1 bg-[#D4AF37] hover:bg-[#C9A227] text-[#0B0B0B] font-bold py-2.5 rounded-lg transition-colors text-sm"
                >
                    Checkout · ${{ number_format($this->cartTotal, 2) }}
                </button>
            </div>
        </div>
        @endif
        {{-- /STEP: MENU --}}



        {{-- ════════════════════════════════════════════════════════════ --}}
        {{-- STEP: CHECKOUT                                              --}}
        {{-- ════════════════════════════════════════════════════════════ --}}
        @elseif($step === 'checkout')
        <div class="flex flex-col lg:flex-row gap-6">

            {{-- ── CHECKOUT FORM ─────────────────────────────────────── --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-3 mb-6">
                    <button
                        wire:click="backToMenu"
                        class="text-[#888] hover:text-[#F5F5F5] flex items-center gap-1 text-sm transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Back to Menu
                    </button>
                </div>

                <form wire:submit="placeOrder" class="space-y-6">

                    {{-- Contact Info --}}
                    <fieldset class="bg-[#111111] border border-[#2A2A2A] rounded-xl p-5">
                        <legend class="text-sm font-semibold text-[#D4AF37] px-2 -ml-2 mb-4">Contact Information</legend>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-[#888] mb-1.5">Full Name <span class="text-[#8B1E1E]">*</span></label>
                                <input
                                    type="text"
                                    wire:model="customer_name"
                                    placeholder="Your name"
                                    autocomplete="name"
                                    class="w-full bg-[#0B0B0B] border @error('customer_name') border-red-500 @else border-[#2A2A2A] @enderror rounded-lg px-3 py-2.5 text-sm text-[#F5F5F5] placeholder-[#555] focus:outline-none focus:border-[#D4AF37] transition-colors"
                                >
                                @error('customer_name')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-[#888] mb-1.5">Email <span class="text-[#8B1E1E]">*</span></label>
                                    <input
                                        type="email"
                                        wire:model="customer_email"
                                        placeholder="you@email.com"
                                        autocomplete="email"
                                        class="w-full bg-[#0B0B0B] border @error('customer_email') border-red-500 @else border-[#2A2A2A] @enderror rounded-lg px-3 py-2.5 text-sm text-[#F5F5F5] placeholder-[#555] focus:outline-none focus:border-[#D4AF37] transition-colors"
                                    >
                                    @error('customer_email')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-[#888] mb-1.5">Phone <span class="text-[#8B1E1E]">*</span></label>
                                    <input
                                        type="tel"
                                        wire:model="customer_phone"
                                        placeholder="(555) 000-0000"
                                        autocomplete="tel"
                                        class="w-full bg-[#0B0B0B] border @error('customer_phone') border-red-500 @else border-[#2A2A2A] @enderror rounded-lg px-3 py-2.5 text-sm text-[#F5F5F5] placeholder-[#555] focus:outline-none focus:border-[#D4AF37] transition-colors"
                                    >
                                    @error('customer_phone')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    {{-- Order Type --}}
                    <fieldset class="bg-[#111111] border border-[#2A2A2A] rounded-xl p-5">
                        <legend class="text-sm font-semibold text-[#D4AF37] px-2 -ml-2 mb-4">Order Type</legend>

                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <label @class([
                                'relative flex flex-col items-center gap-2 rounded-xl border-2 p-4 cursor-pointer transition-all',
                                'border-[#D4AF37] bg-[#D4AF37]/5' => $order_type === 'pickup',
                                'border-[#2A2A2A] hover:border-[#D4AF37]/40' => $order_type !== 'pickup',
                            ])>
                                <input type="radio" wire:model="order_type" value="pickup" class="sr-only">
                                <svg class="w-7 h-7 @if($order_type === 'pickup') text-[#D4AF37] @else text-[#555] @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                <span class="text-sm font-semibold @if($order_type === 'pickup') text-[#D4AF37] @else text-[#888] @endif">Pickup</span>
                                <span class="text-xs text-[#555]">Pick up at restaurant</span>
                            </label>

                            <label @class([
                                'relative flex flex-col items-center gap-2 rounded-xl border-2 p-4 cursor-pointer transition-all',
                                'border-[#D4AF37] bg-[#D4AF37]/5' => $order_type === 'delivery',
                                'border-[#2A2A2A] hover:border-[#D4AF37]/40' => $order_type !== 'delivery',
                            ])>
                                <input type="radio" wire:model="order_type" value="delivery" class="sr-only">
                                <svg class="w-7 h-7 @if($order_type === 'delivery') text-[#D4AF37] @else text-[#555] @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                                <span class="text-sm font-semibold @if($order_type === 'delivery') text-[#D4AF37] @else text-[#888] @endif">Delivery</span>
                                <span class="text-xs text-[#555]">Delivered to you</span>
                            </label>
                        </div>

                        @if($order_type === 'delivery')
                        <div class="space-y-3 pt-2 border-t border-[#2A2A2A]">
                            <div>
                                <label class="block text-xs font-medium text-[#888] mb-1.5">Delivery Address <span class="text-[#8B1E1E]">*</span></label>
                                <input
                                    type="text"
                                    wire:model="delivery_address"
                                    placeholder="123 Main St, Apt 4B"
                                    autocomplete="street-address"
                                    class="w-full bg-[#0B0B0B] border @error('delivery_address') border-red-500 @else border-[#2A2A2A] @enderror rounded-lg px-3 py-2.5 text-sm text-[#F5F5F5] placeholder-[#555] focus:outline-none focus:border-[#D4AF37] transition-colors"
                                >
                                @error('delivery_address')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-[#888] mb-1.5">City <span class="text-[#8B1E1E]">*</span></label>
                                <input
                                    type="text"
                                    wire:model="delivery_city"
                                    placeholder="New York"
                                    autocomplete="address-level2"
                                    class="w-full bg-[#0B0B0B] border @error('delivery_city') border-red-500 @else border-[#2A2A2A] @enderror rounded-lg px-3 py-2.5 text-sm text-[#F5F5F5] placeholder-[#555] focus:outline-none focus:border-[#D4AF37] transition-colors"
                                >
                                @error('delivery_city')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>
                            <p class="text-xs text-[#555] flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Delivery fee will be calculated by the restaurant
                            </p>
                        </div>
                        @endif
                    </fieldset>

                    {{-- Payment Method --}}
                    <fieldset class="bg-[#111111] border border-[#2A2A2A] rounded-xl p-5">
                        <legend class="text-sm font-semibold text-[#D4AF37] px-2 -ml-2 mb-4">Payment Method</legend>
                        <div class="grid grid-cols-2 gap-3">
                            <label @class([
                                'flex items-center gap-3 rounded-lg border-2 p-3 cursor-pointer transition-all',
                                'border-[#D4AF37] bg-[#D4AF37]/5' => $payment_method === 'cash',
                                'border-[#2A2A2A] hover:border-[#D4AF37]/40' => $payment_method !== 'cash',
                            ])>
                                <input type="radio" wire:model="payment_method" value="cash" class="sr-only">
                                <svg class="w-5 h-5 @if($payment_method === 'cash') text-[#D4AF37] @else text-[#555] @endif flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span class="text-sm font-medium @if($payment_method === 'cash') text-[#D4AF37] @else text-[#888] @endif">Cash</span>
                            </label>
                            <label @class([
                                'flex items-center gap-3 rounded-lg border-2 p-3 cursor-pointer transition-all',
                                'border-[#D4AF37] bg-[#D4AF37]/5' => $payment_method === 'card',
                                'border-[#2A2A2A] hover:border-[#D4AF37]/40' => $payment_method !== 'card',
                            ])>
                                <input type="radio" wire:model="payment_method" value="card" class="sr-only">
                                <svg class="w-5 h-5 @if($payment_method === 'card') text-[#D4AF37] @else text-[#555] @endif flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                <span class="text-sm font-medium @if($payment_method === 'card') text-[#D4AF37] @else text-[#888] @endif">Card</span>
                            </label>
                        </div>
                    </fieldset>

                    {{-- Special Instructions --}}
                    <div class="bg-[#111111] border border-[#2A2A2A] rounded-xl p-5">
                        <label class="block text-sm font-semibold text-[#D4AF37] mb-3">Special Instructions <span class="text-[#555] font-normal text-xs">(optional)</span></label>
                        <textarea
                            wire:model="special_instructions"
                            rows="3"
                            placeholder="Allergies, cooking preferences, notes for the kitchen..."
                            class="w-full bg-[#0B0B0B] border border-[#2A2A2A] rounded-lg px-3 py-2.5 text-sm text-[#F5F5F5] placeholder-[#555] focus:outline-none focus:border-[#D4AF37] transition-colors resize-none"
                        ></textarea>
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-not-allowed"
                        class="w-full bg-[#D4AF37] hover:bg-[#C9A227] text-[#0B0B0B] font-bold py-3.5 rounded-xl transition-colors active:scale-[.99] text-base flex items-center justify-center gap-2"
                    >
                        <span wire:loading.remove wire:target="placeOrder">
                            Place Order · ${{ number_format($this->cartTotal, 2) }}
                        </span>
                        <span wire:loading wire:target="placeOrder" class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            Placing your order...
                        </span>
                    </button>
                </form>
            </div>

            {{-- ── CHECKOUT ORDER SUMMARY ────────────────────────────── --}}
            <aside class="lg:w-80 flex-shrink-0">
                <div class="sticky top-[72px] bg-[#111111] border border-[#2A2A2A] rounded-xl overflow-hidden">
                    <div class="px-4 py-3 border-b border-[#2A2A2A]">
                        <h3 class="font-semibold text-[#F5F5F5] text-sm">Order Summary</h3>
                    </div>
                    <div class="divide-y divide-[#2A2A2A]">
                        @foreach($cart as $itemId => $cartItem)
                        <div class="px-4 py-2.5 flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="bg-[#2A2A2A] text-[#D4AF37] text-xs font-bold w-5 h-5 rounded flex items-center justify-center flex-shrink-0">{{ $cartItem['quantity'] }}</span>
                                <span class="text-sm text-[#F5F5F5] truncate">{{ $cartItem['name'] }}</span>
                            </div>
                            <span class="text-sm font-semibold text-[#F5F5F5] flex-shrink-0">${{ number_format($cartItem['price'] * $cartItem['quantity'], 2) }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="px-4 py-3 border-t border-[#2A2A2A] space-y-1.5">
                        <div class="flex justify-between text-xs text-[#888]">
                            <span>Subtotal</span><span>${{ number_format($this->cartSubtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-xs text-[#888]">
                            <span>Tax (8.875%)</span><span>${{ number_format($this->cartTax, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-xs text-[#888]">
                            <span>Delivery Fee</span><span class="text-green-400">TBD</span>
                        </div>
                        <div class="flex justify-between font-semibold text-[#F5F5F5] pt-1.5 border-t border-[#2A2A2A]">
                            <span>Total</span>
                            <span class="text-[#D4AF37] text-base">${{ number_format($this->cartTotal, 2) }}</span>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
        {{-- /STEP: CHECKOUT --}}



        {{-- ════════════════════════════════════════════════════════════ --}}
        {{-- STEP: CONFIRMATION                                          --}}
        {{-- ════════════════════════════════════════════════════════════ --}}
        @elseif($step === 'confirmation')
        <div class="max-w-lg mx-auto py-12 text-center">
            <div class="w-20 h-20 bg-[#1F3D2B] border-2 border-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-[#F5F5F5] mb-2 font-[Playfair_Display,serif]">Order Placed!</h1>
            <p class="text-[#888] mb-6">Thank you, your order has been received.</p>

            @if($confirmedOrderNumber)
            <div class="bg-[#111111] border border-[#2A2A2A] rounded-xl px-6 py-5 mb-6 text-left space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-[#888]">Order Number</span>
                    <span class="font-bold text-[#D4AF37] font-mono">{{ $confirmedOrderNumber }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-[#888]">Restaurant</span>
                    <span class="text-[#F5F5F5]">{{ $restaurant->name }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-[#888]">Status</span>
                    <span class="text-yellow-400 flex items-center gap-1">
                        <span class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></span>
                        Pending confirmation
                    </span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-[#888]">Total</span>
                    <span class="font-bold text-[#D4AF37]">${{ number_format($this->cartTotal, 2) }}</span>
                </div>
            </div>
            @endif

            <p class="text-xs text-[#555] mb-6">
                A confirmation will be sent to <span class="text-[#888]">{{ $customer_email }}</span>.<br>
                The restaurant will contact you if needed.
            </p>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                @if($confirmedOrderNumber)
                <a
                    href="/pedido/{{ $confirmedOrderNumber }}"
                    class="bg-[#D4AF37] hover:bg-[#C9A227] text-[#0B0B0B] font-bold px-6 py-2.5 rounded-lg transition-colors text-sm"
                >
                    Track Order
                </a>
                @endif
                <a
                    href="/restaurante/{{ $restaurant->slug }}"
                    class="border border-[#2A2A2A] hover:border-[#D4AF37] text-[#888] hover:text-[#F5F5F5] px-6 py-2.5 rounded-lg transition-colors text-sm"
                >
                    Back to Restaurant
                </a>
            </div>
        </div>
        {{-- /STEP: CONFIRMATION --}}

        @endif
    </div>
</div>
