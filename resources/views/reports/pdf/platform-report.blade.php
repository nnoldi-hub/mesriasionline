<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raport Platformă - {{ $period['start'] }} - {{ $period['end'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #1f2937;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #4f46e5;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #4f46e5;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header p {
            color: #6b7280;
            font-size: 12px;
        }
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .section-title {
            background: #4f46e5;
            color: white;
            padding: 8px 15px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .stats-grid {
            display: table;
            width: 100%;
        }
        .stats-row {
            display: table-row;
        }
        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 10px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }
        .stat-value {
            font-size: 20px;
            font-weight: bold;
            color: #4f46e5;
        }
        .stat-label {
            font-size: 10px;
            color: #6b7280;
            margin-top: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table th {
            background: #f3f4f6;
            padding: 8px;
            text-align: left;
            font-size: 10px;
            border: 1px solid #e5e7eb;
        }
        table td {
            padding: 6px 8px;
            border: 1px solid #e5e7eb;
            font-size: 10px;
        }
        table tr:nth-child(even) {
            background: #f9fafb;
        }
        .funnel-stage {
            padding: 8px;
            margin: 3px 0;
            background: #f3f4f6;
            border-left: 4px solid #4f46e5;
        }
        .funnel-stage .name {
            font-weight: bold;
        }
        .funnel-stage .stats {
            color: #6b7280;
            font-size: 10px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            padding: 10px;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
        }
        .page-break {
            page-break-after: always;
        }
        .two-col {
            display: table;
            width: 100%;
        }
        .two-col .col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 10px;
        }
        .two-col .col:last-child {
            padding-right: 0;
            padding-left: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Raport Platformă Meseriași</h1>
        <p>Perioada: {{ $period['start'] }} - {{ $period['end'] }} | Generat: {{ $generated_at }}</p>
    </div>

    <!-- Traffic Stats -->
    <div class="section">
        <div class="section-title">📊 Statistici Trafic</div>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($traffic['total_visits']) }}</div>
                    <div class="stat-label">Vizite Totale</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($traffic['unique_visitors']) }}</div>
                    <div class="stat-label">Vizitatori Unici</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($traffic['page_views']) }}</div>
                    <div class="stat-label">Pagini Vizualizate</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $funnel['conversion_rate'] }}%</div>
                    <div class="stat-label">Rată Conversie</div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Stats -->
    <div class="section">
        <div class="section-title">👥 Statistici Utilizatori</div>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($users['total_craftsmen']) }}</div>
                    <div class="stat-label">Total Meșteri</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($users['total_clients']) }}</div>
                    <div class="stat-label">Total Clienți</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($users['new_craftsmen']) }}</div>
                    <div class="stat-label">Meșteri Noi</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($users['new_clients']) }}</div>
                    <div class="stat-label">Clienți Noi</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Engagement Stats -->
    <div class="section">
        <div class="section-title">📈 Angajament</div>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($engagement['profile_views']) }}</div>
                    <div class="stat-label">Profiluri Vizualizate</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($engagement['messages_sent']) }}</div>
                    <div class="stat-label">Mesaje Trimise</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($engagement['quote_requests']) }}</div>
                    <div class="stat-label">Cereri Ofertă</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($engagement['appointments_booked']) }}</div>
                    <div class="stat-label">Programări</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Conversion Funnel -->
    <div class="section">
        <div class="section-title">🔄 Pâlnie Conversie</div>
        @foreach($funnel['stages'] as $stage)
        <div class="funnel-stage">
            <span class="name">{{ $stage['name'] }}</span>
            <span class="stats">- {{ number_format($stage['count']) }} utilizatori ({{ $stage['percentage'] }}%)</span>
        </div>
        @endforeach
    </div>

    <div class="page-break"></div>

    <!-- Traffic Sources & Devices -->
    <div class="two-col">
        <div class="col">
            <div class="section">
                <div class="section-title">🌐 Surse Trafic</div>
                <table>
                    <thead>
                        <tr>
                            <th>Sursă</th>
                            <th>Vizite</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($traffic_sources as $source)
                        <tr>
                            <td>{{ ucfirst($source['source']) }}</td>
                            <td>{{ number_format($source['count']) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col">
            <div class="section">
                <div class="section-title">📱 Dispozitive</div>
                <table>
                    <thead>
                        <tr>
                            <th>Dispozitiv</th>
                            <th>Vizitatori</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($devices as $device)
                        <tr>
                            <td>{{ ucfirst($device['device_type']) }}</td>
                            <td>{{ number_format($device['count']) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Craftsmen -->
    <div class="section">
        <div class="section-title">⭐ Top 10 Meșteri</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nume</th>
                    <th>Email</th>
                    <th>Recenzii</th>
                    <th>Rating</th>
                </tr>
            </thead>
            <tbody>
                @foreach($top_craftsmen as $index => $craftsman)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $craftsman->name }}</td>
                    <td>{{ $craftsman->email }}</td>
                    <td>{{ $craftsman->reviews_received_count }}</td>
                    <td>{{ number_format($craftsman->reviews_received_avg_rating ?? 0, 1) }}/5</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        Meseriași.ro - Raport generat automat | {{ $generated_at }}
    </div>
</body>
</html>
