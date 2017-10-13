<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\Application;
use EasyWeChat\Work\Base\Client;

class ApplicationTest extends TestCase
{
    public function testInstances()
    {
        $app = new Application(['agent_id' => 102093]);

        $this->assertInstanceOf(\EasyWeChat\Work\OA\Client::class, $app->oa);
        $this->assertInstanceOf(\EasyWeChat\Work\Auth\AccessToken::class, $app->access_token);
        $this->assertInstanceOf(\EasyWeChat\Work\Agent\Client::class, $app->agent);
        $this->assertInstanceOf(\EasyWeChat\Work\Department\Client::class, $app->department);
        $this->assertInstanceOf(\EasyWeChat\Work\Media\Client::class, $app->media);
        $this->assertInstanceOf(\EasyWeChat\Work\Menu\Client::class, $app->menu);
        $this->assertInstanceOf(\EasyWeChat\Work\Message\Client::class, $app->message);
        $this->assertInstanceOf(\EasyWeChat\Work\Message\Messenger::class, $app->messenger);
        $this->assertInstanceOf(\EasyWeChat\Work\Server\Guard::class, $app->server);
        $this->assertInstanceOf(\EasyWeChat\BasicService\Jssdk\Client::class, $app->jssdk);
        $this->assertInstanceOf(\Overtrue\Socialite\Providers\WeWorkProvider::class, $app->oauth);
    }

    public function testBaseCall()
    {
        $client = \Mockery::mock(Client::class);
        $client->expects()->getCallbackIp(1, 2, 3)->andReturn('mock-result');

        $app = new Application([]);
        $app['base'] = $client;

        $this->assertSame('mock-result', $app->getCallbackIp(1, 2, 3));
    }
}
