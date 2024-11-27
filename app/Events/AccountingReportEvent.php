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

class AccountingReportEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    protected $percentage;
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
            new PrivateChannel('accounting-report.' . $this->user->user_id),
        ];
    }
    public function broadcastWith(): array
    {
        if(($this->percentage > 99) && ($this->progress['isDone'] === false)){
            $this->percentage = 99;
            $this->progress['info'] = 'Saving Report Pls wait...';
        }
        return [
            'id' => $this->user->user_id,
            'reportId' => $this->reportId,
            'data' => $this->progress,
            'percentage' => $this->percentage,
        ];
    }
}
