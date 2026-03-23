<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function employees(Request $request): View
    {
        $this->ensureAdmin($request);

        return view('admin.employees', [
            'employees' => User::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function approvals(Request $request): View
    {
        $this->ensureAdmin($request);

        return view('admin.approvals', [
            'pendingRequests' => LeaveRequest::query()
                ->with(['user:id,name,email', 'leaveType:id,name,color'])
                ->where('status', 'pending')
                ->orderBy('start_date')
                ->get(),
            'recentlyReviewed' => LeaveRequest::query()
                ->with(['user:id,name', 'leaveType:id,name,color', 'approver:id,name'])
                ->whereIn('status', ['approved', 'rejected'])
                ->latest('approved_at')
                ->limit(10)
                ->get(),
        ]);
    }

    public function updateApproval(Request $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        $this->ensureAdmin($request);

        abort_if($leaveRequest->user_id === $request->user()->id, 422, 'Admins cannot approve their own leave requests.');

        if ($leaveRequest->status !== 'pending') {
            return redirect()->route('admin.approvals')->with('status', 'That request has already been reviewed.');
        }

        $validated = $request->validate([
            'decision' => ['required', 'in:approved,rejected'],
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validated['decision'] === 'rejected' && blank($validated['rejection_reason'])) {
            return back()->withErrors([
                'rejection_reason' => 'Please provide a short reason when rejecting a request.',
            ]);
        }

        $leaveRequest->update([
            'status' => $validated['decision'],
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'rejection_reason' => $validated['decision'] === 'rejected' ? $validated['rejection_reason'] : null,
        ]);

        return redirect()->route('admin.approvals')->with('status', 'Leave request updated successfully.');
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->isAdmin(), 403);
    }
}
