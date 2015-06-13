<?php
/**
 * Wechat.php
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace Overtrue\Wechat;

/**
 * SDK 入口
 */
class Wechat
{

    /**
     * 配置信息
     *
     * @var array
     *
     * <pre>
     * [
     *   'use_alias'    => false,
     *   'app_id'       => 'YourAppId', // 必填
     *   'secret'       => 'YourSecret', // 必填
     *   'token'        => 'YourToken',  // 必填
     *   'encoding_key' => 'YourEncodingAESKey' // 加密模式需要，其它模式不需要
     * ]
     * </pre>
     */
    protected static $config;

    /**
     * 已经实例化的对象
     *
     * @var array
     */
    protected static $resolved;

    /**
     * 初始化配置
     *
     * @param array $config 配置项
     *
     * @return void
     */
    public static function config(array $config)
    {
        self::$config = $config;
    }

    /**
     * 获取服务
     *
     * @param string $name
     * @param array  $args
     *
     * @return mixed
     */
    public static function service($name, $args = array())
    {
        return self::build("Overtrue\\Wechat\\" . ucfirst(self::camelCase($name)), $args);
    }

    /**
     * 获取消息
     *
     * @param string $name
     * @param array  $args
     *
     * @return mixed
     */
    public static function message($name, $args = array())
    {
        return self::build("Overtrue\\Wechat\\Messages\\" . ucfirst(self::camelCase($name)), $args);
    }

    /**
     * 获取工具对象
     *
     * @param string $name
     * @param array  $args
     *
     * @return mixed
     */
    public static function util($name, $args = array())
    {
        return self::build("Overtrue\\Wechat\\Utils\\" . ucfirst(self::camelCase($name)), $args);
    }

    /**
     * 获取对象实例
     *
     * @param string $class
     * @param array  $args
     *
     * @return mixed
     */
    public static function build($class, $args = array())
    {
        $args = array_merge(self::$config, $args);

        return self::getResolved($class, $args, function ($key) use ($class, $args) {
            return self::$resolved[$key] = new $class($args);
        });
    }

    /**
     * 字符串转驼峰
     *
     * @param string $string 字符串
     *
     * @return string
     */
    public static function camelCase($string)
    {
        return preg_replace_callback(
            '/_{1,}([a-z])/',
            function ($pipe) {
                    return strtolower($pipe[1]);
            },
            $string
        );
    }

    /**
     * 获取已经实例化的对象
     *
     * @param string   $class    类名
     * @param array    $args     参数
     * @param callable $callback 回调
     *
     * @return mixed
     */
    protected static function getResolved($class, $args, $callback = null)
    {
        $key = $class . md5(json_encode($args));

        if (isset(self::$resolved[$key])) {
            return self::$resolved[$key];
        }

        if (is_callable($callback)) {
            return call_user_func_array($callback, array($key));
        }

        return null;
    }
}
