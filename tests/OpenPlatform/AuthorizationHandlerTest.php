<?php

/**
 * Test AuthHandlerTest.php.
 *
 * @author lixiao <leonlx126@gmail.com>
 */

namespace EasyWeChat\Tests\OpenPlatform;

use EasyWeChat\OpenPlatform\EventHandlers\Authorized;
use EasyWeChat\OpenPlatform\EventHandlers\Unauthorized;
use EasyWeChat\Support\Collection;

class AuthorizationHandlerTest extends DaemonTest
{
    public function testAuthorized()
    {
        return;
        $appId = 'appid@123';
        $authorizerAppId = 'appid@456';
        $authorizerAccessToken = 'access@123';
        $authorizerRefreshToken = 'refresh@123';
        $authorization = $this->make(
            $appId, $authorizerAppId,
            $authorizerAccessToken, $authorizerRefreshToken
        );

        $message = [
            'AppId' => 'open-platform-app-id',
            'CreateTIme' => '1413192760',
            'InfoType' => 'authorized',
            'AuthorizerAppid' => 'authorizer-app-id',
            'AuthorizationCode' => 'auth-code',
            'AuthorizationCodeExpiredTime' => '600',
        ];
        $authorized = new Authorized($authorization);
        $authorized->handle(new Collection($message));

        $this->assertEquals(
            $authorizerAccessToken,
            $authorization->getAuthorizerAccessToken()
        );
        $this->assertEquals(
            $authorizerRefreshToken,
            $authorization->getAuthorizerRefreshToken()
        );
    }

    public function testUnauthorized()
    {
        return;
        $appId = 'appid@123';
        $authorizerAppId = 'appid@456';
        $authorizerAccessToken = 'access@123';
        $authorizerRefreshToken = 'refresh@123';
        $authorization = $this->make(
            $appId, $authorizerAppId,
            $authorizerAccessToken, $authorizerRefreshToken
        );

        // Authorized => saves the tokens.
        $message = [
            'AppId' => 'open-platform-app-id',
            'CreateTIme' => '1413192760',
            'InfoType' => 'authorized',
            'AuthorizerAppid' => 'authorizer-app-id',
            'AuthorizationCode' => 'auth-code',
            'AuthorizationCodeExpiredTime' => '600',
        ];
        $authorized = new Authorized($authorization);
        $authorized->handle(new Collection($message));

        // Unauthorized => removes the tokens.
        $message = [
            'AppId' => 'open-platform-app-id',
            'CreateTIme' => '1413192760',
            'InfoType' => 'authorized',
            'AuthorizerAppid' => 'authorizer-app-id',
        ];
        $authorized = new Unauthorized($authorization);
        $authorized->handle(new Collection($message));

        $this->assertFalse($authorization->getAuthorizerAccessToken());
        $this->setExpectedException(\EasyWeChat\Core\Exception::class);
        $this->assertFalse($authorization->getAuthorizerRefreshToken());
    }
}
