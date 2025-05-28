<?php

namespace App\Service;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use App\Enums\Permissions;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use \Illuminate\Http\Request;
readonly final class AuthService
{
    public function __construct(readonly private Request $request) {}

    function getCurrentUser() : User
    {
        /** @var User $user */
        return $this->request->attributes->get('user');
    }

    function checkPermissions(Operation $operation) : void {
        if(!isset($operation->getExtraProperties()[Permissions::PERMISSION])) {
            return;
        }

        $permissions = $this->request->attributes->get('permissions');
        $permission = $operation->getExtraProperties()[Permissions::PERMISSION]->value;

        if(!in_array($permission, $permissions)) {
            throw new AuthorizationException();
        }
    }
}
