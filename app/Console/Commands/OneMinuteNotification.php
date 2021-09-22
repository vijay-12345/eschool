<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Session;
use App\User;
use App\Notifaction;

class OneMinuteNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'minute:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Every One Minute cron start and give notification';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $objNotification = new Notifaction();
        $objNotification->checkNotification();
        


        $this->info('Minute Notification has been send successfully');
    }
}
