<?php

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Support\Str;

final class GenerateUsernameAction
{
    public function execute(string $name): string
    {
        $base = Str::slug($name, '_');
        $base = Str::limit($base, 20, '');

        if (! User::where('username', $base)->exists()) {
            return $base;
        }

        $candidate = $base.'_'.rand(100, 9999);
        while (User::where('username', $candidate)->exists()) {
            $candidate = $base.'_'.rand(100, 9999);
        }

        return $candidate;
    }
}
