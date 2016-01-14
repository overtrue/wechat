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
 * Payment.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */
namespace EasyWeChat\Payment;

use EasyWeChat\Core\Exceptions\FaultException;
use EasyWeChat\Support\XML;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Payment.
 */
class Payment
{
    /**
     * Scheme base path.
     */
    const SCHEME_PATH = 'weixin://wxpay/bizpayurl';

    /**
     * @var API
     */
    protected $api;

    /**
     * Merchant instance.
     *
     * @var \EasyWeChat\Payment\Merchant
     */
    protected $merchant;

    /**
     * Constructor.
     *
     * @param Merchant $merchant
     */
    public function __construct(Merchant $merchant)
    {
        $this->merchant = $merchant;
    }

    /**
     * Build payment scheme for product.
     *
     * @param string $productId
     *
     * @return string
     */
    public function scheme($productId)
    {
        $params = [
            'appid' => $this->merchant->app_id,
            'mch_id' => $this->merchant->merchant_id,
            'time_stamp' => time(),
            'nonce_str' => uniqid(),
            'product_id' => $productId,
        ];

        $params['sign'] = generate_sign($params, $this->merchant->key, 'md5');

        return self::SCHEME_PATH.'?'.http_build_query($params);
    }

    /**
     * Handle payment notify.
     *
     * @param callable $callback
     *
     * @return Response
     * @throws FaultException
     */
    public function handleNotify ( callable $callback = null )
    {
        $notify = $this->getNotify ();
        if ( ! $notify->isValid () )
        {
            throw new FaultException( 'Invalid request XML.', 400 );
        }

        $notifyContent = $notify->getNotify ();
        $successful    = $notifyContent->result_code == 'SUCCESS';
        if ( ! is_null ( $callback ) )
        {
            $result = $notify->response ( call_user_func_array ( $callback, [ $notifyContent, $successful ] ) );
        }
        else
        {
            $result = $notifyContent;
        }

        return $result;
    }

    /**
     * Merchant setter.
     *
     * @param Merchant $merchant
     */
    public function setMerchant(Merchant $merchant)
    {
        $this->merchant = $merchant;
    }

    /**
     * Merchant getter.
     *
     * @return Merchant
     */
    public function getMerchant()
    {
        return $this->merchant;
    }

    /**
     * Return Notify instance.
     *
     * @return \EasyWeChat\Payment\Notify
     */
    public function getNotify()
    {
        return new Notify($this->merchant);
    }

    /**
     * API setter.
     *
     * @param API $api
     */
    public function setAPI(API $api)
    {
        $this->api = $api;
    }

    /**
     * Return API instance.
     *
     * @return API
     */
    public function getAPI()
    {
        return $this->api ?: $this->api = new API($this->getMerchant());
    }

    /**
     * Magic call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     *
     * @codeCoverageIgnore
     */
    public function __call($method, $args)
    {
        if (is_callable([$this->getAPI(), $method])) {
            return call_user_func_array([$this->api, $method], $args);
        }
    }
}
