{{--
    Livewire requires a single root element, so the modal outlet must live inside it.

    Placement: the Settings page renders each card as a keyed block. We render the card
    inside this component (so Livewire owns it and actions keep working), then Alpine
    moves the DOM node into place right BEFORE the "Notifications" section (after
    "Reinstall Server").

    Why move instead of x-teleport? Teleported content lives outside the morphing
    component and gets wiped whenever the parent form morphs (e.g. flipping a Toggle).
    By moving our own node and re-moving it after every Livewire morph, the card
    survives and stays in the correct position.
--}}
<div
    x-data="{
        targetSelector: String.raw`[wire\\:partial='schema-component::form.notifications::data::section']`,
        moveCard() {
            const target = document.querySelector(this.targetSelector);
            const card = this.$el.querySelector('[data-delete-card]');
            if (! card) return;

            if (target && ! target.contains(card)) {
                // Prepend as first child of the Notifications section
                target.prepend(card);
            }
            // If no target, leave the card where it is (fallback at bottom).
        },
        init() {
            this.$nextTick(() => this.moveCard());

            // Re-place after any Livewire morph (parent form updates, etc.)
            const hook = () => {
                Livewire.hook('request', ({ succeed }) => {
                    succeed(() => {
                        Alpine.nextTick(() => this.moveCard());
                    });
                });
            };

            if (window.Livewire) {
                hook();
            } else {
                document.addEventListener('livewire:initialized', hook, { once: true });
            }
        }
    }"
    x-init="init()"
>
    {{-- Always render the card here so Livewire manages it. Alpine moves it when possible. --}}
    <div data-delete-card class="fi-grid-col mt-6" style="--col-span-default: 1 / -1;">
        @include('delete-server::card', ['action' => $this->deleteAction])
    </div>

    <x-filament-actions::modals />
</div>
