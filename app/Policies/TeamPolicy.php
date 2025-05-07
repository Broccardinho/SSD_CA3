<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TeamPolicy
{
    /**
     * Determine whether the user can view the team.
     */
    public function view(User $user, Team $team): Response
    {
        return $user->id === $team->user_id
            ? Response::allow()
            : Response::deny('You do not own this team.');
    }

    /**
     * Determine whether the user can update the team.
     */
    public function update(User $user, Team $team): Response
    {
        return $user->id === $team->user_id
            ? Response::allow()
            : Response::deny('You do not own this team.');
    }

    /**
     * Determine whether the user can delete the team.
     */
    public function delete(User $user, Team $team): Response
    {
        return $user->id === $team->user_id
            ? Response::allow()
            : Response::deny('You do not own this team.');
    }
}
