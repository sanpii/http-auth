<?php

namespace Sanpi\Http\Auth;

use Sanpi\Http\Auth;

class Basic implements Auth
{
    public function hasAuthorization($request)
    {
        return isset($request['HTTP_AUTHORIZATION']);
    }

    public function getAuthorization($request, $username, $password)
    {
        return 'Basic ' . base64_encode("$username:$password");
    }

    public function getChallenge($realm, $qop = 'auth-int')
    {
        return sprintf('Basic realm="%s"', $realm);
    }

    public function authenticate($request, $username, $password)
    {
        return (
            $request['PHP_AUTH_USER'] === $username
            && $request['PHP_AUTH_PW'] === $password
        );
    }
}
