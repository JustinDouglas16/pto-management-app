<x-layouts::app :title="__('Active Employees')">
    <div class="mx-auto flex w-full max-w-6xl flex-1 flex-col gap-6 p-4 md:p-6">
        <section class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-blue-500 dark:text-blue-300">Admin workspace</p>
            <h1 class="mt-2 text-3xl font-semibold text-zinc-950 dark:text-white">Active employees</h1>
            <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-300">See who is active in the company and what role they currently have in the system.</p>
        </section>

        <section class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-800">
                    <thead>
                        <tr class="text-left text-zinc-500 dark:text-zinc-400">
                            <th class="py-3 pr-4 font-medium">Employee</th>
                            <th class="py-3 pr-4 font-medium">Email</th>
                            <th class="py-3 pr-4 font-medium">Role</th>
                            <th class="py-3 pr-4 font-medium">Hire date</th>
                            <th class="py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-900">
                        @foreach ($employees as $employee)
                            <tr>
                                <td class="py-4 pr-4 font-medium text-zinc-950 dark:text-white">{{ $employee->name }}</td>
                                <td class="py-4 pr-4 text-zinc-600 dark:text-zinc-300">{{ $employee->email }}</td>
                                <td class="py-4 pr-4 text-zinc-600 dark:text-zinc-300">{{ ucfirst($employee->role) }}</td>
                                <td class="py-4 pr-4 text-zinc-600 dark:text-zinc-300">{{ optional($employee->hire_date)->format('M j, Y') ?? '—' }}</td>
                                <td class="py-4">
                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-200">Active</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-layouts::app>
