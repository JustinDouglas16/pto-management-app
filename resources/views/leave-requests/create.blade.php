<x-layouts::app :title="__('Request Leave')">
    <div class="mx-auto flex w-full max-w-5xl flex-1 flex-col gap-6 p-4 md:p-6">
        <section class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-blue-500 dark:text-blue-300">Employee request flow</p>
                    <h1 class="mt-2 text-3xl font-semibold text-zinc-950 dark:text-white">Request paid leave</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                        Submit vacation, paid leave, sick leave, or urgent leave here. PTO-using leave types draw from your shared balance.
                    </p>
                </div>
                <div class="rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-900 dark:border-blue-900/50 dark:bg-blue-950/40 dark:text-blue-100">
                    Current seeded PTO balance: {{ $balance }} day(s)
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
            <form method="POST" action="{{ route('leave-requests.store') }}" class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                @csrf

                <div class="grid gap-5 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label for="leave_type_id" class="mb-2 block text-sm font-medium text-zinc-700 dark:text-zinc-200">Leave type</label>
                        <select id="leave_type_id" name="leave_type_id" class="w-full rounded-2xl border border-zinc-300 bg-white px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-950" required>
                            <option value="">Select a leave type</option>
                            @foreach ($leaveTypes as $leaveType)
                                <option value="{{ $leaveType->id }}" @selected(old('leave_type_id') == $leaveType->id)>
                                    {{ $leaveType->name }}{{ $leaveType->consumes_pto ? ' (uses PTO)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('leave_type_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="start_date" class="mb-2 block text-sm font-medium text-zinc-700 dark:text-zinc-200">Start date</label>
                        <input id="start_date" name="start_date" type="date" value="{{ old('start_date') }}" class="w-full rounded-2xl border border-zinc-300 bg-white px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-950" required>
                        @error('start_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="end_date" class="mb-2 block text-sm font-medium text-zinc-700 dark:text-zinc-200">End date</label>
                        <input id="end_date" name="end_date" type="date" value="{{ old('end_date') }}" class="w-full rounded-2xl border border-zinc-300 bg-white px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-950" required>
                        @error('end_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="duration_type" class="mb-2 block text-sm font-medium text-zinc-700 dark:text-zinc-200">Duration</label>
                        <select id="duration_type" name="duration_type" class="w-full rounded-2xl border border-zinc-300 bg-white px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-950" required>
                            <option value="full_day" @selected(old('duration_type', 'full_day') === 'full_day')>Full day</option>
                            <option value="half_day" @selected(old('duration_type') === 'half_day')>Half day</option>
                        </select>
                        @error('duration_type') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="half_day_period" class="mb-2 block text-sm font-medium text-zinc-700 dark:text-zinc-200">Half-day period</label>
                        <select id="half_day_period" name="half_day_period" class="w-full rounded-2xl border border-zinc-300 bg-white px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                            <option value="">Only needed for half-day</option>
                            <option value="morning" @selected(old('half_day_period') === 'morning')>Morning</option>
                            <option value="afternoon" @selected(old('half_day_period') === 'afternoon')>Afternoon</option>
                        </select>
                        @error('half_day_period') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="reason" class="mb-2 block text-sm font-medium text-zinc-700 dark:text-zinc-200">Reason (optional)</label>
                        <textarea id="reason" name="reason" rows="4" class="w-full rounded-2xl border border-zinc-300 bg-white px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-950" placeholder="Add context for your manager if needed">{{ old('reason') }}</textarea>
                        @error('reason') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap gap-3">
                    <button type="submit" class="rounded-full bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-500">
                        Submit request
                    </button>
                    <a href="{{ route('leave-requests.index') }}" class="rounded-full border border-zinc-300 px-5 py-3 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">
                        View my requests
                    </a>
                </div>
            </form>

            <div class="space-y-6">
                <section class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">PTO-consuming leave types</h2>
                    <div class="mt-4 space-y-3 text-sm text-zinc-600 dark:text-zinc-300">
                        @foreach ($ptoLeaveTypes as $leaveType)
                            <div class="rounded-2xl px-4 py-3" style="background-color: {{ $leaveType->color }}12; color: {{ $leaveType->color }};">
                                {{ $leaveType->name }}
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">Rules applied in this form</h2>
                    <ul class="mt-4 space-y-3 text-sm text-zinc-600 dark:text-zinc-300">
                        <li>Only weekdays count toward full-day totals.</li>
                        <li>Half-day requests must stay on one date.</li>
                        <li>New requests are submitted as pending for approval.</li>
                    </ul>
                </section>
            </div>
        </section>
    </div>
</x-layouts::app>
