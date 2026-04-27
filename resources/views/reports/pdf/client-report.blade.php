<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raport Client - {{ $client->name }}</title>
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
            border-bottom: 2px solid #3b82f6;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #3b82f6;
            font-size: 22px;
            margin-bottom: 5px;
        }
        .header .client-name {
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
            background: #3b82f6;
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
            width: 33.33%;
            padding: 10px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }
        .stat-value {
            font-size: 20px;
            font-weight: bold;
            color: #3b82f6;
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
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-info { background: #dbeafe; color: #1e40af; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Raport Activitate</h1>
        <div class="client-name">{{ $client->name }}</div>
        <p>Perioada: {{ $period['start'] }} - {{ $period['end'] }} | Generat: {{ $generated_at }}</p>
    </div>

    <!-- Appointments -->
    <div class="section">
        <div class="section-title">📅 Programările Mele</div>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-value">{{ $appointments['total'] }}</div>
                    <div class="stat-label">Total Programări</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $appointments['completed'] }}</div>
                    <div class="stat-label">Finalizate</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $appointments['upcoming'] }}</div>
                    <div class="stat-label">Viitoare</div>
                </div>
            </div>
        </div>

        @if($appointments['list']->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Meșter</th>
                    <th>Serviciu</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments['list'] as $apt)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($apt->date)->format('d.m.Y') }}</td>
                    <td>{{ $apt->meserias->name ?? 'N/A' }}</td>
                    <td>{{ $apt->service->name ?? 'N/A' }}</td>
                    <td>
                        @if($apt->status === 'completed')
                            <span class="badge badge-success">Finalizat</span>
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

    <!-- Quote Requests -->
    <div class="section">
        <div class="section-title">📝 Cereri de Ofertă</div>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-value">{{ $quote_requests['total'] }}</div>
                    <div class="stat-label">Cereri Trimise</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $quote_requests['quotes_received'] }}</div>
                    <div class="stat-label">Oferte Primite</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $quote_requests['quotes_accepted'] }}</div>
                    <div class="stat-label">Oferte Acceptate</div>
                </div>
            </div>
        </div>

        @if($quote_requests['list']->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Serviciu</th>
                    <th>Descriere</th>
                    <th>Oferte Primite</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quote_requests['list'] as $qr)
                <tr>
                    <td>{{ $qr->service->name ?? 'N/A' }}</td>
                    <td>{{ \Str::limit($qr->description, 50) }}</td>
                    <td>{{ $qr->quotes->count() }}</td>
                    <td>{{ $qr->created_at->format('d.m.Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <!-- Reviews -->
    <div class="section">
        <div class="section-title">⭐ Recenziile Mele</div>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stat-box" style="width: 100%;">
                    <div class="stat-value">{{ $reviews['total'] }}</div>
                    <div class="stat-label">Recenzii Scrise</div>
                </div>
            </div>
        </div>

        @if($reviews['list']->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Meșter</th>
                    <th>Rating</th>
                    <th>Comentariu</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reviews['list'] as $review)
                <tr>
                    <td>{{ $review->meserias->name ?? 'N/A' }}</td>
                    <td>{{ $review->rating }}/5 ⭐</td>
                    <td>{{ \Str::limit($review->comment, 80) }}</td>
                    <td>{{ $review->created_at->format('d.m.Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <div class="footer">
        Meseriași.ro - Raport generat automat | {{ $generated_at }}
    </div>
</body>
</html>
