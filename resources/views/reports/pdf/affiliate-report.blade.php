<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raport Afiliere - {{ $affiliate->user->name ?? 'N/A' }}</title>
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
            border-bottom: 2px solid #8b5cf6;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #8b5cf6;
            font-size: 22px;
            margin-bottom: 5px;
        }
        .header .affiliate-name {
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
            background: #8b5cf6;
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
            color: #8b5cf6;
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
        .info-box {
            background: #f3f4f6;
            border-left: 4px solid #8b5cf6;
            padding: 10px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Raport Afiliere</h1>
        <div class="affiliate-name">{{ $affiliate->user->name ?? 'Afiliat' }}</div>
        <p>Cod Referral: <strong>{{ $affiliate->referral_code }}</strong></p>
        <p>Perioada: {{ $period['start'] }} - {{ $period['end'] }} | Generat: {{ $generated_at }}</p>
    </div>

    <!-- Affiliate Info -->
    <div class="info-box">
        <strong>Program:</strong> {{ $affiliate->program->name ?? 'Standard' }} |
        <strong>Status:</strong> {{ ucfirst($affiliate->status) }} |
        <strong>Membru din:</strong> {{ $affiliate->created_at->format('d.m.Y') }}
    </div>

    <!-- Referrals -->
    <div class="section">
        <div class="section-title">👥 Referrali</div>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($referrals['total_clicks']) }}</div>
                    <div class="stat-label">Click-uri Totale</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $referrals['registrations'] }}</div>
                    <div class="stat-label">Înregistrări</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $referrals['conversion_rate'] }}%</div>
                    <div class="stat-label">Rată Conversie</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $referrals['list']->count() }}</div>
                    <div class="stat-label">Referrali Activi</div>
                </div>
            </div>
        </div>

        @if($referrals['list']->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Utilizator</th>
                    <th>Status</th>
                    <th>Click-uri</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                @foreach($referrals['list']->take(20) as $referral)
                <tr>
                    <td>{{ $referral->referredUser->name ?? 'N/A' }}</td>
                    <td>
                        @if($referral->status === 'converted')
                            <span class="badge badge-success">Convertit</span>
                        @elseif($referral->status === 'registered')
                            <span class="badge badge-info">Înregistrat</span>
                        @else
                            <span class="badge badge-warning">În așteptare</span>
                        @endif
                    </td>
                    <td>{{ $referral->clicks }}</td>
                    <td>{{ $referral->created_at->format('d.m.Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <!-- Earnings -->
    <div class="section">
        <div class="section-title">💰 Câștiguri</div>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($earnings['total'], 2) }} RON</div>
                    <div class="stat-label">Total Câștiguri</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($earnings['pending'], 2) }} RON</div>
                    <div class="stat-label">În Așteptare</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($earnings['approved'], 2) }} RON</div>
                    <div class="stat-label">Aprobat</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($earnings['paid'], 2) }} RON</div>
                    <div class="stat-label">Plătit</div>
                </div>
            </div>
        </div>

        @if($earnings['list']->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tip</th>
                    <th>Sumă</th>
                    <th>Status</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                @foreach($earnings['list'] as $commission)
                <tr>
                    <td>#{{ $commission->id }}</td>
                    <td>{{ ucfirst($commission->type) }}</td>
                    <td>{{ number_format($commission->amount, 2) }} RON</td>
                    <td>
                        @if($commission->status === 'paid')
                            <span class="badge badge-success">Plătit</span>
                        @elseif($commission->status === 'approved')
                            <span class="badge badge-info">Aprobat</span>
                        @else
                            <span class="badge badge-warning">În așteptare</span>
                        @endif
                    </td>
                    <td>{{ $commission->created_at->format('d.m.Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <!-- Payouts -->
    <div class="section">
        <div class="section-title">🏦 Plăți</div>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($payouts['total_requested'], 2) }} RON</div>
                    <div class="stat-label">Total Solicitat</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($payouts['completed'], 2) }} RON</div>
                    <div class="stat-label">Finalizat</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($payouts['pending'], 2) }} RON</div>
                    <div class="stat-label">În Procesare</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $payouts['list']->count() }}</div>
                    <div class="stat-label">Cereri Plată</div>
                </div>
            </div>
        </div>

        @if($payouts['list']->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sumă</th>
                    <th>Metodă</th>
                    <th>Status</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payouts['list'] as $payout)
                <tr>
                    <td>#{{ $payout->id }}</td>
                    <td>{{ number_format($payout->amount, 2) }} RON</td>
                    <td>{{ strtoupper($payout->payment_method) }}</td>
                    <td>
                        @if($payout->status === 'completed')
                            <span class="badge badge-success">Finalizat</span>
                        @elseif($payout->status === 'processing')
                            <span class="badge badge-info">În procesare</span>
                        @else
                            <span class="badge badge-warning">În așteptare</span>
                        @endif
                    </td>
                    <td>{{ $payout->created_at->format('d.m.Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <div class="footer">
        Meseriași.ro - Raport Afiliere generat automat | {{ $generated_at }}
    </div>
</body>
</html>
