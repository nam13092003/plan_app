<?php

// use Spatie\LaravelSettings\Migrations\SettingsMigration;

// class AddSiteLanguageToGeneralSettings extends SettingsMigration
// {
//     public function up(): void
//     {
//         $this->migrator->add('general.site_language', config('app.fallback_locale'));
//     }
// }

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->update(
            'general.site_language',
            fn ($old) => $old ?? 'vi'
        );
    }
};
