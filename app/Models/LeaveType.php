<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'color',
        'consumes_pto',
        'requires_approval',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'consumes_pto' => 'boolean',
            'requires_approval' => 'boolean',
            'active' => 'boolean',
        ];
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
