{{-- Livewire requires a single root element, so the modal outlet must live inside it. --}}
<div>
    <x-filament::section class="mt-6">
        <x-slot name="heading">
            {{ trans('delete-server::strings.section.heading') }}
        </x-slot>

        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ trans('delete-server::strings.section.description') }}
        </p>

        <p class="mt-2 text-sm font-medium text-danger-600 dark:text-danger-400">
            {{ trans('delete-server::strings.section.warning') }}
        </p>

        <div class="mt-4 flex justify-end">
            {{ $this->deleteAction }}
        </div>
    </x-filament::section>

    <x-filament-actions::modals />
</div>
