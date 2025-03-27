<?php

namespace App\Console\Commands;

use App\Mail\DefaultMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestSendEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email} {--queue= : Type of queue - default, mail, notification}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send test email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Using a string instead of an array for message to match with DefaultMail
        $email = new DefaultMail('Test Email', 'This is a test email');

        if ($this->option('queue')) {
            Mail::to($this->argument('email'))
                ->queue(
                    $email->onQueue($this->option('queue'))
                );
        } else {
            Mail::to($this->argument('email'))->send($email);
        }
        
        $this->info('Test email has been sent to ' . $this->argument('email'));
    }
}
