<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;

class RolesAndPermissionSeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Role and User';

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
     * @return int
     */
    public function handle(): int
    {
        $roles = ['super-admin', 'user'];
        foreach ($roles as $role) {
            if (!Role::where('name', $role)->exists()) {
                Role::create(['name' => $role,]);
            }
        }

        if (!User::where('email', 'admin@admin.com')->exists()) {
            $this->info('Creating User with Super Admin Role');
            $user = User::create([
                'first_name' => 'admin',
                'last_name' => 'admin',
                'email' => 'admin@admin.com',
                'password' => bcrypt('password'),
            ]);
            $user->assignRole('super-admin');
            $this->info('email: admin@admin.com password: password');
        }

        $users = User::all();
        foreach ($users as $user) {

            // if user is not super admin, assign user role
            if (Arr::first($user->getRoleNames()->toArray()) != 'super-admin') { {
                    $user->syncRoles(['user']);
                }
            }
        }
        $roles = Role::all();
        foreach ($roles as $role) {
            if ($role->name != 'super-admin' && $role->name != 'user') {
                $role->delete();
            }
        }
        return 0;
    }
}
