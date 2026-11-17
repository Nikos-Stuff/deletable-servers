<div
  wire:ignore.self
  x-data="{
        place() {
            const target = document.querySelector('[wire\\:partial=\\'schema-component::form.notifications::data::section\\']');
            const card = this.$refs.card;

            if (!card) return;

            if (target && target.parentNode) {
                target.parentNode.insertBefore(card, target);
            }

            card.style.display = '';
        }
    }"
  x-init="$nextTick(() => place())"
  @livewire:update.window="$nextTick(() => place())">
  <div x-ref="card" style="display: none;" class="fi-grid-col" style="--col-span-default: 1 / -1;">
    <x-filament::section>
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
  </div>

  <x-filament-actions::modals />
</div>