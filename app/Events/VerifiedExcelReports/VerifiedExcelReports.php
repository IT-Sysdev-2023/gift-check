<?php

namespace App\Events\VerifiedExcelReports;

use App\Helpers\NumberHelper;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VerifiedExcelReports implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $percentage;
    /**
     * Create a new event instance.
     */
    public function __construct(protected string $message, protected int $currentRow, protected int $totalRows, protected User $user, protected bool $isFinish = false)
    {
        $this->percentage = NumberHelper::percentage($currentRow, $totalRows);

    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('generate-verified-excel.' . $this->user->user_id),
        ];
    }

    public function broadcastAs()
    {
        return 'generate-ver-excel';
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'percentage' => $this->percentage,
            'currentRow' => $this->currentRow,
            'totalRows' => $this->totalRows,
            'isFinish' => $this->isFinish,
        ];
    }
}
