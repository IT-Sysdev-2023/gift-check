<?php

namespace App\Broadcasting;

use App\Models\User;
use Illuminate\Support\Facades\Http;
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
        $hrms = Http::get('http://172.16.161.34/api/hrms/filter/employee/name', [
            'q' => strtolower($user->lastname . ', ' . $user->firstname)
        ])->json();


        $usertype = $this->transaction($user->usertype);
        $storeName = $this->transactionStore($user->store_assigned);

        return [
            'id' => $user->user_id,
            'name' => $user->full_name,
            'aname' => strtolower($user->lastname . ', ' . $user->firstname),
            'usertype' => $usertype,
            'image' => $hrms['data']['employee'][0]['employee_photo'] ?? null,
            'username' => $user->username,
            'storeAssigned' => $storeName
        ];
    }
    public static function transactionStore($type)
    {

        $trans = [
            '1' => 'Alturas Mall',
            '2' => 'Alturas Talibon',
            '3' => 'Island City Mall',
            '4' => 'Plaza Marcela',
            '6' => 'Alturas Tubigon',
            '7' => 'Colonade Mandaue',
            '8' => 'Alta Citta',
            '9' => 'Farmers Market',
            '10' => 'Ubay Distribution Center',
            '11' => 'Screenville',
            '12' => 'Asc Tech',
        ];

        return $trans[$type] ?? 'Corporate Office';
    }

    public static function transaction($type)
    {
        $trans = [
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
