<x-filament-panels::page>

    {{-- ─── Alertas automáticas ─────────────────────────────────────────── --}}
    @if($bounceRate > 3)
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <p class="text-red-800 font-semibold">
                ⚠️ Alerta: Bounce rate en {{ $bounceRate }}% — revisar lista de emails
            </p>
        </div>
    @elseif($bounceRate > 1)
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
            <p class="text-amber-800">
                Aviso: Bounce rate en {{ $bounceRate }}% — monitorear
            </p>
        </div>
    @endif

    {{-- ─── 5 Stat Cards ────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">

        {{-- Total Enviados --}}
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
            <p class="text-xs font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wide mb-1">Total Enviados</p>
            <p class="text-3xl font-bold text-blue-900 dark:text-blue-100">{{ number_format($totalSent) }}</p>
        </div>

        {{-- Entregados --}}
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4">
            <p class="text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wide mb-1">Entregados</p>
            <p class="text-3xl font-bold text-green-900 dark:text-green-100">{{ number_format($totalDelivered) }}</p>
            @if($totalSent > 0)
                <p class="text-sm text-green-600 dark:text-green-400 mt-1">
                    {{ round(($totalDelivered / $totalSent) * 100, 1) }}%
                </p>
            @endif
        </div>

        {{-- Abiertos --}}
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
            <p class="text-xs font-medium text-amber-600 dark:text-amber-400 uppercase tracking-wide mb-1">Abiertos</p>
            <p class="text-3xl font-bold text-amber-900 dark:text-amber-100">{{ number_format($totalOpened) }}</p>
            <p class="text-sm text-amber-600 dark:text-amber-400 mt-1">{{ $openRate }}% open rate</p>
        </div>

        {{-- Clicks --}}
        <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-xl p-4">
            <p class="text-xs font-medium text-purple-600 dark:text-purple-400 uppercase tracking-wide mb-1">Clicks</p>
            <p class="text-3xl font-bold text-purple-900 dark:text-purple-100">{{ number_format($totalClicked) }}</p>
            <p class="text-sm text-purple-600 dark:text-purple-400 mt-1">{{ $clickRate }}% click rate</p>
        </div>

        {{-- Rebotados --}}
        @php
            $bounceColorBg    = $bounceRate > 3 ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800'
                              : ($bounceRate > 1 ? 'bg-orange-50 dark:bg-orange-900/20 border-orange-200 dark:border-orange-800'
                              : 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800');
            $bounceColorText  = $bounceRate > 3 ? 'text-red-600 dark:text-red-400'
                              : ($bounceRate > 1 ? 'text-orange-600 dark:text-orange-400'
                              : 'text-green-600 dark:text-green-400');
            $bounceColorValue = $bounceRate > 3 ? 'text-red-900 dark:text-red-100'
                              : ($bounceRate > 1 ? 'text-orange-900 dark:text-orange-100'
                              : 'text-green-900 dark:text-green-100');
        @endphp
        <div class="border rounded-xl p-4 {{ $bounceColorBg }}">
            <p class="text-xs font-medium uppercase tracking-wide mb-1 {{ $bounceColorText }}">Rebotados</p>
            <p class="text-3xl font-bold {{ $bounceColorValue }}">{{ number_format($totalBounced) }}</p>
            <p class="text-sm mt-1 {{ $bounceColorText }}">{{ $bounceRate }}% bounce rate</p>
        </div>

    </div>

    {{-- ─── Tabla por categoría ─────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl mb-8 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Por Campaña / Categoría</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Categoría</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Enviados</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Entregados</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Abiertos</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tasa Apertura</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Clicks</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rebotados</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($byCategory as $row)
                        @php
                            $openPct = $row->sent > 0 ? round(($row->opened / $row->sent) * 100, 1) : 0;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-6 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                {{ \App\Filament\Pages\CampaignMonitor::getCategoryLabel($row->category ?? 'other') }}
                            </td>
                            <td class="px-6 py-3 text-sm text-right text-gray-700 dark:text-gray-300">{{ number_format($row->sent) }}</td>
                            <td class="px-6 py-3 text-sm text-right text-green-700 dark:text-green-400">{{ number_format($row->delivered) }}</td>
                            <td class="px-6 py-3 text-sm text-right text-amber-700 dark:text-amber-400">{{ number_format($row->opened) }}</td>
                            <td class="px-6 py-3 text-sm text-right">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    {{ $openPct >= 25 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                                     : ($openPct >= 10 ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400'
                                     : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400') }}">
                                    {{ $openPct }}%
                                </span>
                            </td>
                            <td class="px-6 py-3 text-sm text-right text-purple-700 dark:text-purple-400">{{ number_format($row->clicked) }}</td>
                            <td class="px-6 py-3 text-sm text-right text-red-700 dark:text-red-400">{{ number_format($row->bounced) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Sin datos de campañas aún.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ─── Feed de eventos recientes ───────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl mb-8 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Eventos Recientes (últimos 20)</h2>
            <span class="text-xs text-gray-500 dark:text-gray-400">newsletter_events</span>
        </div>

        @php
            $eventBadge = [
                'sent'       => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                'delivered'  => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                'opened'     => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
                'clicked'    => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                'bounced'    => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                'complained' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
            ];
        @endphp

        <ul class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($recentEvents as $event)
                @php
                    $type       = $event->event_type ?? $event->type ?? 'sent';
                    $badgeClass = $eventBadge[$type] ?? 'bg-gray-100 text-gray-700';
                    $email      = Str::limit($event->email ?? $event->subscriber_email ?? '—', 30);
                    $source     = $event->source ?? (str_contains($event->message_id ?? '', 'listmonk') ? 'Listmonk' : 'Resend');
                    $occurred   = $event->occurred_at ?? $event->created_at;
                @endphp
                <li class="px-6 py-3 flex items-center gap-4">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium w-20 justify-center {{ $badgeClass }}">
                        {{ ucfirst($type) }}
                    </span>
                    <span class="text-sm text-gray-700 dark:text-gray-300 flex-1 truncate font-mono">{{ $email }}</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $source }}</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">
                        {{ $occurred ? $occurred->diffForHumans() : '—' }}
                    </span>
                </li>
            @empty
                <li class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                    Sin eventos recientes.
                </li>
            @endforelse
        </ul>
    </div>

    {{-- ─── Acciones rápidas ────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Acciones</h2>
        <div class="flex flex-wrap gap-3">

            <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 flex-1 min-w-60">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Dry-run Email 1</p>
                <code class="text-xs text-gray-700 dark:text-gray-300 break-all">
                    php artisan famer:send-emails 1 --dry-run
                </code>
            </div>

            <div class="flex items-center">
                <a href="/admin/email-logs"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <x-heroicon-o-inbox-stack class="w-4 h-4" />
                    Ver Email Logs completos
                </a>
            </div>

        </div>
    </div>

</x-filament-panels::page>
