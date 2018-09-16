<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\User;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Log;
use App\Mail\WelcomeMail;

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
    public function handle()
    {
        try {
            Log::info('Start send email.');
            Mailer::to($this->user->email)->send(new WelcomeMail($this->user));
            Log::info('End send email.');
        } catch (\Exception $e) {
            Log::info('Could not send email for system error.');
            report($e);
        }
    }
}
