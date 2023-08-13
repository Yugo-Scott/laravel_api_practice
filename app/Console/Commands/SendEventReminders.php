<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Notifications\EventReminderNotification;
use Illuminate\Support\Str;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-event-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send event reminders to attendees';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $events = Event::with('attendees.user')
            ->where('start_time', '>', now())
            ->where('start_time', '<', now()->addDay())
            ->get();
        $eventCount = $events->count();
        $eventLabel = Str::plural('event', $eventCount);
        $this->info("Sending event reminders for {$eventCount} {$eventLabel}...");
        $events->each(fn($event)=> $event->attendees->each(fn($attendee) 
        =>  $this->info("Sending reminder to {$attendee->user->name} for {$event->name}")));
        
        $events->each(fn($event)=> $event->attendees->each(fn($attendee) 
        => $attendee->user->notify(new EventReminderNotification($event))));
        // $this->info('Sending event reminders...');
    }
}
