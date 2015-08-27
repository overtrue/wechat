<?php
/**
 * Refund.php
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    peiwen <haopeiwen123@gmail.com>
 * @copyright 2015 peiwen <haopeiwen123@gmail.com>
 * @link      https://github.com/troubleman
 * @link      https://github.com/troubleman/Wechat
 */

namespace Overtrue\Wechat\Payment;

use Overtrue\Wechat\Http;
use Overtrue\Wechat\Util\Bag;
use Overtrue\Wechat\Utils\XML;
use Overtrue\Wechat\Utils\SignGenerator;
use Overtrue\Wechat\Config\WechatConfig;

class Refund
{
    /**
     * 申请退款接口
     * https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_4
     */
    const REFUND_URL = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
    
    /**
     * 参数包
     * @var String(32)
     */
    protected $bag;

    /**
     * 商户 Key
     */
    protected $key;

    /**
     * 商户证书 cert
     */
    protected $clientCert;
    /**
     * 商户证书 key
     */
    protected $clientKey;

    /**
     * 必填项目
     */
    protected $required = array(
        'appid', 'mch_id', 'nonce_str', 'out_refund_no', 'total_fee', 'refund_fee','op_user_id'
    );
    /**
     * 选填项目
     */
    protected $optional = array('device_info', 'refund_fee_type');

    /**
     * QueryOrder缓存
     * 
     * @var Array
     */
    protected $refund = null;
    
    public function __construct(Bag $bag)
    {   
        //商户基本信息
        $bag->set('appid',  WechatConfig::APPID);
        $bag->set('mch_id', WechatConfig::MCHID);

        //
        if( !$bag->has('nonce_str') ) {
            $bag->set('nonce_str', md5(uniqid(microtime())) );
        }

        //操作员 默认为商户号
        if( !$bag->has('op_user_id') ) {
            $bag->set('op_user_id', WechatConfig::MCHID);
        }

        // 检测必填字段
        foreach($this->required AS $paramName) {
            if( !$bag->has($paramName) ) {
                throw new Exception(sprintf('"%s" is required', $paramName));
            }
        }

        if( !$bag->has('transaction_id') && !$bag->has('out_trade_no') ) {
            throw new Exception('transaction_id or out_trade_no is required as least');
        }

        $this->bag = $bag;
        $this->key = WechatConfig::KEY;

        //设置安全证书路径
        $sslcert_path =  WechatConfig::SSLCERT_PATH? : $bag->get('sslcert_path');
        $sslkey_path  =  WechatConfig::SSLKEY_PATH ? : $bag->get('sslkey_path');
        if(empty($sslcert_path) || empty($sslkey_path) ){
            throw new Exception("Refund required client_cert and client_key");  
        }else{
            $this->setClientCert($sslcert_path);
            $this->setClientKey($sslkey_path);
        }

    }

    /**
     * 设置商户证书 cert
     */
    public function setClientCert($filepath)
    {
        if( !file_exists($filepath) ) {
            throw new Exception(sprintf('client_cert "%s" is not found', $filepath));
        }
        $this->clientCert = $filepath;
    }

    /**
     * 设置商户证书 key
     */
    public function setClientKey($filepath)
    {
        if( !file_exists($filepath) ) {
            throw new PaymentException(sprintf('client_key "%s" is not found', $filepath));
        }
        $this->clientKey = $filepath;
    }

    /**
     * 获取申请退款结果
     * 
     * @param bool|false $force 是否忽略缓存强制更新
     *
     * @return array
     * @throws Exception
     * @throws \Overtrue\Wechat\Exception
     */
    public function getResponse($force = false)
    {

        if ($this->refund !== null && $force === false) {
            return $this->refund;
        }
        
        $params  =  $this->bag->all();

        $signGenerator = new SignGenerator($params);
        $signGenerator->onSortAfter(function($that) {
            $that->key = $this->key;
        });

        // 生成签名
        $sign = $signGenerator->getResult();

        // 调置签名
        $params['sign'] = $sign;

        $request = XML::build($params);

        // $http = new Http(new AccessToken($this->business->appid, $this->business->appsecret));
        $http = new Http();

        $options['ssl']['cert']  = $this->clientCert;
        $options['ssl']['key']   = $this->clientKey;

        $response = $http->request(static::REFUND_URL, Http::POST, $request, $options);
        if(empty($response)) {
            throw new Exception('Get Refund Failure:');
        }

        $response = XML::parse($response);
        if( isset($response['result_code']) &&
            ($response['result_code'] === 'FAIL') ) {
            throw new Exception($response['err_code'].': '.$response['err_code_des']);
        }
        if( isset($response['return_code']) &&
            $response['return_code'] === 'FAIL' ) {
            throw new Exception($response['return_code'].': '.$response['return_msg']);
        }

        return $this->refund = $response;
    }
}
