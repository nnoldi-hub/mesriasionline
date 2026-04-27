<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raport Meșter - {{ $craftsman->name }}</title>
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
            border-bottom: 2px solid #10b981;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #10b981;
            font-size: 22px;
            margin-bottom: 5px;
        }
        .header .craftsman-name {
            font-size: 16px;
            color: #374151;
            margin-bottom: 5px;
        }
        .header p {
            color: #6b7280;
            font-size: 11px;
        }
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .section-title {
            background: #10b981;
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
            color: #10b981;
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
        .rating-bar {
            display: inline-block;
            height: 8px;
            background: #fbbf24;
            margin-left: 5px;
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
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #dbeafe; color: #1e40af; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Raport Performanță</h1>
        <div class="craftsman-name">{{ $craftsman->name }}</div>
        <p>Perioada: {{ $period['start'] }} - {{ $period['end'] }} | Generat: {{ $generated_at }}</p>
    </div>

    <!-- Appointments Summary -->
    <div class="section">
        <div class="section-title">📅 Programări</div>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-value">{{ $appointments['total'] }}</div>
                    <div class="stat-label">Total</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $appointments['completed'] }}</div>
                    <div class="stat-label">Finalizate</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $appointments['pending'] }}</div>
                    <div class="stat-label">În Așteptare</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $appointments['cancelled'] }}</div>
                    <div class="stat-label">Anulate</div>
                </div>
            </div>
        </div>

        @if($appointments['list']->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Ora</th>
                    <th>Client</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments['list'] as $apt)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($apt->date)->format('d.m.Y') }}</td>
                    <td>{{ $apt->time }}</td>
                    <td>{{ $apt->user->name ?? 'N/A' }}</td>
                    <td>
                        @if($apt->status === 'completed')
                            <span class="badge badge-success">Finalizat</span>
                        @elseif($apt->status === 'cancelled')
                            <span class="badge badge-danger">Anulat</span>
                        @elseif($apt->status === 'confirmed')
                            <span class="badge badge-info">Confirmat</span>
                        @else
                            <span class="badge badge-warning">În așteptare</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <!-- Quotes Summary -->
    <div class="section">
        <div class="section-title">💰 Oferte</div>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-value">{{ $quotes['total'] }}</div>
                    <div class="stat-label">Total Oferte</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $quotes['accepted'] }}</div>
                    <div class="stat-label">Acceptate</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $quotes['pending'] }}</div>
                    <div class="stat-label">În Așteptare</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($quotes['total_value'], 0) }} RON</div>
                    <div class="stat-label">Valoare Acceptate</div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-break"></div>

    <!-- Reviews Summary -->
    <div class="section">
        <div class="section-title">⭐ Recenzii</div>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-value">{{ $reviews['total'] }}</div>
                    <div class="stat-label">Total Recenzii</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $reviews['average_rating'] }}/5</div>
                    <div class="stat-label">Rating Mediu</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $reviews['rating_distribution'][5] }}</div>
                    <div class="stat-label">5 Stele</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $reviews['rating_distribution'][4] }}</div>
                    <div class="stat-label">4 Stele</div>
                </div>
            </div>
        </div>

        <h4 style="margin-top: 15px; margin-bottom: 10px;">Distribuție Rating:</h4>
        @foreach([5, 4, 3, 2, 1] as $rating)
        <div style="margin-bottom: 5px;">
            <span style="display: inline-block; width: 50px;">{{ $rating }} ⭐</span>
            <span class="rating-bar" style="width: {{ $reviews['rating_distribution'][$rating] * 10 }}px;"></span>
            <span style="margin-left: 5px;">{{ $reviews['rating_distribution'][$rating] }}</span>
        </div>
        @endforeach

        @if($reviews['list']->count() > 0)
        <table style="margin-top: 15px;">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Rating</th>
                    <th>Comentariu</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reviews['list'] as $review)
                <tr>
                    <td>{{ $review->user->name ?? 'Anonim' }}</td>
                    <td>{{ $review->rating }}/5</td>
                    <td>{{ \Str::limit($review->comment, 80) }}</td>
                    <td>{{ $review->created_at->format('d.m.Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <!-- Conversion Stats -->
    <div class="section">
        <div class="section-title">📊 Statistici Conversie</div>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-value">{{ $conversions['metrics']['profile_views'] }}</div>
                    <div class="stat-label">Vizualizări Profil</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $conversions['metrics']['contact_clicks'] }}</div>
                    <div class="stat-label">Click-uri Contact</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $conversions['conversion_rates']['view_to_contact'] }}%</div>
                    <div class="stat-label">Vizită → Contact</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $conversions['conversion_rates']['quote_to_booking'] }}%</div>
                    <div class="stat-label">Ofertă → Booking</div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        Meseriași.ro - Raport generat automat | {{ $generated_at }}
    </div>
</body>
</html>
