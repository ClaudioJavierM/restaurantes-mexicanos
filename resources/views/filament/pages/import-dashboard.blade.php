<x-filament-panels::page>
    <style>
        .import-dashboard { --card-bg: linear-gradient(135deg, #1e293b 0%, #334155 100%); --card-border: rgba(255,255,255,0.1); }
        .period-selector { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; }
        .period-btn { padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); background: transparent; color: rgba(255,255,255,0.7); cursor: pointer; transition: all 0.2s; font-size: 0.875rem; }
        .period-btn:hover { background: rgba(255,255,255,0.1); }
        .period-btn.active { background: #3b82f6; border-color: #3b82f6; color: white; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 0.75rem; margin-bottom: 1.5rem; }
        .stat-card { background: var(--card-bg); border-radius: 10px; padding: 1rem; border: 1px solid var(--card-border); }
        .stat-card.primary { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
        .stat-card.success { background: linear-gradient(135deg, #22c55e 0%, #15803d 100%); }
        .stat-card.warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .stat-card.purple { background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); }
        .stat-card.cyan { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
        .stat-card.rose { background: linear-gradient(135deg, #f43f5e 0%, #be123c 100%); }
        .stat-label { font-size: 0.65rem; color: rgba(255,255,255,0.7); margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-value { font-size: 1.4rem; font-weight: 700; color: white; line-height: 1.2; }
        .stat-detail { font-size: 0.6rem; color: rgba(255,255,255,0.5); margin-top: 0.25rem; }
        .section-title { font-size: 0.9rem; font-weight: 600; color: white; margin: 1.25rem 0 0.75rem; display: flex; align-items: center; gap: 0.5rem; }
        .section-title svg { width: 18px; height: 18px; opacity: 0.7; }
        .charts-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1rem; }
        .charts-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1rem; }
        @media (max-width: 1024px) { .charts-grid, .charts-grid-3 { grid-template-columns: 1fr; } }
        .chart-card { background: #1e293b; border-radius: 10px; padding: 1rem; border: 1px solid rgba(255,255,255,0.1); }
        .chart-title { font-size: 0.75rem; font-weight: 600; color: white; margin-bottom: 0.75rem; }
        .enrichment-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 0.75rem; margin-bottom: 1rem; }
        .enrichment-card { background: #1e293b; border-radius: 10px; padding: 0.875rem; border: 1px solid rgba(255,255,255,0.1); }
        .enrichment-item { display: flex; justify-content: space-between; align-items: center; padding: 0.4rem 0; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .enrichment-item:last-child { border-bottom: none; }
        .enrichment-name { color: rgba(255,255,255,0.7); font-size: 0.75rem; }
        .enrichment-value { display: flex; align-items: center; gap: 0.5rem; }
        .enrichment-count { color: white; font-weight: 600; font-size: 0.8rem; }
        .enrichment-percent { color: rgba(255,255,255,0.4); font-size: 0.65rem; }
        .progress-bar { height: 4px; background: rgba(255,255,255,0.1); border-radius: 2px; overflow: hidden; width: 50px; }
        .progress-fill { height: 100%; border-radius: 2px; }
        .bottom-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
        @media (max-width: 1280px) { .bottom-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 768px) { .bottom-grid { grid-template-columns: 1fr; } }
        .schedule-item { display: flex; align-items: center; justify-content: space-between; padding: 0.4rem 0.6rem; background: rgba(255,255,255,0.03); border-radius: 6px; margin-bottom: 0.35rem; font-size: 0.75rem; }
        .schedule-day { font-weight: 600; color: white; }
        .schedule-states { color: rgba(255,255,255,0.5); font-size: 0.65rem; }
        .schedule-time { background: rgba(59,130,246,0.2); color: #60a5fa; padding: 0.15rem 0.4rem; border-radius: 9999px; font-size: 0.6rem; }
        .recent-item { display: flex; align-items: center; justify-content: space-between; padding: 0.35rem 0; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .recent-item:last-child { border-bottom: none; }
        .recent-name { color: white; font-size: 0.7rem; font-weight: 500; }
        .recent-location { color: rgba(255,255,255,0.4); font-size: 0.6rem; }
        .recent-rating { background: rgba(34,197,94,0.2); color: #4ade80; padding: 0.1rem 0.35rem; border-radius: 4px; font-size: 0.6rem; }
        .missing-states { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); border-radius: 8px; padding: 0.75rem; margin-top: 0.5rem; }
        .missing-states-title { color: #f87171; font-size: 0.7rem; font-weight: 600; margin-bottom: 0.5rem; }
        .missing-states-list { color: rgba(255,255,255,0.6); font-size: 0.65rem; }
    </style>

    <div class="import-dashboard">
        <!-- Period Selector -->
        <div class="period-selector">
            <button wire:click="setPeriod('7days')" class="period-btn {{ $period === '7days' ? 'active' : '' }}">7 Days</button>
            <button wire:click="setPeriod('30days')" class="period-btn {{ $period === '30days' ? 'active' : '' }}">30 Days</button>
            <button wire:click="setPeriod('90days')" class="period-btn {{ $period === '90days' ? 'active' : '' }}">90 Days</button>
        </div>

        @php 
            $stats = $this->getStats(); 
            $enrichment = $this->getEnrichmentMetrics();
            $qualityScore = $this->getDataQualityScore();
            $total = $enrichment['total'];
            $apiStats = $this->getApiCallStats();
        @endphp

        <!-- Main Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-label">Total Restaurants</div>
                <div class="stat-value">{{ number_format($stats['total_restaurants']) }}</div>
                <div class="stat-detail">+{{ number_format($stats['new_restaurants']) }} this period</div>
            </div>
            <div class="stat-card success">
                <div class="stat-label">Yelp Coverage</div>
                <div class="stat-value">{{ $stats['yelp_coverage'] }}%</div>
                <div class="stat-detail">{{ number_format($stats['with_yelp']) }} linked</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-label">Google Coverage</div>
                <div class="stat-value">{{ $stats['google_coverage'] }}%</div>
                <div class="stat-detail">{{ number_format($stats['with_google']) }} linked</div>
            </div>
            <div class="stat-card cyan">
                <div class="stat-label">Data Quality</div>
                <div class="stat-value">{{ $qualityScore }}%</div>
                <div class="stat-detail">Overall score</div>
            </div>
            <div class="stat-card purple">
                <div class="stat-label">API Calls</div>
                <div class="stat-value">{{ number_format($apiStats['total_calls']) }}</div>
                <div class="stat-detail">${{ number_format($apiStats['total_cost'], 2) }} cost</div>
            </div>
            <div class="stat-card rose">
                <div class="stat-label">States</div>
                <div class="stat-value">{{ $stats['states_covered'] }}/50</div>
                <div class="stat-detail">covered</div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="section-title">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Import & API Trends
        </div>

        <div class="charts-grid">
            <div class="chart-card">
                <div class="chart-title">Imports by Day</div>
                <canvas id="importsByDayChart" height="140"></canvas>
            </div>
            <div class="chart-card">
                <div class="chart-title">API Calls by Day</div>
                <canvas id="apiCallsChart" height="140"></canvas>
            </div>
        </div>

        <div class="charts-grid-3">
            <div class="chart-card">
                <div class="chart-title">Import Sources</div>
                <canvas id="sourceChart" height="140"></canvas>
            </div>
            <div class="chart-card">
                <div class="chart-title">Top States</div>
                <canvas id="statesChart" height="140"></canvas>
            </div>
            <div class="chart-card">
                <div class="chart-title">API Calls by Service</div>
                <canvas id="apiServiceChart" height="140"></canvas>
            </div>
        </div>

        <!-- Enrichment Section -->
        <div class="section-title">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Data Enrichment Coverage
        </div>

        <div class="enrichment-grid">
            <!-- Platform Coverage -->
            <div class="enrichment-card">
                <div class="chart-title">Platform Links</div>
                @foreach($enrichment['platforms'] as $platform)
                    @php $pct = $total > 0 ? round(($platform['count'] / $total) * 100, 1) : 0; @endphp
                    <div class="enrichment-item">
                        <span class="enrichment-name">{{ $platform['name'] }}</span>
                        <div class="enrichment-value">
                            <span class="enrichment-count">{{ number_format($platform['count']) }}</span>
                            <span class="enrichment-percent">{{ $pct }}%</span>
                            <div class="progress-bar"><div class="progress-fill" style="width: {{ min($pct, 100) }}%; background: {{ $platform['color'] }};"></div></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Contact Info -->
            <div class="enrichment-card">
                <div class="chart-title">Contact Information</div>
                @foreach($enrichment['contact'] as $item)
                    @php $pct = $total > 0 ? round(($item['count'] / $total) * 100, 1) : 0; @endphp
                    <div class="enrichment-item">
                        <span class="enrichment-name">{{ $item['name'] }}</span>
                        <div class="enrichment-value">
                            <span class="enrichment-count">{{ number_format($item['count']) }}</span>
                            <span class="enrichment-percent">{{ $pct }}%</span>
                            <div class="progress-bar"><div class="progress-fill" style="width: {{ min($pct, 100) }}%; background: {{ $pct > 70 ? '#22c55e' : ($pct > 40 ? '#f59e0b' : '#ef4444') }};"></div></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Content Data -->
            <div class="enrichment-card">
                <div class="chart-title">Content & Media</div>
                @foreach($enrichment['content'] as $item)
                    @php $pct = $total > 0 ? round(($item['count'] / $total) * 100, 1) : 0; @endphp
                    <div class="enrichment-item">
                        <span class="enrichment-name">{{ $item['name'] }}</span>
                        <div class="enrichment-value">
                            <span class="enrichment-count">{{ number_format($item['count']) }}</span>
                            <span class="enrichment-percent">{{ $pct }}%</span>
                            <div class="progress-bar"><div class="progress-fill" style="width: {{ min($pct, 100) }}%; background: {{ $pct > 70 ? '#22c55e' : ($pct > 40 ? '#f59e0b' : '#ef4444') }};"></div></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Yelp Specific Data -->
            <div class="enrichment-card">
                <div class="chart-title">Yelp Enrichment</div>
                @foreach($enrichment['yelp_data'] as $item)
                    @php $pct = $total > 0 ? round(($item['count'] / $total) * 100, 1) : 0; @endphp
                    <div class="enrichment-item">
                        <span class="enrichment-name">{{ $item['name'] }}</span>
                        <div class="enrichment-value">
                            <span class="enrichment-count">{{ number_format($item['count']) }}</span>
                            <span class="enrichment-percent">{{ $pct }}%</span>
                            <div class="progress-bar"><div class="progress-fill" style="width: {{ min($pct, 100) }}%; background: #ef4444;"></div></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Bottom Grid -->
        <div class="bottom-grid">
            <!-- Weekly Schedule -->
            <div class="chart-card">
                <div class="chart-title">Weekly Import Schedule (50/50 states)</div>
                @foreach($this->getScheduledTasks() as $task)
                    <div class="schedule-item">
                        <div>
                            <div class="schedule-day">{{ $task['day'] }}</div>
                            <div class="schedule-states">{{ $task['states'] }}</div>
                        </div>
                        <div class="schedule-time">{{ $task['time'] }}</div>
                    </div>
                @endforeach
                <div class="missing-states" style="display:none;">
                    <div class="missing-states-title">19 States Not Scheduled:</div>
                    <div class="missing-states-list">AK, CT, DE, HI, ID, MA, MD, ME, MT, ND, NH, NJ, RI, SC, SD, VA, VT, WV, WY</div>
                </div>
            </div>

            <!-- Daily Tasks -->
            <div class="chart-card">
                <div class="chart-title">Daily Automated Tasks</div>
                @foreach($this->getDailyTasks() as $task)
                    <div class="schedule-item" style="border-left: 3px solid #8b5cf6;">
                        <div>
                            <div class="schedule-day">{{ $task['task'] }}</div>
                            <div class="schedule-states">{{ $task['description'] }}</div>
                        </div>
                        <div class="schedule-time" style="background: rgba(139,92,246,0.2); color: #a78bfa;">{{ $task['time'] }}</div>
                    </div>
                @endforeach
            </div>

            <!-- Recent Imports -->
            <div class="chart-card">
                <div class="chart-title">Recent Imports</div>
                @forelse($this->getRecentImports() as $import)
                    <div class="recent-item">
                        <div>
                            <div class="recent-name">{{ Str::limit($import['name'], 20) }}</div>
                            <div class="recent-location">{{ $import['city'] }}, {{ $import['state'] }}</div>
                        </div>
                        <div class="recent-rating">{{ $import['rating'] }}</div>
                    </div>
                @empty
                    <p style="color: rgba(255,255,255,0.4); text-align: center; padding: 1rem; font-size: 0.75rem;">No recent imports</p>
                @endforelse
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartOpts = {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: 'rgba(255,255,255,0.5)', font: { size: 9 } } },
                    x: { grid: { display: false }, ticks: { color: 'rgba(255,255,255,0.5)', font: { size: 9 } } }
                }
            };
            const pieOpts = {
                responsive: true,
                plugins: { legend: { position: 'bottom', labels: { color: 'rgba(255,255,255,0.6)', font: { size: 9 }, boxWidth: 10, padding: 6 } } }
            };

            // Imports by Day
            const importsByDay = @json($this->getImportsByDay());
            new Chart(document.getElementById('importsByDayChart'), {
                type: 'bar',
                data: { labels: importsByDay.map(d => d.date), datasets: [{ data: importsByDay.map(d => d.count), backgroundColor: 'rgba(59,130,246,0.8)', borderRadius: 3 }] },
                options: chartOpts
            });

            // API Calls by Day
            const apiCallsByDay = @json($this->getApiCallsByDay());
            new Chart(document.getElementById('apiCallsChart'), {
                type: 'line',
                data: { labels: apiCallsByDay.map(d => d.date), datasets: [{ data: apiCallsByDay.map(d => d.calls), borderColor: '#22c55e', backgroundColor: 'rgba(34,197,94,0.1)', fill: true, tension: 0.3, borderWidth: 2 }] },
                options: chartOpts
            });

            // Import Sources Pie
            const importsBySource = @json($this->getImportsBySource());
            if (importsBySource.length > 0) {
                new Chart(document.getElementById('sourceChart'), {
                    type: 'doughnut',
                    data: { labels: importsBySource.map(d => d.source), datasets: [{ data: importsBySource.map(d => d.count), backgroundColor: ['#3b82f6', '#22c55e', '#f59e0b', '#8b5cf6', '#ec4899'] }] },
                    options: pieOpts
                });
            }

            // Top States
            const topStates = @json($this->getTopStates());
            if (topStates.length > 0) {
                new Chart(document.getElementById('statesChart'), {
                    type: 'bar',
                    data: { labels: topStates.slice(0, 6).map(d => d.code), datasets: [{ data: topStates.slice(0, 6).map(d => d.count), backgroundColor: 'rgba(139,92,246,0.8)', borderRadius: 3 }] },
                    options: { responsive: true, maintainAspectRatio: true, indexAxis: "y", plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, grid: { color: "rgba(255,255,255,0.05)" }, ticks: { color: "rgba(255,255,255,0.5)", font: { size: 9 } } }, y: { grid: { display: false }, ticks: { color: "rgba(255,255,255,0.5)", font: { size: 9 } } } } }
                });
            }

            // API Calls by Service
            const apiByService = @json($this->getApiCallsByService());
            if (apiByService.length > 0) {
                new Chart(document.getElementById('apiServiceChart'), {
                    type: 'doughnut',
                    data: { labels: apiByService.map(d => d.service), datasets: [{ data: apiByService.map(d => d.calls), backgroundColor: ['#ef4444', '#22c55e', '#06b6d4', '#f59e0b', '#8b5cf6'] }] },
                    options: pieOpts
                });
            } else {
                document.getElementById('apiServiceChart').parentElement.innerHTML = '<div class="chart-title">API Calls by Service</div><p style="color: rgba(255,255,255,0.4); text-align: center; padding: 2rem; font-size: 0.75rem;">No API calls tracked yet.<br>Tracking starts from now.</p>';
            }
        });
    </script>
</x-filament-panels::page>
