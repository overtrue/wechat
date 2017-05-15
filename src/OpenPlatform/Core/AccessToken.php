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
 * AccessToken.php.
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

namespace EasyWeChat\OpenPlatform\Core;

use EasyWeChat\Foundation\Core\AccessToken as BaseAccessToken;

class AccessToken extends BaseAccessToken
{
    /**
     * VerifyTicket.
     *
     * @var \EasyWeChat\OpenPlatform\Core\VerifyTicket
     */
    protected $verifyTicket;

    /**
     * API.
     */
    const API_TOKEN_GET = 'https://api.weixin.qq.com/cgi-bin/component/api_component_token';

    /**
     * {@inheritdoc}.
     */
    protected $queryName = 'component_access_token';

    /**
     * {@inheritdoc}.
     */
    protected $tokenJsonKey = 'component_access_token';

    /**
     * {@inheritdoc}.
     */
    protected $prefix = 'easywechat.open_platform.component_access_token.';

    /**
     * Set VerifyTicket.
     *
     * @param EasyWeChat\OpenPlatform\Core\VerifyTicket $verifyTicket
     *
     * @return $this
     */
    public function setVerifyTicket(VerifyTicket $verifyTicket)
    {
        $this->verifyTicket = $verifyTicket;

        return $this;
    }

    /**
     * {@inheritdoc}.
     */
    public function requestFields(): array
    {
        return [
            'component_appid' => $this->getClientId(),
            'component_appsecret' => $this->getClientSecret(),
            'component_verify_ticket' => $this->verifyTicket->getTicket(),
        ];
    }
}
