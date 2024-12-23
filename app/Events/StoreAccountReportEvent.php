<?php

namespace App\Events;

use App\Helpers\NumberHelper;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StoreAccountReportEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $percentage;
    /**
     * Create a new event instance.
     */
    public function __construct(public User $user, protected $progress, protected string $reportId)
    {
        $this->percentage = NumberHelper::percentage($progress['progress']['currentRow'], $progress['progress']['totalRow']);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('storeaccounting-report.' . $this->user->user_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->user->user_id,
            'reportId' => $this->reportId,
            'data' => $this->progress,
            'percentage' => $this->percentage,
        ];
    }
}
