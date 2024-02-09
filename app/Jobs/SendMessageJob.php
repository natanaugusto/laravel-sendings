<?php

namespace App\Jobs;

use App\Mail\SendMessage;
use App\Models\Contact;
use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
        public Message $message,
        public int $offset = 0
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $contacts = Contact::skip($this->offset)
            ->take(
                config('excel.exports.chunk_size')
            )->get();
        foreach ($contacts as $contact) {
            $this->offset++;
            Mail::to($contact->email)->queue(new SendMessage($this->user, $this->message, $contact));
        }
        self::dispatch($this->user, $this->message, $this->offset);
    }
}
