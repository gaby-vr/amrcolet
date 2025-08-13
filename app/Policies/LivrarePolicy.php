<?php
 
namespace App\Policies;
 
use App\Models\Livrare;
use App\Models\User;
 
class LivrarePolicy
{
    /**
     * Determine if the given post can be updated by the user.
     */
    public function read(User $user, Livrare $livrare): bool
    {
        return $user->id == $livrare->user_id || $user->is_admin == 1;
    }
}