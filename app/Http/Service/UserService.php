<?php

namespace App\Http\Service;

use App\Models\User;

class UserService
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * return registered user object
     *
     * @method store
     *
     * @param array
     *
     * @return Illuminate\Database\Eloquent\Collection  [\App\Models\User]
     *
     */
    public function store(array $user): object
    {
        return $this->user->create($user);
    }
}
