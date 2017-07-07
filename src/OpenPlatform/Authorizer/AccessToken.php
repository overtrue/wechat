<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenPlatform\Authorizer;

use EasyWeChat\Kernel\AccessToken as BaseAccessToken;
use EasyWeChat\OpenPlatform\Auth\AccessToken as OpenPlatformAccessToken;

/**
 * Class AccessToken.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class AccessToken extends BaseAccessToken
{
    /**
     * @var string
     */
    protected $requestMethod = 'POST';

    /**
     * @var string
     */
    protected $queryName = 'access_token';

    /**
     * {@inheritdoc}.
     */
    protected $tokenKey = 'authorizer_access_token';

    /**
     * @var string
     */
    protected $endpointToGetToken = 'cgi-bin/component/api_authorizer_token';

    /**
     * @var \EasyWeChat\OpenPlatform\Auth\AccessToken
     */
    protected $openPlatformAccessToken;

    /**
     * @param \EasyWeChat\OpenPlatform\Auth\AccessToken $accessToken
     *
     * @return $this
     */
    public function setOpenPlatformAccessToken(OpenPlatformAccessToken $accessToken)
    {
        $this->openPlatformAccessToken = $accessToken;

        return $this;
    }

    /**
     * {@inheritdoc}.
     */
    protected function getCredentials(): array
    {
        return [
            'component_appid' => $this->app['config']['component_app_id'],
            'authorizer_appid' => $this->app['config']['app_id'],
            'authorizer_refresh_token' => $this->app['config']['refresh_token'],
        ];
    }

    /**
     * Append queries to the current request endpoint for open-platform authorizer.
     *
     * @return array
     */
    protected function appendQuery(): array
    {
        return [
            'component_access_token' => $this->openPlatformAccessToken->getToken()['component_access_token'],
        ];
    }
}
