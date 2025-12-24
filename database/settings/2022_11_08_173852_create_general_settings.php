<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('general.site_name', 'Project Manager');
        $this->migrator->add('general.enable_registration', true);
        $this->migrator->add('general.site_logo', null);
        $this->migrator->add('general.enable_social_login', false);
        $this->migrator->add('general.site_language', 'vi');
        $this->migrator->add('general.default_role', 'user');
        $this->migrator->add('general.enable_login_form', true);
        $this->migrator->add('general.enable_oidc_login', false);
    }
};
