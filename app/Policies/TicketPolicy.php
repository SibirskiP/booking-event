<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;

use Illuminate\Auth\Access\Response;

class TicketPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Event $event) {
        return $user->role === 'admin' || $event->created_by === $user->id;
    }

    public function update(User $user, Ticket $ticket) {
        return $user->role === 'admin' || $ticket->event->created_by === $user->id;
    }

    public function delete(User $user, Ticket $ticket) {
        return $user->role === 'admin' || $ticket->event->created_by === $user->id;
    }


    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Ticket $ticket): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Ticket $ticket): bool
    {
        return false;
    }
}
