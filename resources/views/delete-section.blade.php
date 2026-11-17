{{--
    Livewire requires a single root element, so the modal outlet must live inside it.

    Placement: the Settings page renders each card as a keyed block. We use Alpine's
    x-teleport.prepend to insert our card right BEFORE the "Notifications" block (i.e. after
    "Reinstall Server"). Livewire resolves events from teleported nodes back to this component.
    If that block can't be found (different panel version / hidden), we fall back to rendering
    the card in place (bottom of the page) so it never disappears.
--}}
<div
  x-data="{
        hasTarget: document.querySelector(String.raw`[wire\:partial='schema-component::form.notifications::data::section']`) !== null,
    }">
  <template x-if="! hasTarget">
    <div class="mt-6">
      @include('delete-server::card', ['action' => $this->deleteAction])
    </div>
  </template>

  <template x-teleport.prepend='[wire\:partial="schema-component::form.notifications::data::section"]'>
    <div class="fi-grid-col" style="--col-span-default: 1 / -1;">
      @include('delete-server::card', ['action' => $this->deleteAction])
    </div>
  </template>

  <x-filament-actions::modals />
</div>