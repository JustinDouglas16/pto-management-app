<x-layouts::app :title="__('My Requests')">
    <div class="mx-auto flex w-full max-w-6xl flex-1 flex-col gap-6 p-4 md:p-6">
        <section class="flex flex-col gap-4 rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-blue-500 dark:text-blue-300">Employee history</p>
                <h1 class="mt-2 text-3xl font-semibold text-zinc-950 dark:text-white">My leave requests</h1>
                <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-300">Track pending, approved, rejected, and cancelled requests in one place.</p>
            </div>
            <a href="{{ route('leave-requests.create') }}" class="rounded-full bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-500">
                Request leave
            </a>
        </section>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-100">
                {{ session('status') }}
            </div>
        @endif

        <section class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="space-y-4">
                @forelse ($requests as $leaveRequest)
                    <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-800">
                        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                            <div>
                                <p class="font-semibold text-zinc-950 dark:text-white">{{ $leaveRequest->leaveType->name }}</p>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $leaveRequest->start_date->format('M j, Y') }} - {{ $leaveRequest->end_date->format('M j, Y') }}</p>
                            </div>
                            <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-semibold capitalize" style="background-color: {{ $leaveRequest->leaveType->color }}20; color: {{ $leaveRequest->leaveType->color }};">
                                {{ $leaveRequest->status }}
                            </span>
                        </div>
                        <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-300">{{ $leaveRequest->total_days }} day(s) · {{ str_replace('_', ' ', $leaveRequest->duration_type) }}@if($leaveRequest->half_day_period) · {{ $leaveRequest->half_day_period }} @endif</p>
                        @if ($leaveRequest->reason)
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $leaveRequest->reason }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">You have not submitted any leave requests yet.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-layouts::app>
