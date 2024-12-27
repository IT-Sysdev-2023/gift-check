<?php

namespace App;

use App\Models\StoreLocalServer;
use Illuminate\Support\Facades\DB;

class DatabaseConnectionService
{
    /**
     * Create a new class instance.
     */

    protected string $host;
    protected string $database;
    protected string $username;
    protected string $password;

    public function __construct()
    {
        //
    }

    public function setHost(){

    }
    public function getConnection($host, $database, $username, $password)
    {
        config(['database.connections.server_connection' => [
            'driver' => 'mariadb',
            'host' => $host,
            'port' =>  '3306',
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
        ]]);

        return DB::connection('server_connection');
    }

    protected function localServer(string|int $store)
    {
        $lserver = StoreLocalServer::where('stlocser_storeid', $store)
            ->first(['stlocser_ip', 'stlocser_username', 'stlocser_password']);

        return $this->getConnection($lserver->stlocser_ip, 'gc_local', $lserver->stlocser_username, $lserver->stlocser_password);
    }

    public function getLocalConnection(bool $isLocal, $store){
        return $isLocal ? DB::connection('mariadb') : $this->localServer($store);
    }


}
