<?php

namespace App\Services;

use App\Events\AccountingReportEvent;
use App\Models\User;
use Illuminate\Support\Str;

class Progress
{
    protected $progress;
    protected $reportId;
    protected User $user;
    public function __construct(?User $user = null)
    {
        $this->user = $user;
        $this->reportId = (string) Str::uuid();
        $this->progress = [
            'name' => 'Accounting Report',
            'progress' => [
                'currentRow' => 0,
                'totalRow' => 0,
            ],
            'info' => "",
            'isDone' => false
        ];
    }

    protected function broadcastProgress(User $user, string $info, bool $isDone = false, $id = null)
    {
        $this->progress['info'] = $info;
        $this->progress['progress']['currentRow']++;
        $this->progress['isDone'] = $isDone;
        AccountingReportEvent::dispatch($user, $this->progress, $id ?? $this->reportId);
    }

    protected function broadcast(string $info, $eventClass, bool $isDone = false, $id = null)
    {
        $this->progress['info'] = $info;
        $this->progress['progress']['currentRow']++;
        $this->progress['isDone'] = $isDone;

        if($eventClass){
            $eventClass::dispatch($this->user, $this->progress, $id ?? $this->reportId);
        }
    }

}