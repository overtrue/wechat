<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\MiniProgram\TemplateMessage;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\MiniProgram\TemplateMessage\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testSend()
    {
        $client = $this->mockApiClient(Client::class)->makePartial();

        // without touser
        try {
            $client->send();
        } catch (\Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertSame('Attribute "touser" can not be empty!', $e->getMessage());
        }

        // without template_id
        try {
            $client->send(['touser' => 'mock-openid']);
        } catch (\Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertSame('Attribute "template_id" can not be empty!', $e->getMessage());
        }

        $client->expects()->httpPostJson('cgi-bin/message/wxopen/template/send', [
            'touser' => 'mock-openid',
            'template_id' => 'mock-template_id',
            'page' => '',
            'form_id' => 'mock-form_id',
            'emphasis_keyword' => '',
            'data' => [],
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->send(['touser' => 'mock-openid', 'template_id' => 'mock-template_id', 'form_id' => 'mock-form_id']));
    }
    
    public function testTemplatelist()
    {
        $client = $this->mockApiClient(Client::class)->makePartial();
        print('获取帐号下已存在的模板列表');

        // 获取帐号下已存在的模板列表
        $client->expects()->httpPostJson('cgi-bin/wxopen/template/list')->andReturn('mock-result')->once();
        $this->assertNotSame('mock-result', $client->getPrivateTemplates();
    }
}
