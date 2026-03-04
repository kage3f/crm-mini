<?php

namespace App\Livewire\Dashboard;

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Opportunity;
use App\Models\Subscription;
use App\Models\Task;
use Livewire\Component;

class Index extends Component
{
    public function getTotalClientsProperty(): int
    {
        return Client::count();
    }

    public function getOpenOpportunitiesProperty(): int
    {
        return Opportunity::whereHas('stage', fn($q) => $q->whereNotIn('name', ['Fechado - Ganho', 'Fechado - Perdido']))
            ->count();
    }

    public function getEstimatedRevenueProperty(): float
    {
        return (float) Opportunity::whereHas('stage', fn($q) => $q->whereNotIn('name', ['Fechado - Ganho', 'Fechado - Perdido']))
            ->sum('value');
    }

    public function getClosedRevenueThisMonthProperty(): float
    {
        return (float) Opportunity::whereHas('stage', fn($q) => $q->where('name', 'Fechado - Ganho'))
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->sum('value');
    }

    public function getOverdueTasksCountProperty(): int
    {
        return Task::where('status', '!=', 'done')
            ->whereNotNull('due_date')
            ->where('due_date', '<', today())
            ->count();
    }

    public function getRecentActivityProperty()
    {
        return ActivityLog::orderByDesc('created_at')->take(10)->get();
    }

    public function getSubscriptionProperty()
    {
        return Subscription::where('company_id', auth()->user()->company_id)
            ->with('plan')
            ->first();
    }

    public function getMonthlyChartDataProperty(): array
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $data[] = [
                'month' => $month->translatedFormat('M'),
                'revenue' => (float) Opportunity::whereHas('stage', fn($q) => $q->where('name', 'Fechado - Ganho'))
                    ->whereYear('updated_at', $month->year)
                    ->whereMonth('updated_at', $month->month)
                    ->sum('value'),
                'deals' => Opportunity::whereHas('stage', fn($q) => $q->where('name', 'Fechado - Ganho'))
                    ->whereYear('updated_at', $month->year)
                    ->whereMonth('updated_at', $month->month)
                    ->count(),
            ];
        }
        return $data;
    }

    public function render()
    {
        return view('livewire.dashboard.index', [
            'totalClients'          => $this->totalClients,
            'openOpportunities'     => $this->openOpportunities,
            'estimatedRevenue'      => $this->estimatedRevenue,
            'closedRevenueThisMonth' => $this->closedRevenueThisMonth,
            'overdueTasksCount'     => $this->overdueTasksCount,
            'recentActivity'        => $this->recentActivity,
            'subscription'          => $this->subscription,
            'chartData'             => $this->monthlyChartData,
        ])->layout('layouts.app', ['title' => 'Dashboard']);
    }
}
