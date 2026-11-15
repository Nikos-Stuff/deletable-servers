<?php

namespace NikosStuff\DeleteServerPlugin;

use App\Filament\Server\Pages\Settings;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

class DeleteServerPlugin implements Plugin
{
    public function getId(): string
    {
        return 'delete-server';
    }

    public function register(Panel $panel): void
    {
        if ($panel->getId() !== 'server') {
            return;
        }

        // Inject the delete section after the main form content on the Settings page.
        // The Livewire component decides on its own whether anything is rendered.
        $panel->renderHook(
            PanelsRenderHook::PAGE_FOOTER_WIDGETS_BEFORE,
            fn (): string => Blade::render('@livewire(\'delete-server-section\')'),
            scopes: Settings::class,
        );
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
