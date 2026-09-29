<?php

namespace App\Services;

use App\Models\Organisation;
use App\Models\OrganisationInvitation;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class OrganisationInvitationService
{
    public function all(
        Organisation $organisation
    ): Collection {
        return $organisation
            ->invitations()
            ->with([
                'role',
                'inviter',
            ])
            ->latest()
            ->get();
    }

    public function create(
        Organisation $organisation,
        int $invitedBy,
        array $data
    ): OrganisationInvitation {
        return DB::transaction(function () use (
            $organisation,
            $invitedBy,
            $data
        ) {

            $role = null;

            if (!empty($data['role_id'])) {
                $role = Role::query()
                    ->where('id', $data['role_id'])
                    ->where(
                        'organisation_id',
                        $organisation->id
                    )
                    ->firstOrFail();

                if (!$role->is_active) {
                    abort(
                        422,
                        'The selected role is inactive.'
                    );
                }
            }

            /*
             * Do not allow multiple pending invitations
             * for the same email in the same organisation.
             */
            $existing = OrganisationInvitation::query()
                ->where(
                    'organisation_id',
                    $organisation->id
                )
                ->where(
                    'email',
                    $data['email']
                )
                ->where(
                    'status',
                    'pending'
                )
                ->where(
                    'expires_at',
                    '>',
                    now()
                )
                ->exists();

            if ($existing) {
                abort(
                    422,
                    'A pending invitation already exists for this email.'
                );
            }

            /*
             * Generate a random plain token.
             * Only the hash is stored in the database.
             */
            $plainToken = Str::random(64);

            $invitation = OrganisationInvitation::create([
                'organisation_id' => $organisation->id,
                'invited_by' => $invitedBy,
                'role_id' => $role?->id,
                'email' => strtolower(
                    trim($data['email'])
                ),
                'token_hash' => hash(
                    'sha256',
                    $plainToken
                ),
                'status' => 'pending',
                'expires_at' => now()->addDays(7),
            ]);

            /*
             * We return the plain token internally so it
             * can later be used to send the invitation URL.
             */
            $invitation->setAttribute(
                'plain_token',
                $plainToken
            );

            return $invitation->load([
                'role',
                'inviter',
            ]);
        });
    }

    public function cancel(
        Organisation $organisation,
        int $invitationId
    ): OrganisationInvitation {
        $invitation = $organisation
            ->invitations()
            ->where('id', $invitationId)
            ->firstOrFail();

        if ($invitation->status !== 'pending') {
            abort(
                422,
                'Only pending invitations can be cancelled.'
            );
        }

        $invitation->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return $invitation->fresh([
            'role',
            'inviter',
        ]);
    }

    public function accept(
        string $plainToken,
        array $data
    ): array {
        return DB::transaction(function () use (
            $plainToken,
            $data
        ) {

            $tokenHash = hash(
                'sha256',
                $plainToken
            );

            $invitation = OrganisationInvitation::query()
                ->where('token_hash', $tokenHash)
                ->where('status', 'pending')
                ->where('expires_at', '>', now())
                ->firstOrFail();

            $organisation = $invitation->organisation;

            $role = null;

            if ($invitation->role_id) {
                $role = Role::query()
                    ->where('id', $invitation->role_id)
                    ->where(
                        'organisation_id',
                        $organisation->id
                    )
                    ->where('is_active', true)
                    ->firstOrFail();
            }

            $user = User::where(
                'email',
                $invitation->email
            )->first();

            if (!$user) {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $invitation->email,
                    'password' => $data['password'],
                ]);
            }

            /*
            * Prevent duplicate organisation membership.
            */
            $membership = $organisation->users()
                ->where('users.id', $user->id)
                ->first();

            if (!$membership) {
                $organisation->users()->attach(
                    $user->id,
                    [
                        'status' => 'active',
                        'joined_at' => now(),
                    ]
                );
            }

            /*
            * Assign the invited role.
            */
            if ($role) {
                $alreadyAssigned = $role->users()
                    ->where('users.id', $user->id)
                    ->wherePivot(
                        'organisation_id',
                        $organisation->id
                    )
                    ->exists();

                if (!$alreadyAssigned) {
                    $role->users()->attach(
                        $user->id,
                        [
                            'organisation_id' =>
                                $organisation->id,
                        ]
                    );
                }
            }

            $invitation->update([
                'status' => 'accepted',
                'accepted_at' => now(),
            ]);

            return [
                'user' => $user->fresh(),
                'organisation' => $organisation->fresh(),
                'role' => $role?->fresh(),
            ];
        });
    }
}