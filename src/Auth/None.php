<?php

namespace Sanpi\Http\Auth;

use Symfony\Component\HttpFoundation\HeaderBag;

class None implements Auth
{
    public function hasAuthorization(HeaderBag $headers)
    {
        return $headers->has('Authorization');
    }

    public function accept(HeaderBag $headers)
    {
        return !$this->hasAuthorization($headers);
    }

    public function getAuthorization($method, $uri, HeaderBag $headers, $username, $password)
    {
        return '';
    }

    public function getChallenge($realm, $qop = 'auth-int')
    {
        return '';
    }

    public function getUsername(HeaderBag $headers)
    {
        return '';
    }

    public function authenticate($method, HeaderBag $headers, $username, $password)
    {
        return true;
    }
}
