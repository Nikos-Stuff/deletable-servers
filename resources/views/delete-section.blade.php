<div
  x-data="{
        moveCard() {
            const card = document.querySelector('[data-delete-card]');
            if (! card) return;

            const notificationsItem = document.querySelector(
                `[wire\\:partial='${CSS.escape('schema-component::form.notifications::data::section')}']`
            );

            if (notificationsItem && notificationsItem.parentElement) {
                // Insert as a sibling right BEFORE the Notifications card (i.e. AFTER Reinstall Server)
                if (notificationsItem.previousElementSibling !== card) {
                    notificationsItem.before(card);
                }
            }
        },
        init() {
            this.$nextTick(() => this.moveCard());

            const registerHooks = () => {
                Livewire.hook('morphed', () => {
                    queueMicrotask(() => this.moveCard());
                });

                Livewire.hook('request', ({ succeed }) => {
                    succeed(() => {
                        this.$nextTick(() => this.moveCard());
                    });
                });
            };

            if (window.Livewire) {
                registerHooks();
            } else {
                document.addEventListener('livewire:initialized', registerHooks, { once: true });
            }

            document.addEventListener('livewire:navigated', () => {
                this.$nextTick(() => this.moveCard());
            });
        }
    }"
  x-init="init()">
  <div data-delete-card wire:key="delete-server-card-movable" class="fi-grid-col" style="--col-span-default: 1 / -1;">
    @include('delete-server::card', ['action' => $this->deleteAction])
  </div>

  <x-filament-actions::modals />
</div>