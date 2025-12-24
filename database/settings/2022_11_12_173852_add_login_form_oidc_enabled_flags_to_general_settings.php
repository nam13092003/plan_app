<?php

// use Spatie\LaravelSettings\Migrations\SettingsMigration;

// class AddLoginFormOidcEnabledFlagsToGeneralSettings extends SettingsMigration
// {
//     public function up(): void
//     {
//         $this->migrator->add('general.enable_login_form', config('system.login_form.is_enabled'));
//         $this->migrator->add('general.enable_oidc_login', config('services.oidc.is_enabled'));
//     }
// }

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->update(
            'general.enable_login_form',
            fn ($old) => $old ?? true
        );

        $this->migrator->update(
            'general.enable_oidc_login',
            fn ($old) => $old ?? false
        );
    }
};
