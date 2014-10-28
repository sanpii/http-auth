<?php

namespace Test\Unit\Sanpi\Http\Auth;

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
        $authorization = $this->auth->getAuthorization([], 'Aladdin', 'open sesame');
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
        $authorization = $this->auth->authenticate(
            ['PHP_AUTH_USER' => 'Aladdin', 'PHP_AUTH_PW' => 'open sesame'],
            'Aladdin', 'open sesame'
        );
        $this->boolean($authorization)
            ->isTrue();
    }

    public function testInvalidAuthenticate()
    {
        $authorization = $this->auth->authenticate(
            ['PHP_AUTH_USER' => 'Aladdin', 'PHP_AUTH_PW' => 'xxx'],
            'Aladdin', 'open sesame'
        );
        $this->boolean($authorization)
            ->isFalse();
    }
}
