<?php

namespace Test\Unit\Sanpi\Http\Auth;

use Symfony\Component\HttpFoundation\HeaderBag;

class Basic extends \atoum
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
        $headers = new HeaderBag();

        $this->boolean($this->auth->hasAuthorization($headers))
            ->isFalse();
    }

    public function testHasAuthorization()
    {
        $headers = new HeaderBag([
            'Authorization' => ''
        ]);

        $this->boolean($this->auth->hasAuthorization($headers))
            ->isTrue();
    }

    public function testAccept()
    {
        $headers = new HeaderBag([
            'Authorization' => 'Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ==',
        ]);

        $this->boolean($this->auth->accept($headers))
            ->isTrue();
    }

    public function testDontAccept()
    {
        $headers = new HeaderBag([
            'Authorization' => 'Digest username="Mufasa", realm="testrealm@host.com", nonce="dcd98b7102dd2f0e8b11d0f600bfb0c093", uri="/dir/index.html", qop="auth-int", nc="00000001", cnonce="53f49857c5ff8", response="7cc6e7cb66974d51b1595d7d81065150", opaque="5ccc069c403ebaf9f0171e9517f40e41"',
        ]);

        $this->boolean($this->auth->accept($headers))
            ->isFalse();
    }

    public function testGetAuthorization()
    {
        $headers = new HeaderBag();
        $authorization = $this->auth->getAuthorization('GET', '/dir/index.html', $headers, 'Aladdin', 'open sesame');

        $this->string($authorization)
            ->isIdenticalTo('Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ==');
    }

    public function testGetChallenge()
    {
        $challenge = $this->auth->getChallenge('testrealm@host.com');

        $this->string($challenge)
            ->isIdenticalTo('Basic realm="testrealm@host.com"');
    }

    public function testAuthenticate()
    {
        $headers = new HeaderBag([
            'PHP_AUTH_USER' => 'Aladdin',
            'PHP_AUTH_PW' => 'open sesame',
        ]);
        $authorization = $this->auth->authenticate('GET', $headers, 'Aladdin', 'open sesame');
        $this->boolean($authorization)
            ->isTrue();
    }

    public function testInvalidAuthenticate()
    {
        $headers = new HeaderBag([
            'PHP_AUTH_USER' => 'Aladdin',
            'PHP_AUTH_PW' => 'xxx',
        ]);
        $authorization = $this->auth->authenticate('GET', $headers, 'Aladdin', 'open sesame');

        $this->boolean($authorization)
            ->isFalse();
    }
}
