<?php

namespace Sanpi\Http;

use Symfony\Component\HttpFoundation\HeaderBag;

interface Auth
{
    public function hasAuthorization(HeaderBag $headers);
    public function accept(HeaderBag $headers);
    public function getAuthorization($method, $uri, HeaderBag $headers, $username, $password);
    public function getChallenge($realm, $qop = 'auth-int');
    public function authenticate($method, HeaderBag $headers, $username, $password);
}
