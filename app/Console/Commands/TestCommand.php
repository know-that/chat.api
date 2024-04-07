<?php

namespace App\Console\Commands;

use App\Enums\RelationEnum;
use App\Models\User\UserModel;
use App\Services\TestService;
use App\Services\Upload\QiNiuKoDo;
use App\Services\Upload\UploadService;
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
        dd(UploadService::getInstance()->credentials());
    }
}
