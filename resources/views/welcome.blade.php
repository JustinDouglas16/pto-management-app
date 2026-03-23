<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        <title>{{ config('app.name', 'PTO Management App') }}</title>
    </head>
    <body class="min-h-screen bg-zinc-950 text-white">
        <div class="relative isolate overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(59,130,246,0.22),_transparent_35%),radial-gradient(circle_at_right,_rgba(124,58,237,0.2),_transparent_30%)]"></div>
            <div class="relative mx-auto flex min-h-screen max-w-7xl flex-col px-6 py-10 lg:px-8">
                <header class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-blue-300">PTO Management App</p>
                        <h1 class="mt-3 text-4xl font-semibold tracking-tight text-white sm:text-6xl">Plan, approve, and track leave for a small team.</h1>
                    </div>
                    <nav class="flex items-center gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-zinc-950 transition hover:bg-zinc-200">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-full border border-white/20 px-5 py-2.5 text-sm font-semibold text-white transition hover:border-white/40 hover:bg-white/5">Sign in</a>
                            <a href="{{ route('register') }}" class="rounded-full bg-blue-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-400">Create account</a>
                        @endauth
                    </nav>
                </header>

                <main class="grid flex-1 items-center gap-12 py-16 lg:grid-cols-[1.25fr_0.95fr] lg:py-20">
                    <section class="space-y-8">
                        <p class="max-w-2xl text-lg leading-8 text-zinc-300">
                            This v1 is designed for a company of fewer than 20 people with employee login, leave booking,
                            a shared company calendar, admin approvals, and monthly PTO accrual.
                        </p>

                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach ([
                                ['title' => 'Shared PTO balance', 'body' => 'Vacation and Paid Leave draw from the same monthly-accrued balance.'],
                                ['title' => 'Half-day support', 'body' => 'Half day is modeled as a duration option, not a leave type.'],
                                ['title' => 'Simple calendar rules', 'body' => 'Everyone can see who is off and weekends are excluded from counts.'],
                                ['title' => 'Admin workflow', 'body' => 'Requests move through pending, approved, rejected, and cancelled states.'],
                            ] as $feature)
                                <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur">
                                    <h2 class="text-lg font-semibold">{{ $feature['title'] }}</h2>
                                    <p class="mt-2 text-sm leading-6 text-zinc-300">{{ $feature['body'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <section class="rounded-[2rem] border border-white/10 bg-white/5 p-8 shadow-2xl shadow-blue-950/20 backdrop-blur">
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-blue-300">Initial scope</p>
                        <ul class="mt-6 space-y-4 text-sm text-zinc-200">
                            <li class="rounded-2xl border border-white/10 bg-zinc-900/70 p-4">Employee dashboard with PTO balance, pending requests, and upcoming leave.</li>
                            <li class="rounded-2xl border border-white/10 bg-zinc-900/70 p-4">Leave types for Vacation, Paid Leave, Sick Leave, and Urgent Leave.</li>
                            <li class="rounded-2xl border border-white/10 bg-zinc-900/70 p-4">PTO ledger to support monthly accruals, carry-over, and admin adjustments.</li>
                            <li class="rounded-2xl border border-white/10 bg-zinc-900/70 p-4">Future-ready structure for approvals, audit logging, and holiday exclusions later.</li>
                        </ul>
                    </section>
                </main>
            </div>
        </div>
    </body>
</html>
