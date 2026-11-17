<?php

namespace NikosStuff\DeleteServerPlugin\Livewire;

use App\Models\Server;
use App\Services\Servers\ServerDeletionService;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use NikosStuff\DeleteServerPlugin\Services\FreeTierGuard;

class DeleteServerSection extends Component implements HasActions
{
  use InteractsWithActions;

  #[Locked]
  public int $serverId = 0;

  public function mount(): void
  {
    $tenant = Filament::getTenant();
    $this->serverId = $tenant instanceof Server ? $tenant->id : 0;
  }

  public function deleteAction(): Action
  {
    return Action::make('delete')
      ->label(trans('delete-server::strings.action.label'))
      ->color('danger')
      ->icon('heroicon-o-trash')
      ->requiresConfirmation()
      ->modalHeading(trans('delete-server::strings.action.modal_heading'))
      ->modalDescription(trans('delete-server::strings.action.modal_description'))
      ->modalSubmitActionLabel(trans('delete-server::strings.action.submit'))
      ->modalAlignment(Alignment::Center)
      ->schema([
        TextInput::make('confirm_name')
          ->label(trans('delete-server::strings.action.confirm_label'))
          ->required(),
      ])
      ->action(function (array $data, ServerDeletionService $deletionService) {
        $server = $this->getServer();

        if (! $server || ! $this->canDelete(fresh: true)) {
          Notification::make()
            ->title(trans('delete-server::strings.notifications.not_allowed_title'))
            ->body(trans('delete-server::strings.notifications.not_allowed_body'))
            ->danger()
            ->send();
          return;
        }

        if (($data['confirm_name'] ?? '') !== $server->name) {
          Notification::make()
            ->title(trans('delete-server::strings.action.name_mismatch'))
            ->danger()
            ->send();
          return;
        }

        try {
          $deletionService->handle($server);
        } catch (Exception $exception) {
          report($exception);
          Notification::make()
            ->title(trans('delete-server::strings.notifications.failed_title'))
            ->body($exception->getMessage())
            ->danger()
            ->send();
          return;
        }

        Notification::make()
          ->title(trans('delete-server::strings.notifications.deleted_title'))
          ->body(trans('delete-server::strings.notifications.deleted_body'))
          ->success()
          ->send();

        $this->redirect($this->serverListUrl());
      });
  }

  public function canDelete(bool $fresh = false): bool
  {
    $server = $this->getServer();
    $user = auth()->user();

    if (! $server || ! $user) {
      return false;
    }

    if (! ($user->isRootAdmin() || $user->id === $server->owner_id)) {
      return false;
    }

    return FreeTierGuard::isFree($server, $fresh);
  }

  public function render(): View
  {
    return view($this->canDelete() ? 'delete-server::delete-section' : 'delete-server::empty');
  }

  private function getServer(): ?Server
  {
    return $this->serverId > 0 ? Server::find($this->serverId) : null;
  }

  private function serverListUrl(): string
  {
    try {
      return Filament::getPanel('app')->getUrl();
    } catch (Exception) {
      return url('/');
    }
  }
}
