<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class PosInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:install
        {--admin_email=admin@pos.local}
        {--admin_password=admin123}
        {--admin_name=Administrator}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize app: 
                            key,
                            migrate,
                            seed,
                            admin user
                            storage link
                        ';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if(empty(config('app.key')))
        {
            Artisan::call('key:generate', ['--force'=>true]);
        }

        Artisan::call('migrate', 
                    ['--force'=>true]
                );
        Artisan::call('db:seed', 
                    ['--class'=> \Database\Seeders\DatabaseSeeder::class], 
                    ['--force'=>true]
                );

        $email = (string)$this->option('admin_email');
        $pass  = (string)$this->option('admin_password');
        $name  = (string)$this->option('admin_name');

        $u = User::firstOrCreate(
                ['email' => $email],
                ['name' => $name,
                  'password' => Hash::make($pass),
                  'role' => 'admin'
                ]
            );
        
        $u->forceFill(['role' => 'admin', 'password'=> Hash::make($pass)])->save();

        @Artisan::call('storage:link');
        Artisan::call('optimize:clear');
        
        @file_put_contents(storage_path('installed'), now()->toDateTimeString());

        $this->info("Installed. Admin: {$email} / {$pass}");

        return self::SUCCESS;

    }

    
}
