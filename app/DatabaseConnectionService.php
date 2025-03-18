<?php

namespace App;

use App\Models\StoreLocalServer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseConnectionService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function setHost()
    {

    }

    public static function getConnection($host, $database, $username, $password)
    {
        config([
            'database.connections.server_connection' => [
                'driver' => 'mariadb',
                'host' => $host,
                'port' => '3306',
                'database' => $database,
                'username' => $username,
                'password' => $password,
                'unix_socket' => env('DB_SOCKET', ''),
                'charset' => env('DB_CHARSET', 'utf8mb4'),
                'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
                'prefix' => '',
                'prefix_indexes' => true,
                'strict' => true,
                'engine' => null
            ]
        ]);

        return DB::connection('server_connection');
    }

    public static function remoteServer(string|int $store)
    {
        $storeMapping = [
            1 => 'gc',
            2 => 'gc_local',
            3 => 'gc',
            4 => 'gc',
            5 => 'gc',
            6 => 'gc',
            7 => 'gc',
            8 => 'gc',
            9 => 'gc',
            10 => 'gc',
            11 => 'gc',
            12 => 'gc'
        ];
        if (!isset($storeMapping[$store])) {
            throw new \Exception("Invalid store ID: $store");
        }
        if ($store == 6 || $store == 7) {
            throw new \Exception("No server configuration found for store ID: $store");
        }

        $lserver = StoreLocalServer::where('stlocser_storeid', $store)
            ->first(['stlocser_ip', 'stlocser_username', 'stlocser_password']);


        if (!$lserver) {
            throw new \Exception("No server configuration found for store ID: $store");
        }
        $database = $storeMapping[$store] ?? 'gc_local';
        return self::getConnection($lserver->stlocser_ip, $database, $lserver->stlocser_username, $lserver->stlocser_password);

    }
    public static function getLocalConnection(bool $isLocal, $store)
    {
        return $isLocal ? DB::connection('mariadb') : self::remoteServer($store);
    }

    // BELOW IS THE ORIGINAL SETUP FOR THE DATABASECONNECTION SERVER

     // public static function remoteServer(string|int $store)
    // {
    //     $lserver = StoreLocalServer::where('stlocser_storeid', $store)
    //         ->first(['stlocser_ip', 'stlocser_username', 'stlocser_password']);

    //     return self::getConnection($lserver->stlocser_ip, 'gc_local', $lserver->stlocser_username, $lserver->stlocser_password);
    // }

}
