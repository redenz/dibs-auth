<?php

namespace Dibs\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use LaraParse\Auth\ParseUserProvider;
use Parse\ParseException;
use Parse\ParseObject;
use Parse\ParseQuery;

class DibsUserProvider extends ParseUserProvider
{
    public function validateCredentials(Authenticatable $user, array $credentials)
    {

        try {
            $user = $this->authenticateUser($user, $credentials);
            $this->authenticateRoles($user);

            return true;
        } catch (ParseException $error) {
            return false;
        }
    }
    private function getUsernameFromCredentials(array $credentials)
    {
        if (array_key_exists('username', $credentials)) {
            return $credentials['username'];
        } elseif (array_key_exists('email', $credentials)) {
            return $credentials['email'];
        } else {
            throw new ParseException('$credentials must contain either a "username" or "email" key');
        }
    }

    private function authenticateUser($user, $credentials)
    {
        $username = $this->getUsernameFromCredentials($credentials);

        /** @var ParseUser $userClass */
        $userClass = ParseObject::getRegisteredSubclass('_User');

        $user = $userClass::logIn($username, $credentials['password']);

        return $user;
    }

    private function authenticateRoles($user)
    {
        // If there are no allowed roles defined we'll just let them through
        if (!env('allowed_roles')) {
            return true;
        }

        if ($user == null) {
            return false;
        }
        $query = new ParseQuery("_Role");
        $query->equalTo('users', new ParseObject("_User", $user->objectId, true));
        $roles = collect($query->find());

        $roles = $roles->map(function ($role) {
            return $role->get('name');
        });

        $allowedRoles = explode(',', env('allowed_roles', ''));
        $metRoles = collect($allowedRoles)->intersect($roles);
        Session::put('dibs.roles', $metRoles);

        if ($metRoles->isEmpty()) {
            Log::info("User roles do not minimum requirement for login", [$user->get('email', $user->objectId)]);
            throw new ParseException("User not allow to access this system");
        }
    }
}
