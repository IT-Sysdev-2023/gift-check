<?php

namespace App\Services\Documents;

use App\Models\BudgetRequest;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;



class UploadFileHandler
{

    protected string $folderName = '';
    private $disk;
    public function __construct()
    {
        $this->disk = Storage::disk('public');
    }

    private function folder()
    {
        return "$this->folderName/";
    }

    protected function handleUpload(Request $request)
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

        return $request->document;
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

    protected function saveMultiFiles(Request $request, $id)
    {
        
        if ($request->hasFile('file')) {
            foreach ($request->file as $image) {
                $name = $this->getOriginalFileName($request, $image);
                $path = $this->disk->putFileAs($this->folder(), $image, $name);

                Document::create([
                    'doc_trid' => $id,
                    'doc_type' => 'Special External GC Request',
                    'doc_fullpath' => $path
                ]);
            }
        }
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

    protected function updateTable(Request $request, BudgetRequest $id, string $filename)
    {
        $res = $id->update([
            'br_requested_by' => $request->updatedById,
            'br_request' => $request->budget,
            'br_remarks' => $request->remarks,
            'br_requested_needed' => $request->dateNeeded,
            'br_file_docno' => $filename,
            'br_group' => $request->group ?? 0,
        ]);

        if ($res) {
            session()->flash('success', 'Update Successfully');
        } else {
            session()->flash('error', 'Something went wrong while updating..');
        }
    }

    public function download(string $file)
    {
        if ($this->disk->exists($this->folder() . $file)) {
            return $this->disk->download($this->folder() . $file);
        } else {
            return redirect()->back()->with('error', 'File Not Found');
        }
    }
    public function getOriginalFileName(Request $request, $image)
    {
        $filename = $this->createFileName($request);

        $originalName = $image->getClientOriginalName();
        $nameWithoutExtension = pathinfo($originalName, PATHINFO_FILENAME);

        //remove special Unicode character (\u{202F})
        $cleanedFilename = preg_replace('/[^\x20-\x7E]/', '', $nameWithoutExtension);
        $name = Str::replace(' ', '-', $cleanedFilename);

        return "{$name}-{$filename}";
    }
}