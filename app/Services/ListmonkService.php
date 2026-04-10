<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Listmonk Newsletter Integration Service
 *
 * Add to config/services.php:
 * 'listmonk' => [
 *     'url'           => env('LISTMONK_URL', 'http://localhost:9000'),
 *     'username'      => env('LISTMONK_USERNAME', 'admin'),
 *     'password'      => env('LISTMONK_PASSWORD'),
 *     'list_users_id' => env('LISTMONK_LIST_USERS_ID', 1),
 *     'list_owners_id'=> env('LISTMONK_LIST_OWNERS_ID', 2),
 * ],
 *
 * Add to .env:
 * LISTMONK_URL=http://localhost:9000
 * LISTMONK_USERNAME=listmonk
 * LISTMONK_PASSWORD=your_password
 * LISTMONK_LIST_USERS_ID=1
 * LISTMONK_LIST_OWNERS_ID=2
 */
class ListmonkService
{
    protected string $baseUrl;
    protected string $username;
    protected string $password;

    public function __construct()
    {
        $this->baseUrl  = rtrim(config('services.listmonk.url', 'http://localhost:9000'), '/');
        $this->username = config('services.listmonk.username', 'admin');
        $this->password = config('services.listmonk.password', '');
    }

    /**
     * Build authenticated HTTP client.
     */
    protected function client(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withBasicAuth($this->username, $this->password)
            ->acceptJson()
            ->timeout(15);
    }

    /**
     * Subscribe an email to Listmonk.
     * If the subscriber already exists (409), update their lists instead.
     *
     * @param  string  $email
     * @param  string  $name
     * @param  int[]   $listIds
     * @param  array   $attribs   Custom attributes stored on the subscriber profile
     * @return bool
     */
    public function subscribe(string $email, string $name, array $listIds = [], array $attribs = []): bool
    {
        try {
            $response = $this->client()->post("{$this->baseUrl}/api/subscribers", [
                'email'   => $email,
                'name'    => $name,
                'status'  => 'enabled',
                'lists'   => $listIds,
                'attribs' => (object) $attribs,
            ]);

            if ($response->successful()) {
                return true;
            }

            // 409 = subscriber already exists — add them to the requested lists
            if ($response->status() === 409) {
                $id = $this->findSubscriberIdByEmail($email);
                if ($id && !empty($listIds)) {
                    return $this->addToLists($id, $listIds);
                }
                return $id !== null;
            }

            Log::warning('ListmonkService::subscribe failed', [
                'email'  => $email,
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return false;
        } catch (\Throwable $e) {
            Log::error('ListmonkService::subscribe exception', [
                'email'   => $email,
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Unsubscribe (delete) a subscriber by email.
     *
     * @param  string $email
     * @return bool
     */
    public function unsubscribe(string $email): bool
    {
        try {
            $id = $this->findSubscriberIdByEmail($email);
            if ($id === null) {
                return true; // already gone
            }

            $response = $this->client()->delete("{$this->baseUrl}/api/subscribers/{$id}");

            if ($response->successful()) {
                return true;
            }

            Log::warning('ListmonkService::unsubscribe failed', [
                'email'  => $email,
                'id'     => $id,
                'status' => $response->status(),
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::error('ListmonkService::unsubscribe exception', [
                'email'   => $email,
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Update subscriber data by email (name, attribs, status, etc.).
     *
     * @param  string $email
     * @param  array  $data   Any subset of: name, status, attribs, lists
     * @return bool
     */
    public function updateSubscriber(string $email, array $data): bool
    {
        try {
            $id = $this->findSubscriberIdByEmail($email);
            if ($id === null) {
                return false;
            }

            $response = $this->client()->put("{$this->baseUrl}/api/subscribers/{$id}", $data);

            if ($response->successful()) {
                return true;
            }

            Log::warning('ListmonkService::updateSubscriber failed', [
                'email'  => $email,
                'status' => $response->status(),
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::error('ListmonkService::updateSubscriber exception', [
                'email'   => $email,
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Retrieve all mailing lists from Listmonk.
     *
     * @return array
     */
    public function getLists(): array
    {
        try {
            $response = $this->client()->get("{$this->baseUrl}/api/lists", [
                'per_page' => 500,
            ]);

            if ($response->successful()) {
                return $response->json('data.results', []);
            }

            Log::warning('ListmonkService::getLists failed', ['status' => $response->status()]);
            return [];
        } catch (\Throwable $e) {
            Log::error('ListmonkService::getLists exception', ['message' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Find a list by name or create it if it doesn't exist.
     *
     * @param  string $name
     * @param  string $type  'public' | 'private' | 'optinDouble' | 'optinSingle'
     * @return int   List ID
     */
    public function getOrCreateList(string $name, string $type = 'public'): int
    {
        $lists = $this->getLists();

        foreach ($lists as $list) {
            if (strcasecmp($list['name'], $name) === 0) {
                return (int) $list['id'];
            }
        }

        // List not found — create it
        try {
            $response = $this->client()->post("{$this->baseUrl}/api/lists", [
                'name'          => $name,
                'type'          => $type,
                'optin'         => 'single',
                'tags'          => ['famer'],
                'description'   => "Auto-created by FAMER platform",
            ]);

            if ($response->successful()) {
                return (int) $response->json('data.id', 0);
            }

            Log::warning('ListmonkService::getOrCreateList failed to create', [
                'name'   => $name,
                'status' => $response->status(),
            ]);
            return 0;
        } catch (\Throwable $e) {
            Log::error('ListmonkService::getOrCreateList exception', ['message' => $e->getMessage()]);
            return 0;
        }
    }

    /**
     * Add a subscriber (by email) to a single list.
     *
     * @param  string $email
     * @param  int    $listId
     * @return bool
     */
    public function addToList(string $email, int $listId): bool
    {
        $id = $this->findSubscriberIdByEmail($email);
        if ($id === null) {
            return false;
        }
        return $this->addToLists($id, [$listId]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Internal helpers
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Look up a subscriber's numeric ID by email address.
     * Returns null when not found.
     */
    protected function findSubscriberIdByEmail(string $email): ?int
    {
        try {
            $response = $this->client()->get("{$this->baseUrl}/api/subscribers", [
                'query'    => "subscribers.email = '{$email}'",
                'per_page' => 1,
            ]);

            if ($response->successful()) {
                $results = $response->json('data.results', []);
                if (!empty($results)) {
                    return (int) $results[0]['id'];
                }
            }
        } catch (\Throwable $e) {
            Log::error('ListmonkService::findSubscriberIdByEmail exception', [
                'email'   => $email,
                'message' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Add a subscriber (by internal ID) to multiple lists.
     */
    protected function addToLists(int $subscriberId, array $listIds): bool
    {
        try {
            $response = $this->client()->put("{$this->baseUrl}/api/subscribers/lists", [
                'ids'    => [$subscriberId],
                'action' => 'add',
                'status' => 'confirmed',
                'target_list_ids' => $listIds,
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('ListmonkService::addToLists exception', ['message' => $e->getMessage()]);
            return false;
        }
    }
}
