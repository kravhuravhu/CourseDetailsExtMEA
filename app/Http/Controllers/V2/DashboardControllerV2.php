<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\V2\PersonnelDataV2;
use App\Models\V2\AuditLogV2;
use App\Models\V2\ErrorLogV2;
use Illuminate\Http\Request;

class DashboardControllerV2 extends Controller
{
    public function index()
    {
        $personnelRecords = PersonnelDataV2::with(['auditLogs', 'errorLogs'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $totalPersonnel = PersonnelDataV2::count();
        $totalAuditLogs = AuditLogV2::count();
        $totalErrorLogs = ErrorLogV2::count();
        
        $recentAuditLogs = AuditLogV2::with('personnel')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $recentErrorLogs = ErrorLogV2::with('personnel')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
        
        return view('v2.dashboard', compact(
            'personnelRecords',
            'totalPersonnel',
            'totalAuditLogs',
            'totalErrorLogs',
            'recentAuditLogs',
            'recentErrorLogs'
        ));
    }
}