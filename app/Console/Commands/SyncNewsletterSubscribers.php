<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Models\User;
use App\Services\ListmonkService;
use Illuminate\Console\Command;

class SyncNewsletterSubscribers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'famer:newsletter-sync
                            {--limit=500 : Maximum subscribers to sync per run}
                            {--force : Re-sync even subscribers that already have a listmonk_subscriber_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync FAMER users and restaurant owners to Listmonk newsletter lists';

    public function handle(ListmonkService $listmonk): int
    {
        $limit = (int) $this->option('limit');
        $force = (bool) $this->option('force');

        $this->info('FAMER Newsletter Sync — Starting');
        $this->newLine();

        // Resolve list IDs (use config values, fall back to name-based lookup)
        $usersListId  = (int) config('services.listmonk.list_users_id', 0);
        $ownersListId = (int) config('services.listmonk.list_owners_id', 0);

        if ($usersListId === 0) {
            $this->line('  Resolving "FAMER Usuarios" list…');
            $usersListId = $listmonk->getOrCreateList('FAMER Usuarios', 'public');
        }

        if ($ownersListId === 0) {
            $this->line('  Resolving "FAMER Propietarios" list…');
            $ownersListId = $listmonk->getOrCreateList('FAMER Propietarios', 'private');
        }

        if ($usersListId === 0 || $ownersListId === 0) {
            $this->error('Could not resolve Listmonk list IDs. Check LISTMONK_* env vars and connectivity.');
            return self::FAILURE;
        }

        $this->line("  Users list ID  : {$usersListId}");
        $this->line("  Owners list ID : {$ownersListId}");
        $this->newLine();

        // ── Users ─────────────────────────────────────────────────────────────
        $this->info('▶ Syncing users (newsletter_subscribed = true)…');

        $usersQuery = User::query()
            ->where('newsletter_subscribed', true)
            ->whereNotNull('email')
            ->when(!$force, fn ($q) => $q->whereNull('listmonk_subscriber_id'))
            ->limit($limit);

        $userTotal   = (clone $usersQuery)->count();
        $userSynced  = 0;
        $userFailed  = 0;

        $progressUsers = $this->output->createProgressBar($userTotal);
        $progressUsers->start();

        $usersQuery->each(function (User $user) use (
            $listmonk, $usersListId, &$userSynced, &$userFailed, $progressUsers
        ) {
            $ok = $listmonk->subscribe(
                email    : $user->email,
                name     : $user->name ?? $user->email,
                listIds  : [$usersListId],
                attribs  : [
                    'source'   => 'famer_platform',
                    'user_id'  => $user->id,
                    'role'     => $user->role ?? 'user',
                ],
            );

            if ($ok) {
                // Best-effort: store Listmonk subscriber ID for future reference
                // We skip a heavy lookup here for speed — ID can be backfilled later
                $user->updateQuietly(['newsletter_subscribed_at' => $user->newsletter_subscribed_at ?? now()]);
                $userSynced++;
            } else {
                $userFailed++;
            }

            $progressUsers->advance();
        });

        $progressUsers->finish();
        $this->newLine();
        $this->line("  Synced: {$userSynced} | Failed: {$userFailed} | Total: {$userTotal}");
        $this->newLine();

        // ── Restaurant owners ─────────────────────────────────────────────────
        $this->info('▶ Syncing restaurant owners (owner_newsletter = true)…');

        $ownersQuery = Restaurant::query()
            ->where('owner_newsletter', true)
            ->whereNotNull('owner_email')
            ->limit($limit);

        $ownerTotal  = (clone $ownersQuery)->count();
        $ownerSynced = 0;
        $ownerFailed = 0;

        $progressOwners = $this->output->createProgressBar($ownerTotal);
        $progressOwners->start();

        $ownersQuery->each(function (Restaurant $restaurant) use (
            $listmonk, $ownersListId, $usersListId, &$ownerSynced, &$ownerFailed, $progressOwners
        ) {
            $ok = $listmonk->subscribe(
                email    : $restaurant->owner_email,
                name     : $restaurant->owner_name ?? $restaurant->name,
                listIds  : [$ownersListId, $usersListId],  // owners also get general list
                attribs  : [
                    'source'        => 'famer_owner',
                    'restaurant_id' => $restaurant->id,
                    'restaurant'    => $restaurant->name,
                    'city'          => $restaurant->city ?? '',
                    'state'         => $restaurant->state ?? '',
                ],
            );

            if ($ok) {
                $ownerSynced++;
            } else {
                $ownerFailed++;
            }

            $progressOwners->advance();
        });

        $progressOwners->finish();
        $this->newLine();
        $this->line("  Synced: {$ownerSynced} | Failed: {$ownerFailed} | Total: {$ownerTotal}");
        $this->newLine();

        // ── Summary ───────────────────────────────────────────────────────────
        $totalSynced = $userSynced + $ownerSynced;
        $totalFailed = $userFailed + $ownerFailed;

        $this->table(
            ['Category', 'Synced', 'Failed', 'Total'],
            [
                ['Users',   $userSynced,  $userFailed,  $userTotal],
                ['Owners',  $ownerSynced, $ownerFailed, $ownerTotal],
                ['TOTAL',   $totalSynced, $totalFailed, $userTotal + $ownerTotal],
            ]
        );

        if ($totalFailed > 0) {
            $this->warn("  {$totalFailed} subscribers failed — check logs/laravel.log for details.");
        }

        $this->info('Newsletter sync complete.');

        return $totalFailed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
