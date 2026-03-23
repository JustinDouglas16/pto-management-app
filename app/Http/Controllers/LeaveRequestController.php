<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LeaveRequestController extends Controller
{
    public function index(Request $request): View
    {
        return view('leave-requests.index', [
            'requests' => $request->user()->leaveRequests()
                ->with('leaveType:id,name,color')
                ->latest('start_date')
                ->get(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('leave-requests.create', [
            'leaveTypes' => LeaveType::query()->where('active', true)->orderBy('name')->get(),
            'ptoLeaveTypes' => LeaveType::query()->where('active', true)->where('consumes_pto', true)->orderBy('name')->get(),
            'balance' => number_format((float) $request->user()->ptoTransactions()->sum('amount'), 1),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'duration_type' => ['required', 'in:full_day,half_day'],
            'half_day_period' => ['nullable', 'in:morning,afternoon'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);

        if ($validated['duration_type'] === 'half_day' && ! $request->filled('half_day_period')) {
            return back()->withErrors([
                'half_day_period' => 'Please choose morning or afternoon for a half-day request.',
            ])->withInput();
        }

        if ($validated['duration_type'] === 'half_day' && ! $startDate->isSameDay($endDate)) {
            return back()->withErrors([
                'end_date' => 'Half-day requests must start and end on the same date.',
            ])->withInput();
        }

        $totalDays = $validated['duration_type'] === 'half_day'
            ? 0.5
            : $this->countWeekdays($startDate, $endDate);

        if ($totalDays <= 0) {
            return back()->withErrors([
                'start_date' => 'Please choose at least one weekday for this leave request.',
            ])->withInput();
        }

        $request->user()->leaveRequests()->create([
            'leave_type_id' => $validated['leave_type_id'],
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'duration_type' => $validated['duration_type'],
            'half_day_period' => $validated['duration_type'] === 'half_day' ? $validated['half_day_period'] : null,
            'total_days' => $totalDays,
            'reason' => $validated['reason'] ?: null,
            'status' => 'pending',
        ]);

        return redirect()->route('leave-requests.index')->with('status', 'Leave request submitted for approval.');
    }

    private function countWeekdays(Carbon $startDate, Carbon $endDate): int
    {
        $days = 0;
        $cursor = $startDate->copy();

        while ($cursor->lte($endDate)) {
            if ($cursor->isWeekday()) {
                $days++;
            }

            $cursor->addDay();
        }

        return $days;
    }
}
