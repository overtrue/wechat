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
 * ShakeAroundPageTest.php.
 *
 * @author    allen05ren <allen05ren@outlook.com>
 * @copyright 2016 overtrue <i@overtrue.me>
 *
 * @see       https://github.com/overtrue
 * @see       http://overtrue.me
 */

namespace EasyWeChat\Tests\ShakeAround;

use EasyWeChat\ShakeAround\Page;
use EasyWeChat\Tests\TestCase;

class ShakeAroundPageTest extends TestCase
{
    public function getPage()
    {
        $page = \Mockery::mock('EasyWeChat\ShakeAround\Page[parseJSON]', [\Mockery::mock('EasyWeChat\Core\AccessToken')]);
        $page->shouldReceive('parseJSON')->andReturnUsing(function ($method, $params) {
            return [
                'api' => $params[0],
                'params' => $params[1],
            ];
        });

        return $page;
    }

    /**
     * Test add().
     */
    public function testAdd()
    {
        $page = $this->getPage();

        $expected = [
            'title' => '主标题',
            'description' => '副标题',
            'page_url' => 'https://zb.weixin.qq.com',
            'icon_url' => 'http://3gimg.qq.com/shake_nearby/dy/icon',
        ];

        $result = $page->add('主标题', '副标题', 'https://zb.weixin.qq.com', 'http://3gimg.qq.com/shake_nearby/dy/icon');

        $this->assertStringStartsWith(Page::API_ADD, $result['api']);
        $this->assertEquals($expected, $result['params']);

        $expected = [
            'title' => '主标题',
            'description' => '副标题',
            'page_url' => 'https://zb.weixin.qq.com',
            'icon_url' => 'http://3gimg.qq.com/shake_nearby/dy/icon',
            'comment' => '数据示例',
        ];

        $result = $page->add('主标题', '副标题', 'https://zb.weixin.qq.com', 'http://3gimg.qq.com/shake_nearby/dy/icon', '数据示例');

        $this->assertEquals($expected, $result['params']);
    }

    /**
     * Test update().
     */
    public function testUpdate()
    {
        $page = $this->getPage();

        $expected = [
            'page_id' => 1234,
            'title' => '主标题',
            'description' => '副标题',
            'page_url' => 'https://zb.weixin.qq.com',
            'icon_url' => 'http://3gimg.qq.com/shake_nearby/dy/icon',
        ];

        $result = $page->update(1234, '主标题', '副标题', 'https://zb.weixin.qq.com', 'http://3gimg.qq.com/shake_nearby/dy/icon');

        $this->assertStringStartsWith(Page::API_UPDATE, $result['api']);
        $this->assertEquals($expected, $result['params']);

        $expected = [
            'page_id' => 1234,
            'title' => '主标题',
            'description' => '副标题',
            'page_url' => 'https://zb.weixin.qq.com',
            'icon_url' => 'http://3gimg.qq.com/shake_nearby/dy/icon',
            'comment' => '数据示例',
        ];

        $result = $page->update(1234, '主标题', '副标题', 'https://zb.weixin.qq.com', 'http://3gimg.qq.com/shake_nearby/dy/icon', '数据示例');

        $this->assertStringStartsWith(Page::API_UPDATE, $result['api']);
        $this->assertEquals($expected, $result['params']);
    }

    /**
     * Test fetchByIds().
     */
    public function testFetchByIds()
    {
        $page = $this->getPage();

        $expected = [
            'type' => 1,
            'page_ids' => [1234, 5678],
        ];

        $result = $page->fetchByIds([1234, 5678]);

        $this->assertStringStartsWith(Page::API_SEARCH, $result['api']);
        $this->assertEquals($expected, $result['params']);
    }

    /**
     * Test pagination().
     */
    public function testPagination()
    {
        $page = $this->getPage();

        $expected = [
            'type' => 2,
            'begin' => 0,
            'count' => 10,
        ];

        $result = $page->pagination(0, 10);

        $this->assertStringStartsWith(Page::API_SEARCH, $result['api']);
        $this->assertEquals($expected, $result['params']);
    }

    /**
     * Test delete().
     */
    public function testDelete()
    {
        $page = $this->getPage();

        $expected = [
            'page_id' => 1234,
        ];

        $result = $page->delete(1234);

        $this->assertStringStartsWith(Page::API_DELETE, $result['api']);
        $this->assertEquals($expected, $result['params']);
    }
}
