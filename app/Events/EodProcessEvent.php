<?php

namespace App\Events;

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

class EodProcessEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $percentage;
    /**
     * Create a new event instance.
     */
    public function __construct(protected string $message, protected int $currentRow, protected int $totalRows, protected User $user)
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
            new PrivateChannel('process-eod.' . $this->user->user_id),
        ];
    }

    public function broadcastAs()
    {
        return 'start-processing-eod';
    }


    public function broadcastWith()
    {

        return [
            'message' => $this->message,
            'percentage' => $this->percentage,
            'currentRow' => $this->currentRow,
            'totalRows' => $this->totalRows,
        ];
    }
}
