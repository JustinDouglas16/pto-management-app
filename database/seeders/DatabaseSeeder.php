<?php

namespace Database\Seeders;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\PtoTransaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Alex Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'hire_date' => Carbon::parse('2025-01-01'),
                'email_verified_at' => now(),
            ],
        );

        $employee = User::query()->updateOrCreate(
            ['email' => 'employee@example.com'],
            [
                'name' => 'Taylor Employee',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'is_active' => true,
                'hire_date' => Carbon::parse('2025-06-01'),
                'email_verified_at' => now(),
            ],
        );

        collect([
            ['name' => 'Vacation', 'code' => 'vacation', 'color' => '#2563eb', 'consumes_pto' => true],
            ['name' => 'Paid Leave', 'code' => 'paid_leave', 'color' => '#7c3aed', 'consumes_pto' => true],
            ['name' => 'Sick Leave', 'code' => 'sick_leave', 'color' => '#dc2626', 'consumes_pto' => false],
            ['name' => 'Urgent Leave', 'code' => 'urgent_leave', 'color' => '#ea580c', 'consumes_pto' => false],
        ])->each(function (array $leaveType): void {
            LeaveType::query()->updateOrCreate(
                ['code' => $leaveType['code']],
                $leaveType + ['requires_approval' => true, 'active' => true],
            );
        });

        PtoTransaction::query()->updateOrCreate(
            [
                'user_id' => $employee->id,
                'type' => 'monthly_accrual',
                'reference_month' => Carbon::parse('2026-03-01')->toDateString(),
            ],
            [
                'amount' => 1.0,
                'effective_date' => Carbon::parse('2026-03-01')->toDateString(),
                'notes' => 'Monthly PTO accrual for March 2026',
                'created_by' => $admin->id,
            ],
        );

        $vacation = LeaveType::query()->where('code', 'vacation')->firstOrFail();

        LeaveRequest::query()->updateOrCreate(
            [
                'user_id' => $employee->id,
                'leave_type_id' => $vacation->id,
                'start_date' => Carbon::parse('2026-04-14')->toDateString(),
                'end_date' => Carbon::parse('2026-04-15')->toDateString(),
            ],
            [
                'duration_type' => 'full_day',
                'half_day_period' => null,
                'total_days' => 2,
                'reason' => 'Family trip planned in advance.',
                'status' => 'pending',
            ],
        );
    }
}
