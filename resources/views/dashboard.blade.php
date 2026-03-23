<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-3xl p-4 md:p-6">
        <section class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl space-y-3">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-blue-500 dark:text-blue-300">Implementation foundation</p>
                    <h1 class="text-3xl font-semibold text-zinc-950 dark:text-white">Small-company leave management, shaped around the README vision.</h1>
                    <p class="text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                        Use the employee request flow to submit paid leave directly from the app. This dashboard now highlights
                        your own PTO balance, your latest requests, and quick links to create a new request.
                    </p>
                </div>
                <div class="rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-900 dark:border-blue-900/50 dark:bg-blue-950/40 dark:text-blue-100">
                    Ready to submit vacation or paid leave requests.
                </div>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Active employees</p>
                <p class="mt-3 text-3xl font-semibold text-zinc-950 dark:text-white">{{ $stats['employees'] }}</p>
            </div>
            <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Active leave types</p>
                <p class="mt-3 text-3xl font-semibold text-zinc-950 dark:text-white">{{ $stats['leaveTypes'] }}</p>
            </div>
            <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Your pending requests</p>
                <p class="mt-3 text-3xl font-semibold text-zinc-950 dark:text-white">{{ $stats['pendingRequests'] }}</p>
            </div>
            <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Your PTO balance</p>
                <p class="mt-3 text-3xl font-semibold text-zinc-950 dark:text-white">{{ number_format((float) $stats['ptoAccruals'], 1) }}</p>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
            <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">Your recent requests</h2>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Track the requests you have already submitted and jump into a new one.</p>
                    </div>
                    <a href="{{ route('leave-requests.create') }}" class="inline-flex w-fit items-center rounded-full bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500">Request leave</a>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse ($upcomingRequests as $request)
                        <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-800">
                            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                <div>
                                    <p class="font-medium text-zinc-950 dark:text-white">{{ auth()->user()->name }}</p>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $request->start_date->format('M j, Y') }} - {{ $request->end_date->format('M j, Y') }}</p>
                                </div>
                                <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-semibold" style="background-color: {{ $request->leaveType->color }}20; color: {{ $request->leaveType->color }};">
                                    {{ $request->leaveType->name }} · {{ ucfirst($request->status) }}
                                </span>
                            </div>
                            <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-300">{{ $request->total_days }} day(s) · {{ str_replace('_', ' ', $request->duration_type) }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">You have not submitted any leave requests yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">V1 business rules</h2>
                    <ul class="mt-4 space-y-3 text-sm text-zinc-600 dark:text-zinc-300">
                        @foreach ($rules as $rule)
                            <li class="flex gap-3">
                                <span class="mt-1 size-2 rounded-full bg-blue-500"></span>
                                <span>{{ $rule }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">Leave catalog</h2>
                    <div class="mt-4 flex flex-wrap gap-3">
                        @foreach ($leaveTypes as $leaveType)
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium" style="background-color: {{ $leaveType->color }}20; color: {{ $leaveType->color }};">
                                {{ $leaveType->name }}
                                @if ($leaveType->consumes_pto)
                                    <span class="ml-2 rounded-full bg-white/70 px-2 py-0.5 text-[11px] uppercase tracking-wide dark:bg-zinc-950/50">Uses PTO</span>
                                @endif
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layouts::app>
