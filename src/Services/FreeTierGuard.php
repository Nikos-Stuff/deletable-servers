<?php

namespace NikosStuff\DeleteServerPlugin\Services;

use App\Models\Server;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Decides whether a server is on the free tier by looking at its order in the
 * external `hosting` database (orders.pelican_server_id === servers.id, orders.subplan === 'free').
 *
 * Fails closed: if the database is unreachable or there is no order, the server is NOT free.
 */
class FreeTierGuard
{
    public const CONNECTION = 'hosting';

    public const TABLE = 'orders';

    public const FREE_PLAN = 'free';

    /**
     * @param  bool  $fresh  Bypass the short display cache (use this before destructive actions).
     */
    public static function isFree(Server $server, bool $fresh = false): bool
    {
        $key = "delete-server.free.{$server->id}";

        if (! $fresh && ($cached = Cache::get($key)) !== null) {
            return (bool) $cached;
        }

        try {
            $order = DB::connection(self::CONNECTION)
                ->table(self::TABLE)
                ->where('pelican_server_id', $server->id)
                ->orderByDesc('id')
                ->first();
        } catch (Throwable $exception) {
            Log::warning('delete-server: could not read hosting order, treating server as not free.', [
                'server_id' => $server->id,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }

        $isFree = $order !== null
            && strtolower(trim((string) ($order->subplan ?? ''))) === self::FREE_PLAN;

        Cache::put($key, $isFree, now()->addMinute());

        return $isFree;
    }
}
