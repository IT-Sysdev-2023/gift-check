<?php

namespace App\Services\Documents;

use App\Models\BudgetRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;


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

    protected function saveMultiFile(Request $request)
    {
        if ($request->hasFile('file')) {
            foreach ($request->file as $image) {
                $name = $this->getOriginalFileName($request, $image);
                $this->disk->putFileAs($this->folder(), $image, $name);
            }
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
    private function getOriginalFileName(Request $request, $image)
    {
        $filename = $this->createFileName($request);

        $originalName = $image->getClientOriginalName();
        $nameWithoutExtension = pathinfo($originalName, PATHINFO_FILENAME);
        return "{$nameWithoutExtension}-{$filename}";
    }
}