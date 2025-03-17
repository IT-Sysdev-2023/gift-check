<?php

namespace App\Broadcasting;

use App\Models\User;
use Illuminate\Support\Facades\Storage;


class OnlineUsers
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function join(User $user)
    {
        // dd($user);

        $image = "/storage/users-image/$user->id";

        $usertype = $this->transaction($user->usertype);

        return [
            'id' => $user->user_id,
            'name' => $user->full_name,
            'usertype' => $usertype,
            'image' => $image,
            'username' => $user->username
        ];
    }

    public static function transaction($type)
    {
        $trans =  [
            '1' => 'Admin',
            '2' => 'Treasury',
            '3' => 'Finance',
            '4' => 'Custodian',
            '6' => 'Marketing',
            '7' => 'Retail',
            '8' => 'Retailgroup',
            '9' => 'Accounting',
            '10' => 'Iad',
            '12' => 'Eod',
            '13' => 'Storeaccounting',
        ];

        return $trans[$type] ?? '';
    }
}
