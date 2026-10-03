<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\FilingSubject;

class Organisation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'website',
        'address_line_1',
        'address_line_2',
        'city',
        'county',
        'postcode',
        'country',
        'type',
        'status',
        'timezone',
        'currency',
        'logo',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
        ];
    }

    /*
     * Organisation members
     */
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'organisation_users',
            'organisation_id',
            'user_id'
        )->withPivot([
           // 'role_id',
            'status',
            'joined_at',
        ])->withTimestamps();
    }

    /*
     * Roles belonging to this organisation
     */
    public function roles()
    {
        return $this->hasMany(Role::class);
    }

    /*
     * Invitations
     */
    public function invitations()
    {
        return $this->hasMany(OrganisationInvitation::class);
    }

  
    public function filingSubjects()
    {
        return $this->hasMany(
            FilingSubject::class
        );
    }

    public function filings()
    {
        return $this->hasMany(Filing::class);
    }
}