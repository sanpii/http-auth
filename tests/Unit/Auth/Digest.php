<?php

namespace Test\Unit\Sanpi\Http\Auth;

class Digest extends \atoum
{
    private $auth;

    public function beforeTestMethod($testMethod)
    {
        $className = $this->getTestedClassName();
        $this->auth = new $className();
    }

    public function testInit()
    {
        $this->object($this->auth)
            ->isInstanceOf('Sanpi\Http\Auth');
    }

    public function testNoHasAuthorization()
    {
        $this->boolean($this->auth->hasAuthorization([]))
            ->isFalse();
    }

    public function testHasAuthorization()
    {
        $this->boolean($this->auth->hasAuthorization(['HTTP_AUTHORIZATION' => '']))
            ->isTrue();
    }

    public function testGetAuthorization()
    {
        $authorization = $this->auth->getAuthorization([
            'WWW-Authenticate' => 'Digest realm="testrealm@host.com", qop="auth-int", nonce="dcd98b7102dd2f0e8b11d0f600bfb0c093", opaque="5ccc069c403ebaf9f0171e9517f40e41"',
            'method' => 'GET',
            'uri' => '/dir/index.html',
        ], 'Mufasa', 'Circle Of Life');

        $this->string($authorization)
            ->match('#Digest username="Mufasa", realm="testrealm@host.com", nonce="dcd98b7102dd2f0e8b11d0f600bfb0c093", uri="/dir/index.html", qop="auth-int", nc="00000001", cnonce="[\w\d]+", response="[\w\d]+", opaque="5ccc069c403ebaf9f0171e9517f40e41"#');
    }

    public function testGetChallenge()
    {
        $challenge = $this->auth->getChallenge('testrealm@host.com');

        $this->string($challenge)
            ->match('#Digest realm="testrealm@host.com", qop="auth-int", nonce="[\w\d]+", opaque="[\w\d]+"#');
    }

    public function testAuthenticate()
    {
        $authorization = $this->auth->authenticate(
            [
                'REQUEST_METHOD' => 'GET',
                'HTTP_AUTHORIZATION' => 'Digest username="Mufasa", realm="testrealm@host.com", nonce="dcd98b7102dd2f0e8b11d0f600bfb0c093", uri="/dir/index.html", qop="auth-int", nc="00000001", cnonce="53f49857c5ff8", response="7cc6e7cb66974d51b1595d7d81065150", opaque="5ccc069c403ebaf9f0171e9517f40e41"'
            ],
            'Mufasa', 'Circle Of Life'
        );
        $this->boolean($authorization)
            ->isTrue();
    }

    public function testInvalidAuthenticate()
    {
        $authorization = $this->auth->authenticate(
            [
                'REQUEST_METHOD' => 'GET',
                'HTTP_AUTHORIZATION' => 'Digest username="Mufasa", realm="testrealm@host.com", nonce="dcd98b7102dd2f0e8b11d0f600bfb0c093", uri="/dir/index.html", qop="auth-int", nc="00000001", cnonce="53f49857c5ff8", response="7cc6e7cb66974d51b1595d7d81065150", opaque="5ccc069c403ebaf9f0171e9517f40e41"'
            ],
            'Mufasa', 'xxx'
        );
        $this->boolean($authorization)
            ->isFalse();
    }
}
