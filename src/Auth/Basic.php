<?php

namespace Sanpi\Http\Auth;

use Symfony\Component\HttpFoundation\HeaderBag;

class Basic implements Auth
{
    public function hasAuthorization(HeaderBag $headers)
    {
        return ($headers->get('Authorization') !== null);
    }

    public function accept(HeaderBag $headers)
    {
        $authorization = $headers->get('Authorization');
        return (stripos($authorization, 'Basic ') === 0);
    }

    public function getAuthorization($method, $uri, HeaderBag $headers, $username, $password)
    {
        return 'Basic ' . base64_encode("$username:$password");
    }

    public function getChallenge($realm, $qop = 'auth-int')
    {
        return sprintf('Basic realm="%s"', $realm);
    }

    public function authenticate($method, HeaderBag $headers, $username, $password)
    {
        return (
            $headers->get('PHP_AUTH_USER') === $username
            && $headers->get('PHP_AUTH_PW') === $password
        );
    }
}
