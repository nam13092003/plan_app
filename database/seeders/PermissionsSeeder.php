<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Settings\GeneralSettings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionsSeeder extends Seeder
{
    private array $modules = [
        'permission', 'project', 'project status', 'role', 'ticket',
        'ticket priority', 'ticket status', 'ticket type', 'user',
        'activity', 'sprint'
    ];

    private array $pluralActions = [
        'List'
    ];

    private array $singularActions = [
        'View', 'Create', 'Update', 'Delete'
    ];

    private array $extraPermissions = [
        'Manage general settings', 'Import from Jira',
        'List timesheet data', 'View timesheet dashboard'
    ];

    private string $defaultRole = 'Default role';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create profiles
        foreach ($this->modules as $module) {
            $plural = Str::plural($module);
            $singular = $module;
            foreach ($this->pluralActions as $action) {
                Permission::firstOrCreate([
                    'name' => $action . ' ' . $plural
                ]);
            }
            foreach ($this->singularActions as $action) {
                Permission::firstOrCreate([
                    'name' => $action . ' ' . $singular
                ]);
            }
        }

        foreach ($this->extraPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission
            ]);
        }

        // Create default role
        $defaultRole = Role::firstOrCreate([
            'name' => $this->defaultRole
        ]);
        $settings = app(GeneralSettings::class);
        $settings->default_role = $defaultRole->id;
        $settings->save();

        // Add all permissions to default role
        $defaultRole->syncPermissions(Permission::all()->pluck('name')->toArray());

        // Create Admin role
        $adminRole = Role::firstOrCreate([
            'name' => 'Admin'
        ]);
        
        // Add all permissions to Admin role
        $adminRole->syncPermissions(Permission::all()->pluck('name')->toArray());

        // Create other standard roles
        Role::firstOrCreate(['name' => 'Manager']);
        Role::firstOrCreate(['name' => 'Developer']);
        Role::firstOrCreate(['name' => 'Customer']);

        // Assign Admin role to john.doe@helper.app user (only this user will be admin)
        $adminUser = User::where('email', 'john.doe@helper.app')->first();
        if ($adminUser) {
            $adminUser->syncRoles([$adminRole]);
        }

        // Assign default role to other users (not the admin user)
        $otherUsers = User::where('email', '!=', 'john.doe@helper.app')->get();
        foreach ($otherUsers as $user) {
            if ($user->roles->isEmpty()) {
                $user->syncRoles([$this->defaultRole]);
            }
        }
    }
}
