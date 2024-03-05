<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Password Reset Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */

    'reset' => trans('Your password has been reset!'),
    'sent' => trans('We have emailed your password reset link. Please click on the link to reset your password.'),
    'throttled' => trans('Please wait before retrying.'),
    'token' => trans('Sorry, the token is invalid'),
    'user' => trans("We can't find a user with that email address."),

];