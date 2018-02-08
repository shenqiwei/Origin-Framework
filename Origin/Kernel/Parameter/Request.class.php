<?php
/**
 * coding: utf-8 *
 * system OS: windows2008 *
 * work Tools:Phpstorm *
 * language Ver: php7.1 *
 * agreement: PSR-1 to PSR-11 *
 * filename: IoC.Origin.Kernel.Parameter.Validate *
 * version: 1.0 *
 * structure: common framework *
 * email: cheerup.shen@foxmail.com *
 * designer: 沈启威 *
 * developer: 沈启威 *
 * partner: 沈启威 *
 * create Time: 2017/01/06 14:30
 * update Time: 2017/01/09 16:22
 * chinese Context: IoC 请求结构封装
 */

namespace Origin\Kernel\Parameter;
/**
 * 参数请求基类，公共类
 */
class Request
{
    /**
     * 全局变量，用于方法间值的传递
     * 请求器，请求类型
     * @access private
     * @var string $_Method
    */
    private $_Method = 'request';
    /**
     * 全局变量，用于方法间值的传递
     * 请求器，需调用的参数对象
     * @access private
     * @var $_Validate_Name
    */
    private $_Validate_Name = null;
    /**
     * 全局变量，用于方法间值的传递
     * 请求器，被调用的参数对象的数据类型
     * @access private
     * @var string $_Type
    */
    private $_Type = 'string';
    /**
     * 全局变量，用于方法间值的传递
     * 请求其，当请求内容为空或不存在时，系统会放回默认值信息
     * @access private
     * @var $_Default
    */
    private $_Default = null;
    /**
     * 构造函数，用于对请求状态和请求获得值的过滤形式进行加载和判断
     * 对象参数
     * @access public
     * @param $method
     * @param $validate_name
     * @param $type
     * @param $default
    */
    function __construct( $validate_name, $default=null, $method='request', $type='string')
    {
        /**
         * 正则变量,method 用于验证请求方式，type用于转化数据类型
         * @var string $_method_regular
         * @var string $_type_regular
         */
        # 请求方式正则，涵盖基本的5中表单请求
        $_method_regular = '/^(request|post|get|delete|put){1}$/';
        if(is_true($_method_regular, $method) === true){
            $this->_Method = $method;
        }
        $this->_Validate_Name = $validate_name;
        # 数据类型正则，用于限定php接受值的基本数据类型
        $_type_regular = '/^(int|integer|string|str|float|double|boolean){1}$/';
        if(is_true($_type_regular, $type) === true){
            $this->_Type = $type;
        }
        $this->_Default = $default;
    }
    /**
     * 请求器主方法，通过对请求方式的判断，获取请求对象的值信息
     * 根据查询变量的名称，进行数组判断及数组变量，返回相应值
     * 当查询值不存在或者为空时，系统将默认值赋入，当默认值为空或者null时，直接返回null值
     * @access public
     * @return mixed
    */
    public function main()
    {
        /**
         * @var string $_receipt
         * @var array $_array
         * @var string $k
         * @var string $v
         */
        # 创建返回变量，设置值为 null
        $_receipt = null;
        # 设置请求组数组对象变量， 设置值为 null
        $_array = null;
        # 判断请求类型，并将请求对象中的值存入数组对象变量
        switch(strtolower($this->_Method)){
            case 'get':
                $_array = $_GET;
                break;
            case 'post':
                $_array = $_POST;
                break;
            default:
                $_array = $_REQUEST;
                break;
        }
        # 判断数组是否有有效
        if($_array){# 判断php是否在捕捉请求时，对值信息进行过滤，如果没有则进行过滤
            # 判断数组中是否存在，需查询变量名,如果存在进行数组遍历，反之执行默认值装载
            if(array_key_exists($this->_Validate_Name, $_array)){
                # 数组遍历，通过逐一比对获取查询变量信息值
                foreach($_array as $k => $v){
                    if($k == $this->_Validate_Name){
                        if(!get_magic_quotes_gpc() and !is_array($v)){
                            $_receipt = addslashes($v);
                        }else{
                            $_receipt = $v;
                        }
                        break;
                    }else{
                        continue;
                    }
                }
            }else{
                # 当查询变量名不存在与数组中时，装载默认值
                if($this->_Default != null){
                    $_receipt = addslashes($this->_Default);
                }
            }
        }else{
            # 当数组无效时，装载默认值
            if($this->_Default != null){
                $_receipt = addslashes($this->_Default);
            }
        }
        return $_receipt;
    }
    /**
     * 请求器删除方法，用于删除指定元素
     * @access public
     * @return null
    */
    function delete(){
        $_array = null;
        # 判断请求类型，并将请求对象中的值存入数组对象变量
        switch(strtolower($this->_Method)){
            case 'get':
                if(array_key_exists($this->_Validate_Name, $_GET))
                    unset($_GET[$this->_Validate_Name]);
                break;
            case 'post':
                if(array_key_exists($this->_Validate_Name, $_POST))
                    unset($_POST[$this->_Validate_Name]);
                break;
            default:
                if(array_key_exists($this->_Validate_Name, $_REQUEST))
                    unset($_REQUEST[$this->_Validate_Name]);
                break;
        }
        return null;
    }
}