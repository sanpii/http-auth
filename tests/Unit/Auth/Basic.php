<?php

namespace Test\Unit\Sanpi\Http\Auth;

use Symfony\Component\HttpFoundation\HeaderBag;

class Basic extends \atoum
{
    const VALID_AUTHORIZATION = 'Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ==';

    private $auth;

    public function beforeTestMethod($testMethod)
    {
        $className = $this->getTestedClassName();
        $this->auth = new $className();
    }

    public function testInit()
    {
        $this->object($this->auth)
            ->isInstanceOf('\Sanpi\Http\Auth\Auth');
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
            'Authorization' => Digest::VALID_AUTHORIZATION,
        ]);

        $this->boolean($this->auth->accept($headers))
            ->isFalse();
    }

    public function testGetAuthorization()
    {
        $headers = new HeaderBag();
        $authorization = $this->auth->getAuthorization('GET', '/dir/index.html', $headers, 'Aladdin', 'open sesame');

        $this->string($authorization)
            ->isIdenticalTo(self::VALID_AUTHORIZATION);
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
