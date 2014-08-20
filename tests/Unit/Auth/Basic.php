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

    public function testGetAuthorization()
    {
        $authorization = $this->auth->getAuthorization([], 'Aladdin', 'open sesame');
        $this->string($authorization)
            ->isIdenticalTo('Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ==');
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
