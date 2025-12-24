<?php

namespace Database\Seeders;

use Spatie\LaravelSettings\SettingsMigrator;

class SettingsSeeder extends Seeder
{
    public function run(SettingsMigrator $migrator): void
    {
        $migrator->add('general.site_name', 'Project Manager');
        $migrator->add('general.enable_registration', true);
        $migrator->add('general.site_logo', null);
        $migrator->add('general.enable_social_login', false);
        $migrator->add('general.site_language', 'vi');
        $migrator->add('general.default_role', 'user');
        $migrator->add('general.enable_login_form', true);
        $migrator->add('general.enable_oidc_login', false);
    }
}


