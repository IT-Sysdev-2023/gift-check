<?php

namespace App\Services\Documents;

use App\Models\BudgetRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;


class UploadFileHandler
{

    protected string $folderName = '';
    private $disk;
    public function __construct() {
        $this->disk = Storage::disk('public');
    }

    protected function handleUpload(Request $request){
        if ($request->hasFile('file')) {

            if (!is_null($request->document)) {
                //delete old image
                $this->disk->delete($this->folderName . $request->document);
            }
            //insert new image
            $filename = "{$request->user()->user_id}-" . now()->format('Y-m-d-His') . ".jpg";
            $this->disk->putFileAs($this->folderName, $request->file, $filename);

            return $filename;
        }

        return $request->document;
    }

    protected function updateTable(Request $request, BudgetRequest $id, string $filename){
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
		if ($this->disk->exists($this->folderName . $file)) {
			return $this->disk->download($this->folderName . $file);
		} else {
			return redirect()->back()->with('error', 'File Not Found');
		}
	}
}