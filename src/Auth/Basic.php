<?php

namespace Sanpi\Http\Auth;

use Sanpi\Http\Auth;

class Basic implements Auth
{
    public function getAuthorization($request, $username, $password)
    {
        return 'Basic ' . base64_encode("$username:$password");
    }

    public function authenticate($request, $username, $password)
    {
        return (
            $request['PHP_AUTH_USER'] === $username
            && $request['PHP_AUTH_PW'] === $password
        );
    }
}
