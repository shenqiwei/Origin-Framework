<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context: Origin框架Redis封装类
 */
namespace Origin\Package\Redis;

class Key
{
    /**
     * @access private
     * @var object $Connect 数据库链接对象
     */
    private $Connect;

    /**
     * @access public
     * @param object $connect redis主类链接信息
     * @return void
     * @context 构造函数，装在redis数据源连接对象
     */
    function __construct($connect)
    {
        $this->Connect = $connect;
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @return bool
     * @context 删除元素对象内容
     */
    function del($key)
    {
        if($this->Connect->exists($key)){
            $_receipt = $this->Connect->del($key);
        }else{
            $_receipt = false;
        }
        return $_receipt;
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @return mixed
     * @context 序列化元素对象内容
     */
    function dump($key)
    {
        if($this->Connect->exists($key)){
            $_receipt = $this->Connect->dump($key);
            if($_receipt === "nil")
                $_receipt = null;
        }else{
            $_receipt = null;
        }
        return $_receipt;
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @param int $timestamp 时间戳
     * @return bool
     * @context 使用时间戳设置元素对象生命周期
     */
    function setTSC($key,$timestamp)
    {
        return $this->Connect->expireAt($key,$timestamp);
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @param int $second 时间戳
     * @return bool
     * @context 使用秒计时单位设置元素对象生命周期
     */
    function setSec($key,$second)
    {
        return $this->Connect->expire($key,$second);
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @param int $timestamp 时间戳
     * @return bool
     * @context 使用毫秒时间戳设置元素对象生命周期
     */
    function setTSM($key,$timestamp)
    {
        return $this->Connect->pExpireAt($key,$timestamp);
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @param int $millisecond 时间戳
     * @return bool
     * @context 使用毫秒计时单位设置元素对象生命周期
     */
    function setMil($key,$millisecond)
    {
        return $this->Connect->pExpire($key,$millisecond);
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @return bool
     * @context 移除元素目标生命周期限制
     */
    function rmCycle($key)
    {
        return $this->Connect->persist($key);
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @return int
     * @context 获取元素对象剩余周期时间(毫秒)
     */
    function remaining($key)
    {
        return $this->Connect->pttl($key);
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @return int
     * @context 获取元素对象剩余周期时间(秒)
     */
    function remain($key)
    {
        return $this->Connect->ttl($key);
    }

    /**
     * @access public
     * @param string $closeKey 相近元素对象（key*）
     * @return mixed
     * @context 获取搜索相近元素对象键
     */
    function keys($closeKey)
    {
        $_receipt = $this->Connect->keys($closeKey);
        if($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }

    /**
     * @access public
     * @return mixed
     * @context 随机返回元素键
     */
    function randKey()
    {
        $_receipt = $this->Connect->randomKey();
        if($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @param string $newKey 新命名
     * @return bool
     * @context 重命名元素对象
     */
    function rnKey($key,$newKey)
    {
        if($this->Connect->exists($key)){
            $_receipt = $this->Connect->rename($key, $newKey);
        }else{
            $_receipt = false;
        }
        return $_receipt;
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @param string $newKey 新命名
     * @return int
     * @context 非重名元素对象重命名
     */
    function irnKey($key,$newKey)
    {
        return $this->Connect->renameNx($key, $newKey);
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @return string
     * @context 获取元素对象内容数据类型
     */
    function type($key)
    {
        return $this->Connect->type($key);
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @param string $database 对象数据库名
     * @return int
     * @context 将元素对象存入数据库
     */
    function inDB($key,$database)
    {
        return $this->Connect->move($key, $database);
    }
}