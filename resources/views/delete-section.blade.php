{{--
    Re-executed move after every Livewire morph so the section survives form updates.
--}}
<div
  wire:ignore.self
  x-data="{
        place() {
            const target = document.querySelector(String.raw`[wire\\:partial='schema-component::form.notifications::data::section']`);
            const card = this.$refs.card;

            if (! card) return;

            if (target && target.parentNode) {
                target.parentNode.insertBefore(card, target);
            }

            card.style.display = '';
        }
    }"
  x-init="$nextTick(() => place())"
  @livewire:update.window="$nextTick(() => place())">
  <div x-ref="card" style="display: none;" class="fi-grid-col" style="--col-span-default: 1 / -1;">
    @include('delete-server::card', ['action' => $this->deleteAction])
  </div>

  <x-filament-actions::modals />
</div>