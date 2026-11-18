{{--
    Livewire requires a single root element, so the modal outlet must live inside it.

    Placement: the Settings page renders each card as a keyed block. We use Alpine's
    x-teleport.prepend to insert our card right BEFORE the "Notifications" block (i.e. after
    "Reinstall Server"). Livewire resolves events from teleported nodes back to this component.
    If that block can't be found (different panel version / hidden), we fall back to rendering
    the card in place (bottom of the page) so it never disappears.

    Keep-alive: a Livewire `morph.removing` hook skips removal of our card, so normally it is
    never dropped at all.

    Self-heal (fallback): when the Settings form re-renders (toggling a switch, editing an input, ...) the
    panel's DOM morph throws away nodes it doesn't know about, including our teleported card.
    A MutationObserver notices that the card is gone (or no longer sits right before the
    Notifications block) and re-creates it by toggling `placed`.
--}}
<div
    x-data="{
        sel: String.raw`[wire\:partial='schema-component::form.notifications::data::section']`,
        hasTarget: false,
        placed: true,
        healing: false,
        observer: null,
        frame: null,

        init() {
            this.hasTarget = document.querySelector(this.sel) !== null;

            if (! this.hasTarget) {
                return;
            }

            // Primary fix: tell Livewire's DOM morph to leave our card alone, so it is never
            // removed (no flicker / layout jump). Registered once per page load; it only
            // ever matches our own card. The observer below is the fallback.
            if (! window.__deleteServerKeepCard && window.Livewire && typeof window.Livewire.hook === 'function') {
                window.__deleteServerKeepCard = true;

                try {
                    window.Livewire.hook('morph.removing', ({ el, skip }) => {
                        if (el && el.hasAttribute && el.hasAttribute('data-delete-server-card') && typeof skip === 'function') {
                            skip();
                        }
                    });
                } catch (e) {
                    window.__deleteServerKeepCard = false;
                }
            }

            this.observer = new MutationObserver(() => {
                if (this.frame) {
                    cancelAnimationFrame(this.frame);
                }

                this.frame = requestAnimationFrame(() => this.heal());
            });

            this.observer.observe(document.body, { childList: true, subtree: true });
        },

        heal() {
            if (this.healing) {
                return;
            }

            const target = document.querySelector(this.sel);

            if (! target) {
                return;
            }

            const card = document.querySelector('[data-delete-server-card]');

            // Still correctly placed right before the Notifications block: nothing to do.
            if (card && card.nextElementSibling === target) {
                return;
            }

            this.healing = true;
            this.placed = false;

            this.$nextTick(() => {
                this.placed = true;

                this.$nextTick(() => {
                    this.healing = false;
                });
            });
        },

        destroy() {
            this.observer?.disconnect();

            if (this.frame) {
                cancelAnimationFrame(this.frame);
            }
        },
    }"
>
    <template x-if="! hasTarget">
        <div class="mt-6">
            @include('delete-server::card', ['action' => $this->deleteAction])
        </div>
    </template>

    <template x-if="hasTarget && placed">
        <template x-teleport.prepend='[wire\:partial="schema-component::form.notifications::data::section"]'>
            <div class="fi-grid-col" data-delete-server-card style="--col-span-default: 1 / -1;">
                @include('delete-server::card', ['action' => $this->deleteAction])
            </div>
        </template>
    </template>

    <x-filament-actions::modals />
</div>
