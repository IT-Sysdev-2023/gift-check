<?php

namespace App\Services\Documents;

use App\Jobs\DeleteFile;
use GuzzleHttp\Exception\InvalidArgumentException;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\DashboardRoutesTrait;
class ExportHandler extends FileHandler
{
    use DashboardRoutesTrait;
    private $filename;
    private string $fullPath;
    public function __construct()
    {
        parent::__construct();
    }

    public function setFolder(string $folder)
    {
        $this->folderName = Str::finish($folder, '/');
        return $this;
    }
    public function setFileName($id, $identifier)
    {
        $date = now()->timestamp;
        $this->filename = "{$id}-{$identifier}-" . $date;

        return $this;
    }

    /**
     * Create a subfolder as the userstype position.
     *
     * @param string $userType Usertype should be the usertype of the $this-folderName eg. $request->user()->usertype.
     */
    public function setSubfolderAsUsertype(int $userType)
    {
        $this->folderName = Str::finish($this->folderName . $this->roleDashboardRoutes[$userType], '/');
        return $this;
    }

    public function exportToExcel($model)
    {
        $folderName = $this->folder() . 'excel/';
        Excel::store($model, "{$folderName}{$this->filename}.xlsx", 'public');
        return $this;
    }

    public function exportToPdf($pdf)
    {
        $filename = $this->folder() . $this->filename . '.pdf';
        $this->disk->put($filename, $pdf);
        $this->fullPath = $filename;
        return $this;
    }

    /**
     * Export the given document in the specified format.
     *
     * @param string $format The format in which the document should be exported (e.g., 'pdf', 'xlsx').
     * @param mixed $document The document data or object to be exported (e.g., a return from a generated excel).
     * @param callable|null $callback An optional callback to modify the document after exporting .
     */
    public function exportDocument(string $format, $document, ?callable $callback = null)
    {
        $exportMethods = [
            'pdf' => fn() => $this->exportToPdf($document),
            'excel' => fn() => $this->exportToExcel($document),
        ];
    
        if (!array_key_exists($format, $exportMethods)) {
            throw new InvalidArgumentException("Unsupported format: $format");
        }
    
        // Execute the appropriate export method
        $exportMethods[$format]();
    
        // Invoke the callback if provided
        if ($callback !== null) {
            $callback($format, $document);
        }

    }

    public function deleteFileIn($date)
    {
        DeleteFile::dispatch($this->fullPath)->delay($date);
    }

}