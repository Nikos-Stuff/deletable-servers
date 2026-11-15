# Delete Server Plugin for Pelican Panel

Adds a **Delete Server** section to the server Settings page (below Reinstall / Notifications).

By Niko — MIT licensed.

## Rules

The section is only rendered when **all** of these are true:

1. The viewer is the **server owner** or a **root admin**.
2. The server is on the **free tier**: the latest row in the external `hosting` database's
   `orders` table with `pelican_server_id = servers.id` has `subplan = 'free'` (case-insensitive).

The free-tier check **fails closed**: no order row, a different plan, or an unreachable database all
mean the section is hidden. It is checked again, uncached, right before deletion is executed.

Deletion requires typing the exact server name and uses the panel's own `ServerDeletionService`.

## Requirements

The same `.env` keys the Niko's Stuff theme uses:

```
DB_HOSTING_HOST=
DB_HOSTING_PORT=3306
DB_HOSTING_DATABASE=hosting_orders
DB_HOSTING_USERNAME=
DB_HOSTING_PASSWORD=
```

The plugin defines the `hosting` connection itself only if nothing else (e.g. the theme) already has.
The DB user only needs `SELECT` on `orders`.

## Installation

1. Upload the provided zip as-is. The root folder must be named `delete-server` (it has to match the
   plugin id in `plugin.json`).
2. Admin panel → **Plugins → Import**, upload the zip, then **Install**.
3. Make sure your queue worker is running.

Or copy the folder to `/var/www/pelican/plugins/delete-server/` and run `php artisan p:plugin:install delete-server`.

## Notes

- Deleting a server does **not** modify the `orders` row (the plugin only reads from the hosting DB).
- If the Wings node is unreachable, deletion fails with an error notification.
- Strings live in `lang/en/strings.php` (`delete-server::strings.*`).
