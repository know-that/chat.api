<?php

namespace App\Console\Commands;

use App\Enums\RelationEnum;
use App\Models\User\UserModel;
use App\Services\TestService;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'a test';

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
     */
    public function handle(): void
    {

        dd(pathinfo("QQ\u5f55\u5c4f20240317224859.mp4"));
        // $command = 'bf.add users 2';
        // dd(Redis::client()->rawCommand(...explode(' ', $command)));

        dd(Redis::client()->rawCommand('bf.info', 'users'));

        dd(RelationEnum::ChatSingle->name);
        dump(Auth::login(UserModel::find(1)));
        dump(Auth::login(UserModel::find(2)));
    }
}
