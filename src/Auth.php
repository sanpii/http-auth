<?php

namespace Sanpi\Http;

interface Auth
{
    public function hasAuthorization($request);
    public function getAuthorization($request, $username, $password);
    public function getChallenge($realm, $qop = 'auth-int');
    public function authenticate($request, $username, $password);
}
