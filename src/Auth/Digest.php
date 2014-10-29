<?php

namespace Sanpi\Http\Auth;

use Symfony\Component\HttpFoundation\HeaderBag;

class Digest implements Auth
{
    public function hasAuthorization(HeaderBag $headers)
    {
        return ($headers->get('Authorization') !== null);
    }

    public function getAuthorization($method, $uri, HeaderBag $headers, $username, $password)
    {
        $authRequest = $this->unserialize($headers->get('WWW-Authenticate'));
        $data = $this->hash(
            $username,
            $password,
            $authRequest,
            $method,
            $uri
        );
        return $this->serialize($data);
    }

    public function accept(HeaderBag $headers)
    {
        $authorization = $headers->get('Authorization');
        return (stripos($authorization, 'Digest ') === 0);
    }

    public function getChallenge($realm, $qop = 'auth-int')
    {
        return sprintf(
            'Digest realm="%s", qop="%s", nonce="%s", opaque="%s"',
            $realm, $qop, uniqid(), md5($realm)
        );
    }

    public function authenticate($method, HeaderBag $headers, $username, $password)
    {
        $data = $this->unserialize($headers->get('Authorization'));
        $A1 = md5("$username:{$data['realm']}:$password");
        $A2 = md5("$method:{$data['uri']}");
        $response = md5($A1 . ':' . $data['nonce'] . ':' . $data['nc'] . ':' . $data['cnonce'] . ':' . $data['qop'] . ':' . $A2);

        return ($response === $data['response']);
    }

    private function unserialize($digest)
    {
        $data = [];
        $regex = '@(?:^Digest )?(?P<key>[^=]*)=(?:([\'"]?)(?P<value>[^\2]+?)\2)(?:, |$)@';

        preg_match_all($regex, $digest, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $data[$match['key']] = $match['value'];
        }

        return $data;
    }

    private function hash($username, $password, $data, $method, $uri)
    {
        $cnonce = uniqid();
        $nc = '00000001';
        $A1 = md5($username . ':' . $data['realm'] . ':' . $password);
        $A2 = md5("$method:$uri");
        $response = md5($A1 . ':' . $data['nonce'] . ':' . $nc . ':' . $cnonce . ':' . $data['qop'] . ':' . $A2);

        return [
            'username' => $username,
            'realm' => $data['realm'],
            'nonce' => $data['nonce'],
            'uri' => $uri,
            'qop' => $data['qop'],
            'nc' => $nc,
            'cnonce' => $cnonce,
            'response' => $response,
            'opaque' => $data['opaque'],
        ];
    }

    private function serialize($data)
    {
        $txt = "Digest ";

        foreach($data as $key => $value) {
            $txt .= "$key=\"$value\", ";
        }

        return substr($txt, 0, -2);
    }
}
