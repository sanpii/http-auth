<?php

namespace Test\Unit\Sanpi\Http\Auth;

use Symfony\Component\HttpFoundation\HeaderBag;

class None extends \atoum
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
            'Authorization' => Basic::VALID_AUTHORIZATION,
        ]);

        $this->boolean($this->auth->hasAuthorization($headers))
            ->isTrue();
    }

    public function testAccept()
    {
        $headers = new HeaderBag();

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
        $headers = new HeaderBag();
        $authorization = $this->auth->getAuthorization('GET', '/dir/index.html', $headers, 'Aladdin', 'open sesame');

        $this->string($authorization)
            ->isIdenticalTo('');
    }

    public function testGetChallenge()
    {
        $challenge = $this->auth->getChallenge('testrealm@host.com');

        $this->string($challenge)
            ->isIdenticalTo('');
    }

    public function testAuthenticate()
    {
        $headers = new HeaderBag();
        $authorization = $this->auth->authenticate('GET', $headers, 'Aladdin', 'open sesame');
        $this->boolean($authorization)
            ->isTrue();
    }

}
