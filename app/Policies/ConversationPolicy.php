<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConversationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return true; // All authenticated users can list their conversations
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Conversation  $conversation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Conversation $conversation)
    {
        // Regular users can access their own conversations
        return $conversation->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return true; // All authenticated users can create conversations
    }
    
    /**
     * Check if a user can create a conversation for a specific business
     */
    public function createForBusiness(User $user, $businessId)
    {
        // Business admins and users can create conversations for their business
        if ($user->hasRole('business_admin') || $user->hasRole('business_user')) {
            return $user->business_id === $businessId;
        }
        
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Conversation  $conversation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Conversation $conversation)
    {
        // Business admins and users can update their business's conversations
        if ($user->hasRole('business_admin') || $user->hasRole('business_user')) {
            return $conversation->business_id === $user->business_id;
        }
        
        // Regular users can update their own conversations
        return $conversation->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Conversation  $conversation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Conversation $conversation)
    {
        // Only business admins can delete conversations
        if ($user->hasRole('business_admin')) {
            return $conversation->business_id === $user->business_id;
        }
        
        return false;
    }
    
    /**
     * Determine whether the user can send messages in this conversation.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Conversation  $conversation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function sendMessage(User $user, Conversation $conversation)
    {
        // Business users can send messages in their business's conversations
        if ($user->hasRole('business_admin') || $user->hasRole('business_user')) {
            return $conversation->business_id === $user->business_id;
        }
        
        // Regular users can send messages in their own conversations
        return $conversation->user_id === $user->id;
    }
}
