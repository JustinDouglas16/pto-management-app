<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\LeaveRequestController;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\PtoTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function (Request $request) {
        $user = $request->user();

        return view('dashboard', [
            'stats' => [
                'employees' => User::query()->where('is_active', true)->count(),
                'leaveTypes' => LeaveType::query()->where('active', true)->count(),
                'pendingRequests' => $user->leaveRequests()->where('status', 'pending')->count(),
                'ptoAccruals' => PtoTransaction::query()->where('user_id', $user->id)->sum('amount'),
            ],
            'upcomingRequests' => $user->leaveRequests()
                ->with('leaveType:id,name,color')
                ->latest('start_date')
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
        ]);
    })->name('dashboard');

    Route::get('leave-requests', [LeaveRequestController::class, 'index'])->name('leave-requests.index');
    Route::get('leave-requests/create', [LeaveRequestController::class, 'create'])->name('leave-requests.create');
    Route::post('leave-requests', [LeaveRequestController::class, 'store'])->name('leave-requests.store');

    Route::get('admin/employees', [AdminController::class, 'employees'])->name('admin.employees');
    Route::get('admin/approvals', [AdminController::class, 'approvals'])->name('admin.approvals');
    Route::patch('admin/approvals/{leaveRequest}', [AdminController::class, 'updateApproval'])->name('admin.approvals.update');
});

require __DIR__.'/settings.php';
