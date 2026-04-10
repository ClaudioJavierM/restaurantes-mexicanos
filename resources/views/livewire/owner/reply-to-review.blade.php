<div style="font-family: 'Poppins', sans-serif; background-color: #0B0B0B; min-height: 100vh; padding: 1.5rem;">

    {{-- Header --}}
    <div style="background: linear-gradient(135deg, #1A1A1A 0%, #2A2A2A 100%); border: 1px solid #D4AF37; border-radius: 12px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem;">
        <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #D4AF37, #B8973A); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="24" height="24" fill="none" stroke="#0B0B0B" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
        </div>
        <div>
            <h1 style="color: #F5F5F5; font-size: 1.375rem; font-weight: 700; margin: 0 0 0.25rem 0;">Responder Reseñas</h1>
            <p style="color: #9CA3AF; font-size: 0.875rem; margin: 0;">Responde públicamente a tus clientes y construye confianza</p>
        </div>
    </div>

    {{-- Flash success --}}
    @if (session()->has('reply_saved'))
        <div style="background-color: #1A2E1A; border: 1px solid #2ECC71; border-radius: 10px; padding: 1rem 1.5rem; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.75rem;"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition>
            <svg width="20" height="20" fill="none" stroke="#2ECC71" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            <span style="color: #2ECC71; font-weight: 600;">Respuesta guardada exitosamente.</span>
        </div>
    @endif

    {{-- Reviews list --}}
    <div style="display: flex; flex-direction: column; gap: 1rem;">

        @forelse ($reviews as $review)
            <div style="background-color: #1A1A1A; border: 1px solid #2A2A2A; border-radius: 12px; padding: 1.5rem; transition: border-color 0.2s;"
                 @if($replyingToId === $review->id) style="background-color: #1A1A1A; border: 1px solid #D4AF37; border-radius: 12px; padding: 1.5rem;" @endif>

                {{-- Review header --}}
                <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; margin-bottom: 0.875rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        {{-- Avatar --}}
                        <div style="width: 42px; height: 42px; background: linear-gradient(135deg, #2A2A2A, #3A3A3A); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid #3A3A3A;">
                            <span style="color: #D4AF37; font-size: 1rem; font-weight: 700;">
                                {{ strtoupper(substr($review->user ? $review->user->name : ($review->name ?? 'A'), 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <p style="color: #F5F5F5; font-weight: 600; font-size: 0.9375rem; margin: 0 0 0.2rem 0;">
                                {{ $review->user ? $review->user->name : ($review->name ?? 'Cliente Anónimo') }}
                            </p>
                            <p style="color: #9CA3AF; font-size: 0.75rem; margin: 0;">
                                {{ $review->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    {{-- Stars --}}
                    <div style="display: flex; align-items: center; gap: 0.25rem; flex-shrink: 0;">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg width="16" height="16" viewBox="0 0 20 20" fill="{{ $i <= $review->rating ? '#D4AF37' : '#3A3A3A' }}">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                        <span style="color: #9CA3AF; font-size: 0.75rem; margin-left: 0.25rem;">{{ $review->rating }}/5</span>
                    </div>
                </div>

                {{-- Review text --}}
                @if ($review->comment)
                    <p style="color: #D1D5DB; font-size: 0.9375rem; line-height: 1.6; margin: 0 0 1rem 0;">
                        {{ $review->comment }}
                    </p>
                @else
                    <p style="color: #6B7280; font-size: 0.875rem; font-style: italic; margin: 0 0 1rem 0;">
                        — Sin comentario escrito
                    </p>
                @endif

                {{-- Existing owner reply --}}
                @if ($review->owner_reply)
                    <div style="background-color: #0F1A0F; border-left: 3px solid #D4AF37; border-radius: 0 8px 8px 0; padding: 1rem 1.25rem; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                            <svg width="16" height="16" fill="none" stroke="#D4AF37" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            <span style="color: #D4AF37; font-weight: 600; font-size: 0.8125rem; text-transform: uppercase; letter-spacing: 0.05em;">Tu respuesta</span>
                            @if ($review->owner_replied_at)
                                <span style="color: #6B7280; font-size: 0.75rem;">· {{ $review->owner_replied_at->diffForHumans() }}</span>
                            @endif
                        </div>
                        <p style="color: #D1D5DB; font-size: 0.9rem; line-height: 1.5; margin: 0;">{{ $review->owner_reply }}</p>
                    </div>
                @endif

                {{-- Reply inline form --}}
                @if ($replyingToId === $review->id)
                    <div style="background-color: #111111; border: 1px solid #2A2A2A; border-radius: 10px; padding: 1.25rem;" x-data>

                        <label style="display: block; color: #9CA3AF; font-size: 0.8125rem; font-weight: 500; margin-bottom: 0.5rem;">
                            Tu respuesta pública
                        </label>

                        <textarea
                            wire:model.live="replyText"
                            placeholder="Gracias por tu visita, fue un placer atenderte..."
                            maxlength="500"
                            rows="4"
                            style="width: 100%; background-color: #1A1A1A; border: 1px solid #3A3A3A; border-radius: 8px; padding: 0.75rem 1rem; color: #F5F5F5; font-size: 0.9375rem; line-height: 1.5; resize: vertical; outline: none; box-sizing: border-box; font-family: inherit;"
                            onfocus="this.style.borderColor='#D4AF37'"
                            onblur="this.style.borderColor='#3A3A3A'"
                        ></textarea>

                        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.625rem; flex-wrap: wrap; gap: 0.75rem;">
                            <span style="color: {{ strlen($replyText) > 450 ? '#EF4444' : '#6B7280' }}; font-size: 0.75rem;">
                                {{ 500 - strlen($replyText) }} caracteres restantes
                            </span>

                            <div style="display: flex; gap: 0.75rem;">
                                <button
                                    wire:click="cancelReply"
                                    style="background-color: transparent; border: 1px solid #3A3A3A; color: #9CA3AF; font-size: 0.875rem; font-weight: 500; padding: 0.5rem 1.25rem; border-radius: 8px; cursor: pointer;"
                                    onmouseover="this.style.borderColor='#6B7280'; this.style.color='#F5F5F5'"
                                    onmouseout="this.style.borderColor='#3A3A3A'; this.style.color='#9CA3AF'"
                                >
                                    Cancelar
                                </button>
                                <button
                                    wire:click="submitReply"
                                    wire:loading.attr="disabled"
                                    style="background: linear-gradient(135deg, #D4AF37, #B8973A); border: none; color: #0B0B0B; font-size: 0.875rem; font-weight: 700; padding: 0.5rem 1.5rem; border-radius: 8px; cursor: pointer;"
                                    onmouseover="this.style.opacity='0.9'"
                                    onmouseout="this.style.opacity='1'"
                                >
                                    <span wire:loading.remove wire:target="submitReply">Publicar respuesta</span>
                                    <span wire:loading wire:target="submitReply">Guardando...</span>
                                </button>
                            </div>
                        </div>

                        {{-- Validation errors --}}
                        @error('replyText')
                            <p style="color: #EF4444; font-size: 0.8125rem; margin-top: 0.5rem;">{{ $message }}</p>
                        @enderror
                    </div>

                @else
                    {{-- Show reply / edit button --}}
                    <div style="display: flex; justify-content: flex-end;">
                        <button
                            wire:click="startReply({{ $review->id }})"
                            style="background-color: {{ $review->owner_reply ? 'transparent' : '#1A1A1A' }}; border: 1px solid {{ $review->owner_reply ? '#3A3A3A' : '#D4AF37' }}; color: {{ $review->owner_reply ? '#9CA3AF' : '#D4AF37' }}; font-size: 0.8125rem; font-weight: 600; padding: 0.5rem 1.25rem; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;"
                            onmouseover="this.style.borderColor='#D4AF37'; this.style.color='#D4AF37'"
                            onmouseout="this.style.borderColor='{{ $review->owner_reply ? '#3A3A3A' : '#D4AF37' }}'; this.style.color='{{ $review->owner_reply ? '#9CA3AF' : '#D4AF37' }}'"
                        >
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            {{ $review->owner_reply ? 'Editar respuesta' : 'Responder' }}
                        </button>
                    </div>
                @endif
            </div>

        @empty
            <div style="background-color: #1A1A1A; border: 1px solid #2A2A2A; border-radius: 12px; padding: 3rem; text-align: center;">
                <div style="width: 56px; height: 56px; background-color: #2A2A2A; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto;">
                    <svg width="28" height="28" fill="none" stroke="#6B7280" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <p style="color: #9CA3AF; font-size: 1rem; margin: 0;">Aún no hay reseñas aprobadas para este restaurante.</p>
            </div>
        @endforelse

    </div>

    {{-- Pagination --}}
    @if ($reviews->hasPages())
        <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
            {{ $reviews->links() }}
        </div>
    @endif

</div>
