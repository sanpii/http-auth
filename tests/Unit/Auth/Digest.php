<?php

namespace Test\Unit\Sanpi\Http\Auth;

use Symfony\Component\HttpFoundation\HeaderBag;

class Digest extends \atoum
{
    const VALID_AUTHORIZATION = 'Digest username="Mufasa", realm="testrealm@host.com", nonce="dcd98b7102dd2f0e8b11d0f600bfb0c093", uri="/dir/index.html", qop="auth-int", nc="00000001", cnonce="53f49857c5ff8", response="7cc6e7cb66974d51b1595d7d81065150", opaque="5ccc069c403ebaf9f0171e9517f40e41"';

    private $auth;

    public function beforeTestMethod($testMethod)
    {
        $className = $this->getTestedClassName();
        $this->auth = new $className();
    }

    public function testInit()
    {
        $this->object($this->auth)
            ->isInstanceOf('Sanpi\Http\Auth\Auth');
    }

    public function testNoHasAuthorization()
    {
        $headers = new HeaderBag();

        $this->boolean($this->auth->hasAuthorization($headers))
            ->isFalse();
    }

    public function testHasAuthorization()
    {
        $headers = new HeaderBag([
            'Authorization' => self::VALID_AUTHORIZATION,
        ]);

        $this->boolean($this->auth->hasAuthorization($headers))
            ->isTrue();
    }

    public function testAccept()
    {
        $headers = new HeaderBag([
            'Authorization' => self::VALID_AUTHORIZATION,
        ]);

        $this->boolean($this->auth->accept($headers))
            ->isTrue();
    }

    public function testDontAccept()
    {
        $headers = new HeaderBag([
            'Authorization' => Basic::VALID_AUTHORIZATION,
        ]);

        $this->boolean($this->auth->accept($headers))
            ->isFalse();
    }

    public function testGetAuthorization()
    {
        $headers = new HeaderBag([
            'WWW-Authenticate' => self::VALID_AUTHORIZATION,
        ]);
        $authorization = $this->auth->getAuthorization('GET', '/dir/index.html', $headers, 'Mufasa', 'Circle Of Life');

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
        $headers = new HeaderBag([
            'Authorization' => self::VALID_AUTHORIZATION,
        ]);
        $authorization = $this->auth->authenticate('GET', $headers, 'Mufasa', 'Circle Of Life');

        $this->boolean($authorization)
            ->isTrue();
    }

    public function testInvalidAuthenticate()
    {
        $headers = new HeaderBag([
            'Authorization' => self::VALID_AUTHORIZATION,
        ]);
        $authorization = $this->auth->authenticate('GET', $headers, 'Mufasa', 'xxx');

        $this->boolean($authorization)
            ->isFalse();
    }
}
