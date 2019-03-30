<?php

namespace App\Repositories;

use App\User;

class UserRepository
{
    public function getItemByEmail(string $email)
    {
        return User::where('email', strtolower($email))->first();
    }

    public function getItemByHash(string $hash, string $type)
    {
        return User::whereHas('hashes', function($q) use($hash, $type){
            $q->where('hash', $hash);
            $q->where('type', $type);
        })->first();
    }

    public function itemCreate(array $data)
    {
        return User::create($data);
    }

    public function itemUpdate(User $user, array $data)
    {
        $user->fill($data);
        $user->save();
        return $user;
    }

    /**Start User Profile */
    public function saveProfile(User $user, array $data)
    {
        $user->profile ? $user->profile()->update($data) : $user->profile()->create($data);
        return $user;
    }
    /**End User Profile */

    /**Start User Hashes */
    public function addRandomHash(User $user, string $type, $expired_at = null)
    {
        $hash = $user->hashes()->firstOrNew([
            'type' => $type,
        ]);

        if ($hash->exists) {
            $user->hashes()->detach($hash->id);
        }

        $hash->hash = md5(str_random(10));
        $hash->expired_at = $expired_at;
        $user->hashes()->save($hash);

        return $hash->hash;
    }

    public function deleteUserHash(User $user, string $hash):void
    {
        $modelHash = $user->hashes()->where('hash', $hash)->first();
        $modelHash->delete();
    }
    /**End User Hashes */

    /**Start Customer Request */
    public function createCustomerRequest(User $user, array $data)
    {
        return $user->customerRequests()->create($data);
    }
    /**End Customer Request */

    /**Start Team Members*/
    public function attachMembers(User $user, array $pivotData)
    {
        return $user->members()->attach($pivotData);
    }
    /**End Team Members*/

}
