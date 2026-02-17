<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CourseDetailsExtMEA v2.0</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: #f5f5f5;
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .header h1 {
            color: #2c3e50;
            font-size: 24px;
        }

        .header p {
            color: #7f8c8d;
            margin-top: 5px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stat-card h3 {
            color: #7f8c8d;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card .number {
            color: #2c3e50;
            font-size: 32px;
            font-weight: bold;
            margin-top: 10px;
        }

        .section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .section-header {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 2px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-header h2 {
            color: #2c3e50;
            font-size: 18px;
        }

        .section-header .badge {
            background: #3498db;
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
        }

        .table-container {
            overflow-x: auto;
            max-height: 500px;
            overflow-y: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        th {
            background: #f1f3f5;
            color: #495057;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 10px;
            text-align: left;
            border-bottom: 2px solid #dee2e6;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        td {
            padding: 12px 10px;
            border-bottom: 1px solid #e9ecef;
            font-size: 13px;
            vertical-align: top;
        }

        tr:hover td {
            background: #f8f9fa;
        }

        .personnel-row {
            background: #f8f9fa;
        }

        .audit-row {
            background: #e8f4f8;
        }

        .error-row {
            background: #fee9e9;
        }

        .type-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
        }

        .type-start {
            background: #d4edda;
            color: #155724;
        }

        .type-end {
            background: #cce5ff;
            color: #004085;
        }

        .type-error {
            background: #f8d7da;
            color: #721c24;
        }

        .mrid-link {
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
        }

        .mrid-link:hover {
            text-decoration: underline;
        }

        .connection-line {
            font-size: 11px;
            color: #6c757d;
            margin-top: 4px;
        }

        .empty-state {
            padding: 40px;
            text-align: center;
            color: #6c757d;
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #6c757d;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>📊 CourseDetailsExtMEA v2.0</h1>
            <p>Dashboard showing all connected tables - Personnel, Audit Logs, and Error Logs</p>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>📋 Total Personnel Records</h3>
                <div class="number">{{ $totalPersonnel }}</div>
            </div>
            <div class="stat-card">
                <h3>📝 Total Audit Logs</h3>
                <div class="number">{{ $totalAuditLogs }}</div>
            </div>
            <div class="stat-card">
                <h3>⚠️ Total Error Logs</h3>
                <div class="number">{{ $totalErrorLogs }}</div>
            </div>
        </div>

        <!-- Personnel Table -->
        <div class="section">
            <div class="section-header">
                <h2>📋 Personnel Data</h2>
                <span class="badge">{{ count($personnelRecords) }} records</span>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>MRID</th>
                            <th>Name</th>
                            <th>Skill</th>
                            <th>Status</th>
                            <th>Source</th>
                            <th>Message ID</th>
                            <th>Created</th>
                            <th>Related Logs</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($personnelRecords as $personnel)
                        <tr class="personnel-row">
                            <td>#{{ $personnel->id }}</td>
                            <td>
                                <span class="mrid-link">{{ $personnel->mrid }}</span>
                            </td>
                            <td>
                                <strong>{{ $personnel->first_name }} {{ $personnel->last_name }}</strong>
                                @if($personnel->initials)
                                    <div style="font-size: 11px; color: #666;">Initials: {{ $personnel->initials }}</div>
                                @endif
                            </td>
                            <td>{{ $personnel->skill_description ?? 'N/A' }}</td>
                            <td>
                                @if($personnel->skill_status)
                                    <span class="type-badge type-{{ strtolower(str_replace(' ', '-', $personnel->skill_status)) }}" style="background: #d4edda; color: #155724;">
                                        {{ $personnel->skill_status }}
                                    </span>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $personnel->source_system ?? 'N/A' }}</td>
                            <td>
                                <small>{{ $personnel->message_id ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <small>{{ $personnel->created_at->format('Y-m-d H:i') }}</small>
                            </td>
                            <td>
                                <small>
                                    📝 {{ $personnel->auditLogs->count() }} audits
                                    <br>
                                    ⚠️ {{ $personnel->errorLogs->count() }} errors
                                </small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="empty-state">No personnel records found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Audit Logs Table -->
        <div class="section">
            <div class="section-header">
                <h2>📝 Recent Audit Logs</h2>
                <span class="badge">Last 50 records</span>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Transaction ID</th>
                            <th>Message UID</th>
                            <th>Component</th>
                            <th>Bus Key</th>
                            <th>Description</th>
                            <th>Source Time</th>
                            <th>Related Personnel</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentAuditLogs as $log)
                        <tr class="audit-row">
                            <td>#{{ $log->id }}</td>
                            <td>
                                <span class="type-badge type-{{ strtolower($log->audit_type) }}">
                                    {{ $log->audit_type }}
                                </span>
                            </td>
                            <td>
                                <small>{{ $log->transaction_id }}</small>
                            </td>
                            <td>
                                <small>{{ $log->message_uid }}</small>
                            </td>
                            <td>{{ $log->component_name }}</td>
                            <td>
                                <small>{{ Str::limit($log->bus_key_value, 30) }}</small>
                            </td>
                            <td>
                                <small>{{ Str::limit($log->description, 40) }}</small>
                            </td>
                            <td>
                                <small>{{ $log->source_timestamp ? $log->source_timestamp->format('Y-m-d H:i:s') : 'N/A' }}</small>
                            </td>
                            <td>
                                @if($log->personnel)
                                    <a href="#" class="mrid-link">{{ $log->personnel->mrid }}</a>
                                    <div class="connection-line">Connected via message_id</div>
                                @elseif($log->bus_key_value)
                                    <small>Extracting MRID...</small>
                                @else
                                    <small>No connection</small>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="empty-state">No audit logs found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Error Logs Table -->
        <div class="section">
            <div class="section-header">
                <h2>⚠️ Recent Error Logs</h2>
                <span class="badge">Last 50 records</span>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Transaction ID</th>
                            <th>Message UID</th>
                            <th>Component</th>
                            <th>Error Message</th>
                            <th>Source Time</th>
                            <th>Related Personnel</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentErrorLogs as $log)
                        <tr class="error-row">
                            <td>#{{ $log->id }}</td>
                            <td>
                                <small>{{ $log->transaction_id }}</small>
                            </td>
                            <td>
                                <small>{{ $log->message_uid }}</small>
                            </td>
                            <td>{{ $log->component_name }}</td>
                            <td>
                                <strong style="color: #dc3545;">{{ $log->error_message }}</strong>
                                @if($log->error_details)
                                    <div style="font-size: 11px; color: #666; margin-top: 4px;">
                                        {{ Str::limit($log->error_details, 50) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <small>{{ $log->source_timestamp ? $log->source_timestamp->format('Y-m-d H:i:s') : 'N/A' }}</small>
                            </td>
                            <td>
                                @if($log->personnel)
                                    <a href="#" class="mrid-link">{{ $log->personnel->mrid }}</a>
                                    <div class="connection-line">Connected via message_id</div>
                                @elseif($log->bus_key_value && preg_match('/(\d{7})/', $log->bus_key_value, $matches))
                                    <small>MRID: {{ $matches[1] ?? 'Unknown' }}</small>
                                @else
                                    <small>No connection</small>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="empty-state">No error logs found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            CourseDetailsExtMEA v2.0 | Showing all connected tables | Last updated: {{ now()->format('Y-m-d H:i:s') }}
        </div>
    </div>
</body>
</html>