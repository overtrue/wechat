<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenWork\Corp;


use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\ServiceContainer;

class Client extends BaseClient
{

    public function __construct(ServiceContainer $app)
    {
        parent::__construct($app, $app['suite_access_token']);
    }

    /**
     * 企业微信应用授权 url
     * @param string $pre_auth_code
     * @param string $redirect_uri
     * @param string $state
     * @return string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getApp3rdInstallUrl(string $redirect_uri, string $state = '')
    {
        $params = [
            'suite_id'      => $this->app['config']['suite_id'],
            'redirect_uri'  => urlencode($redirect_uri),
            'pre_auth_code' => $this->getPreAuthCode()['pre_auth_code'],
            'state'         => $state || rand()
        ];
        return 'https://open.work.weixin.qq.com/3rdapp/install?' . http_build_query($params);
    }


    /**
     * 获取预授权码
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getPreAuthCode()
    {
        return $this->httpGet('cgi-bin/service/get_pre_auth_code');
    }

    /**
     * 设置授权配置
     * 该接口可对某次授权进行配置
     * @param array $data
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function setSessionInfo(array $data)
    {
        return $this->httpPostJson('cgi-bin/service/set_session_info', compact('data'));
    }

    /**
     * 获取企业永久授权码
     * @param string $auth_code 临时授权码，会在授权成功时附加在redirect_uri中跳转回第三方服务商网站，或通过回调推送给服务商。长度为64至512个字节
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getPermanentCode(string $auth_code)
    {
        $params = [
            'auth_code' => $auth_code
        ];
        return $this->httpPostJson('cgi-bin/service/set_session_info', $params);
    }

    /**
     * 获取企业授权信息
     * @param string $auth_corpid
     * @param string $permanent_code
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getAuthInfo(string $auth_corpid, string $permanent_code)
    {
        $params = [
            'auth_corpid'    => $auth_corpid,
            'permanent_code' => $permanent_code
        ];
        return $this->httpPostJson('cgi-bin/service/get_auth_info', $params);
    }

    /**
     * 获取应用的管理员列表
     * @param string $auth_corpid
     * @param string $agentid
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getAdminList(string $auth_corpid, string $agentid)
    {
        $params = [
            'auth_corpid' => $auth_corpid,
            'agentid'     => $agentid
        ];
        return $this->httpPostJson('cgi-bin/service/get_admin_lis', $params);
    }

    /**
     * 获取登录url
     * @param string $redirect_uri
     * @param string $scope
     * @param string|null $state
     * @return string
     */
    public function getOauth2Uri(string $redirect_uri, string $scope = 'snsapi_userinfo', string $state = null)
    {
        $params = [
            'appid'         => $this->app['config']['suite_id'],
            'redirect_uri'  => urlencode($redirect_uri),
            'response_type' => 'code',
            'scope'         => $scope,
            'state'         => $state || rand()
        ];
        return 'https://open.weixin.qq.com/connect/oauth2/authorize?' . http_build_query($params) . '#wechat_redirect';
    }


    /**
     * 第三方根据code获取企业成员信息
     * @param string $code
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getUserInfo3rd(string $code)
    {
        $params = [
            'code'         => $code,
            'access_token' => $this->app['suite_access_token']->getToken()['suite_access_token']
        ];
        return $this->httpPostJson('cgi-bin/service/getuserinfo3rd', $params);
    }

    /**
     * 第三方使用user_ticket获取成员详情
     * @param string $user_ticket
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getUserDetail3rd(string $user_ticket)
    {
        $params = [
            'user_ticket'  => $user_ticket,
            'access_token' => $this->app['suite_access_token']->getToken()['suite_access_token']
        ];
        return $this->httpPostJson('cgi-bin/service/getuserdetail3rd', $params);
    }

}