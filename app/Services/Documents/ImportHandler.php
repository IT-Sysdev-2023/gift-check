<?php
namespace App\Services\Documents;

use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use App\DashboardRoutesTrait;

class ImportHandler extends FileHandler
{
    use DashboardRoutesTrait;
    public function __construct()
    {
        parent::__construct();
    }

    public function setFolder(string $folder)
    {
        $this->folderName = $folder;
        return $this;
    }

    public function downloadFile(string $file, bool $isFullPath = false)
    {

        if ($isFullPath) {

            $fullpath = $file;
        } else {
            $filename = Str::start($file, '/');
            $fullpath = $this->folder() . $filename;
        }

        if ($this->disk->exists($fullpath)) {
            return $this->disk->download($fullpath);
        } else {
            return response()->json(['error' => 'File Not Found'], 404);
        }
    }

    public function getFileReports($userType)
    {
        $getFiles = $this
            ->setFolder('Reports')
            ->getFilesFromDirectory($this->roleDashboardRoutes[$userType]);

        return $getFiles->transform(function ($item) {
            $fileInfo = pathinfo($item);
            $extension = $fileInfo['extension'];

            $timestamp = substr($item, strrpos($item, '-') + 1);
            $generatedAt = Date::createFromTimestamp($timestamp);

            return [
                'file' => $item,
                'filename' => Str::of(basename($item))->basename('.' . $extension),
                'extension' => $extension,
                'date' => $generatedAt->toDayDateTimeString(), // for Sorting
                'icon' => $extension === 'pdf' ? 'pdf.png' : 'excel.png',
                'generatedAt' => $generatedAt->diffForHumans(),
                'expiration' => $generatedAt->addDays(2)->diffForHumans(),
            ];
        })->sortByDesc('date')->values();
    }
}