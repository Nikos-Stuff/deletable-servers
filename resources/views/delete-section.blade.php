<div
  x-data="{
        placeCard() {
            const card = document.querySelector('[data-delete-card]');
            if (! card) return;

            const notificationsItem = document.querySelector(
                `[wire\\:partial='${CSS.escape('schema-component::form.notifications::data::section')}']`
            );
            const grid = notificationsItem?.parentElement;
            if (! grid || ! notificationsItem) return;

            // Ensure a non-Livewire anchor sits right before Notifications
            let anchor = grid.querySelector('[data-delete-card-anchor]');
            if (! anchor) {
                anchor = document.createElement('span');
                anchor.setAttribute('data-delete-card-anchor', '');
                anchor.style.display = 'none';
            }

            if (notificationsItem.previousElementSibling !== anchor) {
                notificationsItem.before(anchor);
            }

            // Place the card right after the anchor (so it sits between Reinstall Server & Notifications)
            if (anchor.nextElementSibling !== card) {
                anchor.after(card);
            }
        },
        init() {
            this.$nextTick(() => this.placeCard());

            const bindLivewire = () => {
                Livewire.hook('morphed', ({ el, component }) => {
                    this.placeCard();
                    requestAnimationFrame(() => this.placeCard());
                });

                Livewire.hook('request', ({ succeed }) => {
                    succeed(() => {
                        this.$nextTick(() => this.placeCard());
                        requestAnimationFrame(() => this.placeCard());
                    });
                });
            };

            if (window.Livewire) {
                bindLivewire();
            } else {
                document.addEventListener('livewire:initialized', bindLivewire, { once: true });
            }

            // Catch any DOM removal inside the form grid during morphs
            const observer = new MutationObserver(() => {
                const card = document.querySelector('[data-delete-card]');
                const anchor = document.querySelector('[data-delete-card-anchor]');
                if (card && anchor && anchor.nextElementSibling !== card) {
                    this.placeCard();
                }
            });

            this.$nextTick(() => {
                const notificationsItem = document.querySelector(
                    `[wire\\:partial='${CSS.escape('schema-component::form.notifications::data::section')}']`
                );
                if (notificationsItem?.parentElement) {
                    observer.observe(notificationsItem.parentElement, { childList: true });
                }
            });
        }
    }"
  x-init="init()">
  <div data-delete-card wire:ignore.self class="fi-grid-col" style="--col-span-default: 1 / -1;">
    @include('delete-server::card', ['action' => $this->deleteAction])
  </div>

  <x-filament-actions::modals />
</div>