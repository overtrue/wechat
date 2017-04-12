<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * OpenPlatform.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    mingyoung <mingyoungcheung@gmail.com>
 * @copyright 2016
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\OpenPlatform;

use EasyWeChat\Support\Traits\PrefixedContainer;

/**
 * Class OpenPlatform.
 *
 * @property \EasyWeChat\OpenPlatform\Api\PreAuthorization $pre_auth
 * @property \EasyWeChat\OpenPlatform\Guard $server
 * @property \EasyWeChat\OpenPlatform\AccessToken $access_token
 *
 * @method \EasyWeChat\Support\Collection getAuthorizationInfo($authCode = null)
 * @method \EasyWeChat\Support\Collection getAuthorizationToken($authorizerAppId, $authorizerRefreshToken)
 * @method \EasyWeChat\Support\Collection getAuthorizerInfo($authorizerAppId)
 * @method \EasyWeChat\Support\Collection getAuthorizerOption($authorizerAppId, $optionName)
 * @method \EasyWeChat\Support\Collection setAuthorizerOption($authorizerAppId, $optionName, $optionValue)
 */
class OpenPlatform
{
    use PrefixedContainer;

    /**
     * Create an instance of the EasyWeChat for the given authorizer.
     *
     * @param string $appId        Authorizer AppId
     * @param string $refreshToken Authorizer refresh-token
     *
     * @return \EasyWeChat\Foundation\Application
     */
    public function createAuthorizer($appId, $refreshToken)
    {
        $this->daemon->setAuthorizerAppId($appId);
        $this->daemon->setAuthorizerRefreshToken($refreshToken);

        $application = $this->app;
        $application['access_token'] = $this->authorizer_token;
        $application['oauth'] = $this->oauth;

        return $application;
    }

    /**
     * Quick access to the base-api.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array([$this->api, $method], $args);
    }
}
