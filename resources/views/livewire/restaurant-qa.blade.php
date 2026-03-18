<div>
    {{-- Preguntas existentes --}}
    @if($questions->count() > 0)
    <div class="space-y-4 mb-6">
        @foreach($questions as $qa)
        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-sm">P</div>
                <div class="flex-1">
                    <p class="font-medium text-gray-800 text-sm">{{ $qa->question }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $qa->display_name }} · {{ $qa->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @if($qa->answer)
            <div class="flex items-start gap-3 mt-3 pl-2 border-l-4 border-red-400">
                <div class="flex-shrink-0 w-8 h-8 bg-red-100 rounded-full flex items-center justify-center text-red-600 font-bold text-sm">R</div>
                <div class="flex-1">
                    <p class="text-sm text-gray-700">{{ $qa->answer }}</p>
                    <p class="text-xs text-gray-500 mt-1">Respuesta del restaurante · {{ $qa->answered_at->diffForHumans() }}</p>
                </div>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    {{-- Formulario --}}
    @if($submitted)
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
        <p class="text-green-700 font-medium">✅ ¡Pregunta enviada!</p>
        <p class="text-green-600 text-sm mt-1">El restaurante la revisará y responderá pronto.</p>
        <button wire:click="$set('submitted', false)" class="mt-3 text-sm text-green-700 underline">Hacer otra pregunta</button>
    </div>
    @else
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <h4 class="font-semibold text-gray-800 mb-3 text-sm">¿Tienes una pregunta? 💬</h4>

        @if($error)
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded p-2 mb-3">{{ $error }}</div>
        @endif

        <form wire:submit.prevent="submitQuestion" class="space-y-3">
            <div>
                <textarea wire:model="question"
                    class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none @error('question') border-red-400 @enderror"
                    rows="3"
                    placeholder="¿Tienen estacionamiento? ¿Aceptan tarjetas? ¿Cuál es su platillo más popular?..."
                    maxlength="500"></textarea>
                @error('question') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            @guest
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <input wire:model="author_name" type="text"
                        class="w-full border border-gray-300 rounded-lg p-2 text-sm @error('author_name') border-red-400 @enderror"
                        placeholder="Tu nombre">
                    @error('author_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <input wire:model="author_email" type="email"
                        class="w-full border border-gray-300 rounded-lg p-2 text-sm @error('author_email') border-red-400 @enderror"
                        placeholder="Tu email">
                    @error('author_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            @endguest

            <button type="submit"
                class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg text-sm transition">
                <span wire:loading.remove wire:target="submitQuestion">Enviar Pregunta</span>
                <span wire:loading wire:target="submitQuestion">Enviando...</span>
            </button>
        </form>
    </div>
    @endif
</div>
