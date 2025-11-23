<?php

namespace App\Events;

use App\Models\CourseMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CourseChatMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public CourseMessage $message
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel('course.' . $this->message->course_id);
    }

    public function broadcastAs(): string
    {
        return 'chat.message';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'message' => $this->message->message,
            'student' => [
                'id' => $this->message->student->id,
                'name' => $this->message->student->name,
                'avatar_small' => $this->message->student->avatar_small,
            ],
            'created_at' => $this->message->created_at->toIso8601String(),
        ];
    }
}
