<?php

declare(strict_types=1);

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
     * Return newly registered user object
     *
     * @method store
     *
     * @param array
     *
     * @return App\Models\User
     *
     */
    public function store(array $user): ?User
    {
        return $this->user->create($user);
    }
}
