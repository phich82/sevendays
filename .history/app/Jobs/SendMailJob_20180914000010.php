<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\User;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Total of times for job trying again
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Total of seconds for job executing before timeout
     *
     * @var int
     */
    public $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Mailer $mailer)
    {
        try {
            $mailer->raw('This is test message.', function (Message $message) {
                $message->from('jhphich82@gmail.com')
                        ->to('nguyenphat82@gmail.com')
                        ->subject('Test sendmail');
            });
        } catch (\Exception $e) {
            report($e);
        }
    }
}
