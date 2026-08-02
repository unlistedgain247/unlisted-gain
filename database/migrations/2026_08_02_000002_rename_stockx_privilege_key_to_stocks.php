<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * 'stockx' was a typo for 'stocks' in the privilege.unlisted JSON blob.
     * Renaming the key in code alone would silently revoke access for any
     * user who already has it granted, so migrate the stored data too.
     */
    public function up(): void
    {
        $this->renameKey('stockx', 'stocks');
    }

    public function down(): void
    {
        $this->renameKey('stocks', 'stockx');
    }

    private function renameKey(string $from, string $to): void
    {
        DB::table('users')->whereNotNull('privilege')->orderBy('uid')->chunkById(100, function ($users) use ($from, $to) {
            foreach ($users as $user) {
                $privilege = is_string($user->privilege) ? json_decode($user->privilege, true) : $user->privilege;

                if (!is_array($privilege) || !array_key_exists($from, $privilege['unlisted'] ?? [])) {
                    continue;
                }

                $privilege['unlisted'][$to] = $privilege['unlisted'][$from];
                unset($privilege['unlisted'][$from]);

                DB::table('users')->where('uid', $user->uid)->update([
                    'privilege' => json_encode($privilege),
                ]);
            }
        }, 'uid');
    }
};
