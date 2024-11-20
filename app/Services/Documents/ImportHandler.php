<?php 
namespace App\Services\Documents;

use Illuminate\Support\Str;

class ImportHandler extends FileHandler
{
    public function __construct(){
        parent::__construct();
    }

    public function setFolder(string $folder){
        $this->folderName = $folder;
        return $this;
    }

    public function downloadFile(string $file, bool $isFullPath = false){
        
        if($isFullPath){

            $fullpath = $file;
        }else{
            $filename = Str::start($file, '/');
            $fullpath = $this->folder() . $filename;
        }

        if ($this->disk->exists($fullpath)) {
            return $this->disk->download($fullpath);
        } else {
            return response()->json(['error' => 'File Not Found'], 404);
        }
    }
}