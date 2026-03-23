<?php

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\PtoTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard', [
        'stats' => [
            'employees' => User::query()->where('is_active', true)->count(),
            'leaveTypes' => LeaveType::query()->where('active', true)->count(),
            'pendingRequests' => LeaveRequest::query()->where('status', 'pending')->count(),
            'ptoAccruals' => PtoTransaction::query()->where('type', 'monthly_accrual')->sum('amount'),
        ],
        'upcomingRequests' => LeaveRequest::query()
            ->with(['user:id,name', 'leaveType:id,name,color'])
            ->whereIn('status', ['pending', 'approved'])
            ->orderBy('start_date')
            ->limit(5)
            ->get(),
        'leaveTypes' => LeaveType::query()->where('active', true)->orderBy('name')->get(),
        'rules' => [
            '1 PTO day accrues monthly for each active employee.',
            'Vacation and Paid Leave consume the shared PTO balance.',
            'Sick Leave and Urgent Leave are tracked separately for v1.',
            'Half-day requests are allowed for a single date only.',
            'Weekends are excluded from leave duration calculations.',
        ],
    ])->name('dashboard');
});

require __DIR__.'/settings.php';
