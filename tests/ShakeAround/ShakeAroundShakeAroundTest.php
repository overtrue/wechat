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
 * ShakeAroundShakeAroundTest.php.
 *
 * @author    allen05ren <allen05ren@outlook.com>
 * @copyright 2016 overtrue <i@overtrue.me>
 *
 * @see       https://github.com/overtrue
 * @see       http://overtrue.me
 */

namespace EasyWeChat\Tests\ShakeAround;

use EasyWeChat\ShakeAround\Device;
use EasyWeChat\ShakeAround\Group;
use EasyWeChat\ShakeAround\Material;
use EasyWeChat\ShakeAround\Page;
use EasyWeChat\ShakeAround\Relation;
use EasyWeChat\ShakeAround\ShakeAround;
use EasyWeChat\ShakeAround\Stats;
use EasyWeChat\Tests\TestCase;

class ShakeAroundShakeAroundTest extends TestCase
{
    public function getShakeAround()
    {
        $shake_around = \Mockery::mock('EasyWeChat\ShakeAround\ShakeAround[parseJSON]', [\Mockery::mock('EasyWeChat\Core\AccessToken')]);

        return $shake_around;
    }

    /**
     * Test register().
     */
    public function testRegister()
    {
        $shake_around = $this->getShakeAround();
        $shake_around->shouldReceive('parseJSON')->andReturnUsing(function ($method, $params) {
            return [
                'api' => $params[0],
                'params' => $params[1],
            ];
        });

        $expected = [
            'name' => 'allen05ren',
            'phone_number' => 13888888888,
            'email' => 'allen05ren@outlook.com',
            'industry_id' => '0101',
            'qualification_cert_urls' => [],
            'apply_reason' => 'test',
        ];
        $result = $shake_around->register('allen05ren', 13888888888, 'allen05ren@outlook.com', '0101', [], 'test');
        $this->assertStringStartsWith(ShakeAround::API_ACCOUNT_REGISTER, $result['api']);
        $this->assertEquals($expected, $result['params']);

        $expected = [
            'name' => 'allen05ren',
            'phone_number' => 13888888888,
            'email' => 'allen05ren@outlook.com',
            'industry_id' => '0101',
            'qualification_cert_urls' => [],
        ];

        $result = $shake_around->register('allen05ren', 13888888888, 'allen05ren@outlook.com', '0101', []);
        $this->assertEquals($expected, $result['params']);
    }

    /**
     * Test getShakeInfo().
     */
    public function testGetShakeInfo()
    {
        $shake_around = $this->getShakeAround();
        $shake_around->shouldReceive('parseJSON')->andReturnUsing(function ($method, $params) {
            return [
                'api' => $params[0],
                'params' => $params[1],
            ];
        });

        $expected = [
            'ticket' => '6ab3d8465166598a5f4e8c1b44f44645',
        ];

        $result = $shake_around->getShakeInfo('6ab3d8465166598a5f4e8c1b44f44645');

        $this->assertStringStartsWith(ShakeAround::API_GET_SHAKE_INFO, $result['api']);
        $this->assertEquals($expected, $result['params']);

        $expected = [
            'ticket' => '6ab3d8465166598a5f4e8c1b44f44645',
            'need_poi' => 1,
        ];

        $result = $shake_around->getShakeInfo('6ab3d8465166598a5f4e8c1b44f44645', 1);

        $this->assertEquals($expected, $result['params']);
    }

    /**
     * Test getStatus().
     */
    public function testGetStatus()
    {
        $shake_around = $this->getShakeAround();
        $shake_around->shouldReceive('parseJSON')->andReturnUsing(function ($method, $params) {
            return [
                'api' => $params[0],
            ];
        });

        $result = $shake_around->getStatus();

        $this->assertStringStartsWith(ShakeAround::API_ACCOUNT_AUDIT_STATUS, $result['api']);
    }

    /**
     * Test device().
     */
    public function testDevice()
    {
        $shake_around = $this->getShakeAround();

        $result = $shake_around->device();

        $this->assertInstanceOf(Device::class, $result);
    }

    /**
     * Test group().
     */
    public function testGroup()
    {
        $shake_around = $this->getShakeAround();

        $result = $shake_around->group();

        $this->assertInstanceOf(Group::class, $result);
    }

    /**
     * Test page().
     */
    public function testPage()
    {
        $shake_around = $this->getShakeAround();

        $result = $shake_around->page();

        $this->assertInstanceOf(Page::class, $result);
    }

    /**
     * Test material().
     */
    public function testMaterial()
    {
        $shake_around = $this->getShakeAround();

        $result = $shake_around->material();

        $this->assertInstanceOf(Material::class, $result);
    }

    /**
     * Test relation().
     */
    public function testRelation()
    {
        $shake_around = $this->getShakeAround();

        $result = $shake_around->relation();

        $this->assertInstanceOf(Relation::class, $result);
    }

    /**
     * Test stats().
     */
    public function testStats()
    {
        $shake_around = $this->getShakeAround();

        $result = $shake_around->stats();

        $this->assertInstanceOf(Stats::class, $result);
    }
}
