<?php
namespace App\Console\Commands;

use App\Models\StoreVerification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ProcessFiles extends Command
{
    protected $signature = 'files:process';
    protected $description = 'Find barcode with .BAK extension to redeem GC';

    private $directory = '\\\172.16.42.143\Gift\\';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        $user = 'Kenjey';
        $password = 'ken';

        exec('net use \\\\172.16.42.143\Gift /user:' . $user . ' ' . $password . ' /persistent:no');

        $files = collect(File::allFiles($this->directory));

        $bakFiles = $files->filter(function ($file) {
            return $file->getExtension() === 'bak';
        });

        while (true) {
            foreach ($bakFiles as $key => $file) {
                $pathInfo = pathinfo($file->getFilename());
                $baseNameWithoutBak = str_replace('.igc', '', $pathInfo['filename']);

               $barcode = StoreVerification::where('vs_barcode', $baseNameWithoutBak)->get();

               foreach($barcode as $key => $value){
                    StoreVerification::where('vs_barcode',$value->vs_barcode)
                    ->update(['vs_tf_used' => '*']);
               }
            }
            sleep(1);
        }
    }


}
