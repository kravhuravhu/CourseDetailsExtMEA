<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Personnel\Personnel;
use App\Models\Integration\IntegrationLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the main dashboard.
     *
     * @return View
     */
    public function index(): View
    {
        $totalPersonnel = Personnel::count();
        $activePersonnel = Personnel::whereHas('erpPersonnel', function ($q) {
            $q->whereNull('finish_date')
              ->orWhere('finish_date', '>', now());
        })->count();

        $recentMessages = IntegrationLog::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $messageStats = [
            'total' => IntegrationLog::count(),
            'success' => IntegrationLog::where('status', 'success')->count(),
            'error' => IntegrationLog::where('status', 'error')->count(),
            'pending' => IntegrationLog::where('status', 'processing')->count(),
        ];

        return view('dashboard.index', compact(
            'totalPersonnel',
            'activePersonnel',
            'recentMessages',
            'messageStats'
        ));
    }

    /**
     * Show integration dashboard.
     *
     * @return View
     */
    public function integration(): View
    {
        $logs = IntegrationLog::orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'adhoc' => IntegrationLog::where('message_type', 'adhoc')->count(),
            'takeon' => IntegrationLog::where('message_type', 'takeon')->count(),
            'today' => IntegrationLog::whereDate('created_at', today())->count(),
            'this_week' => IntegrationLog::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
        ];

        return view('dashboard.integration', compact('logs', 'stats'));
    }

    /**
     * Show personnel dashboard.
     *
     * @return View
     */
    public function personnel(): View
    {
        $personnel = Personnel::with(['erpPersonnel.erpPerson'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('dashboard.personnel', compact('personnel'));
    }

    /**
     * Show reports dashboard.
     *
     * @return View
     */
    public function reports(): View
    {
        // Generate report data
        $personnelByDate = $this->getPersonnelByDate();
        $integrationByStatus = $this->getIntegrationByStatus();
        $messagesByType = $this->getMessagesByType();

        return view('dashboard.reports', compact(
            'personnelByDate',
            'integrationByStatus',
            'messagesByType'
        ));
    }

    /**
     * Get personnel count by date.
     *
     * @return array
     */
    private function getPersonnelByDate(): array
    {
        $data = Personnel::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $data->pluck('date'),
            'values' => $data->pluck('count'),
        ];
    }

    /**
     * Get integration logs by status.
     *
     * @return array
     */
    private function getIntegrationByStatus(): array
    {
        $data = IntegrationLog::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        return [
            'labels' => $data->pluck('status'),
            'values' => $data->pluck('count'),
        ];
    }

    /**
     * Get messages by type.
     *
     * @return array
     */
    private function getMessagesByType(): array
    {
        $data = IntegrationLog::selectRaw('message_type, COUNT(*) as count')
            ->groupBy('message_type')
            ->get();

        return [
            'labels' => $data->pluck('message_type'),
            'values' => $data->pluck('count'),
        ];
    }
}