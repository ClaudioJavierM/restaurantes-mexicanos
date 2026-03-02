<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Recent Email Campaigns
        </x-slot>
        <x-slot name="description">
            Last 10 campaigns from Listmonk
        </x-slot>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Campaign</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-300">Status</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-300">Sent</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-300">Opens</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-300">Clicks</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-600 dark:text-gray-300">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($this->getCampaigns() as $campaign)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-3">
                                <span class="font-medium text-gray-900 dark:text-white" title="{{ $campaign['name'] }}">
                                    {{ \Illuminate\Support\Str::limit($campaign['name'], 50) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($campaign['status'] === 'finished')
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-800/20 dark:text-green-400">
                                        Finished
                                    </span>
                                @elseif($campaign['status'] === 'running')
                                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-800/20 dark:text-blue-400">
                                        Running
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-800/20 dark:text-gray-400">
                                        {{ ucfirst($campaign['status']) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center font-mono">
                                {{ $campaign['sent'] }}/{{ $campaign['to_send'] }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-medium">{{ $campaign['opens'] }}</span>
                                <span class="text-gray-500 text-xs">({{ $campaign['open_rate'] }}%)</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-medium">{{ $campaign['clicks'] }}</span>
                                <span class="text-gray-500 text-xs">({{ $campaign['click_rate'] }}%)</span>
                            </td>
                            <td class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">
                                {{ $campaign['date'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                No campaigns found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4 text-xs text-gray-500 flex items-center gap-2">
            <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4" />
            <a href="https://lists.mefimports.com" target="_blank" class="hover:underline">
                Open Listmonk Dashboard
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
