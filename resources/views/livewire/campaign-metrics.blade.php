<div>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-bold">Métricas de Campaña</h2>
            <p class="text-gray-600 mt-1">{{ $campaign->name }}</p>
        </div>
        <a href="{{ route('campaigns') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
            Volver a Campañas
        </a>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <!-- Total Enviados -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600 mb-1">Total Enviados</div>
            <div class="text-3xl font-bold text-blue-600">{{ number_format($campaign->sent_count) }}</div>
            <div class="text-xs text-gray-500 mt-1">de {{ number_format($campaign->total_recipients) }}</div>
        </div>

        <!-- Tasa de Entrega -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600 mb-1">Tasa de Entrega</div>
            <div class="text-3xl font-bold text-green-600">
                {{ $campaign->sent_count > 0 ? round(($campaign->delivered_count / $campaign->sent_count) * 100, 1) : 0 }}%
            </div>
            <div class="text-xs text-gray-500 mt-1">{{ number_format($campaign->delivered_count) }} entregados</div>
        </div>

        <!-- Tasa de Lectura -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600 mb-1">Tasa de Lectura</div>
            <div class="text-3xl font-bold text-purple-600">
                {{ $campaign->delivered_count > 0 ? round(($campaign->read_count / $campaign->delivered_count) * 100, 1) : 0 }}%
            </div>
            <div class="text-xs text-gray-500 mt-1">{{ number_format($campaign->read_count) }} leídos</div>
        </div>

        <!-- Tiempo Promedio de Lectura -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600 mb-1">Tiempo Promedio de Lectura</div>
            <div class="text-3xl font-bold text-orange-600">
                {{ $avgReadTime ?? 0 }}
            </div>
            <div class="text-xs text-gray-500 mt-1">minutos</div>
        </div>

        <!-- Mensajes Fallidos -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600 mb-1">Fallidos</div>
            <div class="text-3xl font-bold text-red-600">{{ number_format($campaign->failed_count) }}</div>
            <div class="text-xs text-gray-500 mt-1">
                {{ $campaign->total_recipients > 0 ? round(($campaign->failed_count / $campaign->total_recipients) * 100, 1) : 0 }}%
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Status Distribution Pie Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-semibold mb-4">Distribución de Estados</h3>
            <div id="statusChart" style="height: 300px;"></div>
        </div>

        <!-- Hourly Engagement Bar Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-semibold mb-4">Horarios de Mayor Engagement</h3>
            <div id="hourlyChart" style="height: 300px;"></div>
        </div>
    </div>

    <!-- Read/Unread Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Who Read -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h3 class="text-xl font-semibold text-green-600">Usuarios que Leyeron ({{ count($readDetails) }})</h3>
            </div>
            <div class="p-6 max-h-96 overflow-y-auto">
                @if(count($readDetails) > 0)
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left py-2 px-3">Nombre</th>
                                <th class="text-left py-2 px-3">Teléfono</th>
                                <th class="text-left py-2 px-3">Leído</th>
                                <th class="text-left py-2 px-3">Tiempo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($readDetails as $detail)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 px-3">{{ $detail['name'] }}</td>
                                    <td class="py-2 px-3">{{ $detail['phone'] }}</td>
                                    <td class="py-2 px-3">{{ $detail['read_at']->format('d/m/Y H:i') }}</td>
                                    <td class="py-2 px-3">{{ $detail['time_to_read'] ?? '-' }} min</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-center text-gray-500 py-8">Aún no hay lecturas registradas</p>
                @endif
            </div>
        </div>

        <!-- Who Didn't Read -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h3 class="text-xl font-semibold text-red-600">Usuarios que NO Leyeron ({{ count($unreadContacts) }})</h3>
            </div>
            <div class="p-6 max-h-96 overflow-y-auto">
                @if(count($unreadContacts) > 0)
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left py-2 px-3">Nombre</th>
                                <th class="text-left py-2 px-3">Teléfono</th>
                                <th class="text-left py-2 px-3">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($unreadContacts as $contact)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 px-3">{{ $contact['name'] }}</td>
                                    <td class="py-2 px-3">{{ $contact['phone'] }}</td>
                                    <td class="py-2 px-3">
                                        <span class="px-2 py-1 rounded text-xs
                                            {{ $contact['status'] === 'sent' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $contact['status'] === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $contact['status'] === 'pending' ? 'bg-gray-100 text-gray-800' : '' }}">
                                            {{ ucfirst($contact['status']) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-center text-gray-500 py-8">¡Todos leyeron el mensaje!</p>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Status Distribution Pie Chart
            const statusChart = echarts.init(document.getElementById('statusChart'));
            const statusOption = {
                tooltip: {
                    trigger: 'item',
                    formatter: '{b}: {c} ({d}%)'
                },
                legend: {
                    orient: 'vertical',
                    left: 'left'
                },
                series: [{
                    type: 'pie',
                    radius: '70%',
                    data: @js($statusDistribution),
                    emphasis: {
                        itemStyle: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }]
            };
            statusChart.setOption(statusOption);

            // Hourly Engagement Bar Chart
            const hourlyChart = echarts.init(document.getElementById('hourlyChart'));
            const hourlyOption = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    }
                },
                xAxis: {
                    type: 'category',
                    data: @js($hourlyData['hours']).map(h => h + ':00'),
                    axisLabel: {
                        rotate: 45
                    }
                },
                yAxis: {
                    type: 'value',
                    name: 'Lecturas'
                },
                series: [{
                    data: @js($hourlyData['reads']),
                    type: 'bar',
                    itemStyle: {
                        color: '#3b82f6'
                    },
                    emphasis: {
                        itemStyle: {
                            color: '#1d4ed8'
                        }
                    }
                }]
            };
            hourlyChart.setOption(hourlyOption);

            // Resize charts on window resize
            window.addEventListener('resize', function() {
                statusChart.resize();
                hourlyChart.resize();
            });
        });
    </script>
    @endpush
</div>
