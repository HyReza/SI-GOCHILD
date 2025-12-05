<?php

namespace App\Policies;

use App\Models\Mmdst_Parameter;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MmdstParameterPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Mmdst_Parameter $mmdstParameter): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Mmdst_Parameter $mmdstParameter): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Mmdst_Parameter $mmdstParameter): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Mmdst_Parameter $mmdstParameter): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Mmdst_Parameter $mmdstParameter): bool
    {
        //
    }
}
