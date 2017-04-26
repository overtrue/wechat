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
 * MiniProgram.php.
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

namespace EasyWeChat\MiniProgram;

use EasyWeChat\Support\Traits\PrefixedContainer;

/**
 * Class MiniProgram.
 *
 * @property \EasyWeChat\MiniProgram\Sns\Sns $sns
 * @property \EasyWeChat\MiniProgram\Stats\Stats $stats
 * @property \EasyWeChat\MiniProgram\Server\Guard $server
 * @property \EasyWeChat\MiniProgram\TemplateMessage\TemplateMessage $template_message
 * @property \EasyWeChat\MiniProgram\QRCode\QRCode $qrcode
 * @property \EasyWeChat\MiniProgram\Material\Temporary $material_temporary
 * @property \EasyWeChat\MiniProgram\CustomerService\CustomerService $customer_service
 */
class MiniProgram
{
    use PrefixedContainer;
}
