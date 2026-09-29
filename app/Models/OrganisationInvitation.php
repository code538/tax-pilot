<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganisationInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'organisation_id',
        'invited_by',
        'role_id',
        'email',
        'token_hash',
        'status',
        'expires_at',
        'accepted_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'accepted_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function inviter()
    {
        return $this->belongsTo(
            User::class,
            'invited_by'
        );
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}