<?php

namespace App\Services\Documents;

use App\Models\BudgetRequest;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;



class FileHandler
{

    protected string $folderName = '';
    protected $disk;
    public function __construct()
    {
        $this->disk = Storage::disk('public');
    }
    protected function folder()
    {
        return Str::finish($this->folderName, '/');
    }

    protected function replaceFile(Request $request)
    {
        if ($request->hasFile('file')) {

            if (!is_null($request->document)) {
                //delete old image
                $this->disk->delete($this->folder() . $request->document);
            }
            //insert new image
            $filename = $this->createFileName($request);
            $this->saveFile($request, $filename);

            return $filename;
        }

        return $request->document ?? '';
    }
    protected function destroyFile(Request $request, $file)
    {
        //delete old image
        if ($request->hasFile('file')) {
            return $this->disk->delete($this->folder() . $file);
        }
    }

    protected function createFileName(Request $request)
    {
        if ($request->hasFile('file')) {
            return "{$request->user()->user_id}-" . now()->format('Y-m-d-His') . ".jpg";
        }
        return '';
    }

    protected function saveFile(Request $request, string $filename)
    {
        if ($request->hasFile('file')) {
            return $this->disk->putFileAs($this->folder(), $request->file, $filename);
        }
    }

    protected function saveMultiFiles(Request $request, $id, callable $callback)
    {


        if ($request->hasFile('file')) {
            foreach ($request->file as $image) {
                $name = $this->getOriginalFileName($request, $image);
                $path = $this->disk->putFileAs($this->folder(), $image, $name);

                $callback($id, $path, $image, $name);
            }
        }
    }
    protected function savePdfFile(Request $request, string|int $identifier, $pdf, $date = null)
    {
        $date = $date ?: now()->format('Y-m-d-His');
        $filename = "{$request->user()->user_id}-{$identifier}-" . $date . ".pdf";
        return $this->disk->put("{$this->folder()}{$filename}", $pdf);
    }
    protected function retrieveFile(string $folder, string $filename)
    {
        $file = "{$folder}/{$filename}";
        if ($this->disk->exists($file)) {
            $pdf = $this->disk->get($file);
            return response($pdf, 200)->header('Content-Type', 'application/pdf');
        } else {
            return response()->json('File Not Found on the Server', 404);
        }
    }
    public function getFilesFromDirectory(?string $subfolder = null, bool $includeSubdirectory = false)
    {
        $trim = Str::finish($this->folder() . $subfolder, '/');
        $path = $subfolder ? $trim : $this->folder();

        if($includeSubdirectory){
            return collect($this->disk->allFiles($path));
        }

        return collect($this->disk->files($path));
    }
    public function download(string $file, ?string $subfolder = null)
    {
        $filename = Str::start($file, '/');
        $fullpath = $this->folder() . $subfolder . $filename;
        if ($this->disk->exists($fullpath)) {
            return $this->disk->download($fullpath);
        } else {
            return response()->json(['error' => 'File Not Found'], 404);
        }
    }

    public function getOriginalFileName(Request $request, $image)
    {
        $filename = $this->createFileName($request);
        // dd($request->all());
        $originalName = $image->getClientOriginalName();
        $nameWithoutExtension = pathinfo($originalName, PATHINFO_FILENAME);

        //remove special Unicode character (\u{202F})
        $cleanedFilename = preg_replace('/[^\x20-\x7E]/', '', $nameWithoutExtension);
        $name = Str::replace(' ', '-', $cleanedFilename);

        return "{$name}-{$filename}";
    }
}
