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
 * AuthorizerToken.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    lixiao <leonlx126@gmail.com>
 * @copyright 2016
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\OpenPlatform;

use EasyWeChat\Core\AccessToken;

/**
 * Class AuthorizerToken
 *
 * AuthorizerToken is responsible for the access token of the authorizer,
 * the complexity is that this access token also requires the refresh token
 * of the authorizer which is acquired by the open platform authorization
 * process.
 *
 * This completely overrides the original AccessToken.
 *
 * @package EasyWeChat\OpenPlatform
 */
class AuthorizerToken extends AccessToken {

    /**
     * Handles authorization.
     *
     * @var Authorization
     */
    protected $authorization;

    /**
     * AuthorizerAccessToken constructor.
     *
     * @param string $appId
     * @param Authorization $authorization
     */
    public function __construct($appId, Authorization $authorization)
    {
        parent::__construct($appId, null);

        $this->authorization = $authorization;
    }

    /**
     * Get token from WeChat API.
     *
     * @param bool $forceRefresh
     *
     * @return string
     */
    public function getToken($forceRefresh = false)
    {
        $cached = $this->authorization->getAuthorizerAccessToken();

        if ($forceRefresh || empty($cached)) {
            return $this->authorization->handleAuthorizerAccessToken();
        }

        return $cached;
    }
}
