<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskCreated extends Notification
{
    use Queueable;

    public $task;

    /**
     * Create a new notification instance.
     */
    public function __construct($task)
    {
        $this->task = $task;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'مهمة جديدة',
            'message' => "تم إضافة مهمة جديدة: '{$this->task->title}'",
            'type' => 'task_created',
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'priority' => $this->task->priority,
            'created_by' => auth()->id(),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function broadcastType(): string
    {
        return 'task.created';
    }
}
