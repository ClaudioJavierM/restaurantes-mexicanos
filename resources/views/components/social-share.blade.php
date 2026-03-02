@props([
    'url' => url()->current(),
    'title' => '',
    'description' => '',
    'layout' => 'horizontal', // horizontal, vertical, compact
    'showLabels' => false,
])

@php
    $encodedUrl = urlencode($url);
    $encodedTitle = urlencode($title);
    $encodedText = urlencode($description ?: $title);

    $facebookUrl = "https://www.facebook.com/sharer/sharer.php?u={$encodedUrl}";
    $twitterUrl = "https://twitter.com/intent/tweet?url={$encodedUrl}&text={$encodedText}";
    $whatsappUrl = "https://wa.me/?text={$encodedText}%20{$encodedUrl}";
    $telegramUrl = "https://t.me/share/url?url={$encodedUrl}&text={$encodedText}";
    $smsUrl = "sms:?body={$encodedText}%20{$encodedUrl}";
    $emailUrl = "mailto:?subject={$encodedTitle}&body={$encodedText}%20{$encodedUrl}";
@endphp

<div {{ $attributes->merge(['class' => 'social-share']) }}>
    @if($layout === 'horizontal')
        <!-- Horizontal Layout with Native Share Button -->
        <div class="flex items-center flex-wrap gap-2">
            <!-- Native Share (for mobile) -->
            <button type="button"
                    onclick="nativeShare('{{ $title }}', '{{ $description }}', '{{ $url }}')"
                    class="native-share-btn hidden items-center justify-center px-4 py-2 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white rounded-lg transition-all duration-300 hover:scale-105 shadow-lg font-semibold"
                    title="{{ app()->getLocale() === 'en' ? 'Share' : 'Compartir' }}">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                </svg>
                {{ app()->getLocale() === 'en' ? 'Share' : 'Compartir' }}
            </button>
            
            <!-- Fallback buttons (desktop and when native share not available) -->
            <div class="share-fallback flex items-center gap-2">
                <!-- Facebook -->
                <a href="{{ $facebookUrl }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="flex items-center justify-center w-10 h-10 bg-[#1877F2] hover:bg-[#0C63D4] text-white rounded-full transition-all duration-300 hover:scale-110 shadow-lg"
                   title="{{ __('app.share_on_facebook') }}">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </a>

                <!-- X (Twitter) -->
                <a href="{{ $twitterUrl }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="flex items-center justify-center w-10 h-10 bg-black hover:bg-gray-800 text-white rounded-full transition-all duration-300 hover:scale-110 shadow-lg"
                   title="{{ app()->getLocale() === 'en' ? 'Share on X' : 'Compartir en X' }}">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                </a>

                <!-- WhatsApp -->
                <a href="{{ $whatsappUrl }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="flex items-center justify-center w-10 h-10 bg-[#25D366] hover:bg-[#1EBE57] text-white rounded-full transition-all duration-300 hover:scale-110 shadow-lg"
                   title="{{ __('app.share_on_whatsapp') }}">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                </a>
                
                <!-- Telegram -->
                <a href="{{ $telegramUrl }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="flex items-center justify-center w-10 h-10 bg-[#0088cc] hover:bg-[#006699] text-white rounded-full transition-all duration-300 hover:scale-110 shadow-lg"
                   title="{{ app()->getLocale() === 'en' ? 'Share on Telegram' : 'Compartir en Telegram' }}">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                    </svg>
                </a>

                <!-- SMS (mobile only) -->
                <a href="{{ $smsUrl }}"
                   class="sms-share-btn hidden items-center justify-center w-10 h-10 bg-green-600 hover:bg-green-700 text-white rounded-full transition-all duration-300 hover:scale-110 shadow-lg"
                   title="{{ app()->getLocale() === 'en' ? 'Share via SMS' : 'Compartir por SMS' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </a>

                <!-- Copy Link -->
                <button type="button"
                        onclick="copyToClipboard('{{ $url }}')"
                        class="flex items-center justify-center w-10 h-10 bg-gray-600 hover:bg-gray-700 text-white rounded-full transition-all duration-300 hover:scale-110 shadow-lg"
                        title="{{ __('app.copy_link') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </button>
            </div>
        </div>

    @elseif($layout === 'vertical')
        <!-- Vertical Layout -->
        <div class="flex flex-col space-y-2">
            <!-- Native Share Button (mobile priority) -->
            <button type="button" 
                    onclick="nativeShare('{{ $title }}', '{{ $description }}', '{{ $url }}')"
                    class="native-share-btn hidden items-center justify-center space-x-3 px-4 py-3 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white rounded-lg transition-all duration-300 hover:scale-105 shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                </svg>
                <span class="font-semibold">{{ app()->getLocale() === 'en' ? 'Share Restaurant' : 'Compartir Restaurante' }}</span>
            </button>
            
            <div class="share-fallback space-y-2">
                <a href="{{ $facebookUrl }}" target="_blank" class="flex items-center space-x-3 px-4 py-3 bg-[#1877F2] hover:bg-[#0C63D4] text-white rounded-lg transition-all duration-300 hover:scale-105 shadow-lg">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <span class="font-semibold">Facebook</span>
                </a>

                <a href="{{ $twitterUrl }}" target="_blank" class="flex items-center space-x-3 px-4 py-3 bg-black hover:bg-gray-800 text-white rounded-lg transition-all duration-300 hover:scale-105 shadow-lg">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                    <span class="font-semibold">X (Twitter)</span>
                </a>

                <a href="{{ $whatsappUrl }}" target="_blank" class="flex items-center space-x-3 px-4 py-3 bg-[#25D366] hover:bg-[#1EBE57] text-white rounded-lg transition-all duration-300 hover:scale-105 shadow-lg">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                    <span class="font-semibold">WhatsApp</span>
                </a>

                <button type="button" onclick="copyToClipboard('{{ $url }}')" class="flex items-center space-x-3 px-4 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-all duration-300 hover:scale-105 shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <span class="font-semibold">{{ __('app.copy_link') }}</span>
                </button>
            </div>
        </div>
    @endif
</div>

@once
    @push('scripts')
    <script>
        // Check for native share support
        document.addEventListener('DOMContentLoaded', function() {
            if (navigator.share) {
                // Show native share buttons on mobile
                document.querySelectorAll('.native-share-btn').forEach(btn => {
                    btn.classList.remove('hidden');
                    btn.classList.add('flex');
                });
            }
            
            // Show SMS button only on mobile
            if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                document.querySelectorAll('.sms-share-btn').forEach(btn => {
                    btn.classList.remove('hidden');
                    btn.classList.add('flex');
                });
            }
        });

        async function nativeShare(title, description, url) {
            if (navigator.share) {
                try {
                    await navigator.share({
                        title: title,
                        text: description,
                        url: url,
                    });
                } catch (err) {
                    if (err.name !== 'AbortError') {
                        console.error('Error sharing:', err);
                    }
                }
            }
        }

        function copyToClipboard(text) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => {
                    showCopyNotification();
                });
            } else {
                // Fallback for older browsers
                const textArea = document.createElement("textarea");
                textArea.value = text;
                textArea.style.position = "fixed";
                textArea.style.left = "-999999px";
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    showCopyNotification();
                } catch (err) {
                    console.error('Failed to copy:', err);
                }
                document.body.removeChild(textArea);
            }
        }

        function showCopyNotification() {
            // Create notification
            const notification = document.createElement('div');
            notification.className = 'fixed bottom-4 right-4 bg-gradient-to-r from-green-500 to-emerald-500 text-white px-6 py-3 rounded-lg shadow-xl z-50 animate-fade-in-up';
            notification.innerHTML = `
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-semibold">{{ app()->getLocale() === 'en' ? 'Link copied!' : 'Enlace copiado!' }}</span>
                </div>
            `;
            document.body.appendChild(notification);

            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateY(20px)';
                notification.style.transition = 'all 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
    </script>

    <style>
        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in-up {
            animation: fade-in-up 0.3s ease;
        }
    </style>
    @endpush
@endonce
