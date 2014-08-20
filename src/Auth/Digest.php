<?php

namespace Sanpi\Http\Auth;

use Sanpi\Http\Auth;

class Digest implements Auth
{
    public function getAuthorization($request, $username, $password)
    {
        $authRequest = $this->unserialize($request['WWW-Authenticate']);
        $data = $this->hash(
            $username,
            $password,
            $authRequest,
            $request['method'],
            $request['uri']
        );
        return $this->serialize($data);
    }

    public function authenticate($request, $username, $password)
    {
        $data = $this->unserialize($request['HTTP_AUTHORIZATION']);
        $A1 = md5("$username:{$data['realm']}:$password");
        $A2 = md5($request['REQUEST_METHOD'] . ':' . $data['uri']);
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
