<?php

namespace App\Policies;

use App\Models\ReadingPlan;
use App\Models\User;

class ReadingPlanPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ReadingPlan $plan): bool
    {
        return $user->id === $plan->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ReadingPlan $plan): bool
    {
        return $user->id === $plan->user_id;
    }

    public function delete(User $user, ReadingPlan $plan): bool
    {
        return $user->id === $plan->user_id;
    }

    public function markDone(User $user, ReadingPlan $plan): bool
    {
        return $user->id === $plan->user_id;
    }
}
