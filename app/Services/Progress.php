<?php

namespace App\Services;

use App\Events\AccountingReportEvent;
use App\Models\User;

class Progress
{
    protected $progress;
    protected $reportId;
    public function __construct(){
        $this->reportId = now()->toImmutable()->toISOString();
        $this->progress = [
            'name' => 'Accounting Report',
            'progress' => [
                'currentRow' => 0,
                'totalRow' => 0,
            ],
            'info' => ""
        ];
    }

    private function broadcastProgress(User $user, string $info)
    {
        $this->progress['info'] = $info;
        $this->progress['progress']['currentRow']++;
        AccountingReportEvent::dispatch($user, $this->progress, $this->reportId);
    }

}