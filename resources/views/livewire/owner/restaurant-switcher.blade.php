@if(auth()->user()->allAccessibleRestaurants()->count() > 1)
<div class="flex items-center gap-2 px-3 py-2 bg-gray-800 rounded-lg border border-gray-700">
    <span class="text-xs text-gray-400 whitespace-nowrap">Restaurante:</span>
    <select wire:change="switchRestaurant($event.target.value)"
            class="bg-gray-800 text-white text-sm border-0 focus:ring-1 focus:ring-yellow-500 rounded cursor-pointer pr-6 py-0">
        @foreach($restaurants as $r)
        <option value="{{ $r->id }}" @selected($r->id == $selectedId)>
            {{ $r->name }}{{ $r->city ? ' — '.$r->city : '' }}
        </option>
        @endforeach
    </select>
</div>
@endif
