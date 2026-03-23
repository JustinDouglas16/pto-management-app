<x-layouts::app :title="__('Leave Approvals')">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-4 md:p-6">
        <section class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-blue-500 dark:text-blue-300">Admin workspace</p>
            <h1 class="mt-2 text-3xl font-semibold text-zinc-950 dark:text-white">Leave approvals</h1>
            <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-300">Review pending employee requests and approve or reject them from one queue.</p>
        </section>

        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-900/50 dark:bg-red-950/40 dark:text-red-100">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-100">
                {{ session('status') }}
            </div>
        @endif

        <section class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">Pending requests</h2>
                <div class="mt-6 space-y-4">
                    @forelse ($pendingRequests as $leaveRequest)
                        <div class="rounded-2xl border border-zinc-200 p-5 dark:border-zinc-800">
                            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                <div>
                                    <p class="font-semibold text-zinc-950 dark:text-white">{{ $leaveRequest->user->name }}</p>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $leaveRequest->user->email }}</p>
                                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $leaveRequest->start_date->format('M j, Y') }} - {{ $leaveRequest->end_date->format('M j, Y') }}</p>
                                </div>
                                <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-semibold" style="background-color: {{ $leaveRequest->leaveType->color }}20; color: {{ $leaveRequest->leaveType->color }};">
                                    {{ $leaveRequest->leaveType->name }} · {{ $leaveRequest->total_days }} day(s)
                                </span>
                            </div>

                            @if ($leaveRequest->reason)
                                <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-300">{{ $leaveRequest->reason }}</p>
                            @endif

                            <form method="POST" action="{{ route('admin.approvals.update', $leaveRequest) }}" class="mt-4 space-y-3">
                                @csrf
                                @method('PATCH')
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-200" for="rejection_reason_{{ $leaveRequest->id }}">Rejection reason (required only when rejecting)</label>
                                <textarea id="rejection_reason_{{ $leaveRequest->id }}" name="rejection_reason" rows="2" class="w-full rounded-2xl border border-zinc-300 bg-white px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-950" placeholder="Optional for approve, required for reject"></textarea>
                                <div class="flex flex-wrap gap-3">
                                    <button type="submit" name="decision" value="approved" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500">Approve</button>
                                    <button type="submit" name="decision" value="rejected" class="rounded-full bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-500">Reject</button>
                                </div>
                            </form>
                        </div>
                    @empty
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">There are no pending requests right now.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">Recently reviewed</h2>
                <div class="mt-6 space-y-4">
                    @forelse ($recentlyReviewed as $leaveRequest)
                        <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-800">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-medium text-zinc-950 dark:text-white">{{ $leaveRequest->user->name }}</p>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $leaveRequest->leaveType->name }} · {{ ucfirst($leaveRequest->status) }}</p>
                                </div>
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ optional($leaveRequest->approved_at)->format('M j, Y g:i A') }}</span>
                            </div>
                            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Reviewed by {{ $leaveRequest->approver->name ?? '—' }}</p>
                            @if ($leaveRequest->rejection_reason)
                                <p class="mt-2 text-sm text-red-600">Reason: {{ $leaveRequest->rejection_reason }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">No approvals or rejections have been recorded yet.</p>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</x-layouts::app>
