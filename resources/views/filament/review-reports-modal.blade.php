<div class="space-y-4 p-4">
    {{-- Review Summary --}}
    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
        <div class="flex items-center justify-between mb-2">
            <span class="font-semibold text-gray-700">Review by {{ $review->reviewer_name }}</span>
            <span class="text-sm text-gray-500">{{ $review->created_at->format('M d, Y') }}</span>
        </div>
        <div class="flex gap-1 mb-2">
            @for($i = 1; $i <= 5; $i++)
                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            @endfor
        </div>
        <p class="text-sm text-gray-700">{{ $review->comment }}</p>
        @if($review->trust_flags)
            <div class="mt-2 flex flex-wrap gap-1">
                @foreach($review->trust_flags as $flag)
                    <span class="px-2 py-0.5 bg-amber-100 text-amber-800 text-xs rounded-full">{{ $flag }}</span>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Reports List --}}
    <h3 class="font-semibold text-gray-900">{{ $reports->count() }} Report(s)</h3>

    @forelse($reports as $report)
        <div class="border rounded-lg p-4 {{ $report->status === 'pending' ? 'border-red-200 bg-red-50' : 'border-gray-200 bg-white' }}">
            <div class="flex items-start justify-between">
                <div>
                    <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold
                        {{ $report->status === 'pending' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-600' }}">
                        {{ ucfirst($report->status) }}
                    </span>
                    <span class="ml-2 text-sm font-semibold text-gray-700">
                        Reason: {{ $report->getReasonLabel() }}
                    </span>
                </div>
                <span class="text-xs text-gray-500">{{ $report->created_at->diffForHumans() }}</span>
            </div>
            @if($report->description)
                <p class="mt-2 text-sm text-gray-600">{{ $report->description }}</p>
            @endif
            <p class="mt-1 text-xs text-gray-500">
                Reported by: {{ $report->user?->name ?? 'Anonymous' }}
            </p>
        </div>
    @empty
        <p class="text-gray-500 text-sm">No reports found.</p>
    @endforelse
</div>
