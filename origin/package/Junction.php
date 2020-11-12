<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context Origin自动加载封装类
 */
namespace Origin\Package;

use Exception;

class Junction
{
    const ROUTE_ITEM_URI = "route";
    const ROUTE_ITEM_MAPPING = "mapping";
    /**
     * @access public
     * @static
     * @var string $Class
     * @var string $Function
     * @var float $LoadTime
    */
    static $Class = null;
    static $Function = null;
    static $LoadTime = 0.0;
    /**
     * @access public
     * @context 默认模式，自动加载入口
     */
    static function initialize()
    {
        # 应用结构包调用
        if(is_file($_common = replace(ROOT . "/application/common/public.php")))
            include("{$_common}");
        # 运行起始时间
        self::$LoadTime = explode(" ",microtime());
        self::$LoadTime = floatval(self::$LoadTime[0])+floatval(self::$LoadTime[1]);
        /**
         * 使用请求器和验证结构进行入口保护
         * @var string $_class 带命名空间信息的类信息
         * @var string $_object 类实例化对象
         * @var string $_method 类对象方法
         */
        # 判断自动加载方法
        if(function_exists('spl_autoload_register')){
            # 设置基础控制器参数变量
            $_catalogue = DEFAULT_APPLICATION."/";
            # 默认控制器文件名
            $_files = DEFAULT_CLASS;
            # 默认控制器类名，由于规则规定类名与文件一致，所以该结构暂时只作为平行结构来使用
            # $_class = DEFAULT_CLASS;
            # 默认控制器方法名
            $_method = DEFAULT_FUNCTION;
            # 转换信息
            $_path_array = array();
            # 获取的路径信息
            if(is_null($_SERVER['PATH_INFO']) or empty($_SERVER['PATH_INFO']))
                $_path = self::route($_SERVER["REQUEST_URI"]);
            else
                $_path = self::route($_SERVER['PATH_INFO']); // nginx条件下PATH_INFO返回值为空
            # 获取协议信息
            $_protocol = $_SERVER["SERVER_PROTOCOL"];
            # 获取服务软件信息
            $_server = $_SERVER["SERVER_SOFTWARE"];
            # 获取地址完整信息
            $_http = $_SERVER["HTTP_HOST"];
            # 获取请求地址信息
            $_request = $_SERVER["REQUEST_URI"];
            # 获取请求器类型
            $_type = $_SERVER["REQUEST_METHOD"];
            # 获取用户ip
            $_use = $_SERVER["REMOTE_ADDR"];
            # 对请求对象地址请求内容进行截取
            if(strpos($_request,'?'))
                $_request = substr($_request,0,strpos($_request,'?'));
            # 执行初始化
            # 判断执行对象是否为程序单元
            $_bool = false;
            $_suffix = array(".html",".htm",".php");
            for($_i = 0;$_i < count($_suffix);$_i++){
                if(!empty(strpos($_request,$_suffix[$_i]))){
                    $_bool = true;
                    break;
                }
            }
            # 忽略其他资源类型文件索引
            if(!$_bool)
                if(strpos($_request,".") === false) $_bool = true;
            if($_bool){
                # 重定义指针， 起始位置0
                $_i = 0;
                if(!empty($_path)){
                    # 转化路径为数组结构
                    $_path_array = explode('/',$_path);
                    # 判断首元素结构是否与默认应用目录相同
                    if(empty($_path_array) and $_path_array[0] != '0' or
                        strtolower($_path_array[$_i]) == DEFAULT_APPLICATION  or
                        (strtolower($_path_array[$_i]) != DEFAULT_APPLICATION and
                            is_dir(replace(ROOT."/application/".strtolower($_path_array[0]))))) {
                        # 变更应用文件夹位置
                        $_catalogue = $_path_array[$_i] . "/";
                        # 指针下移
                        $_i += 1;
                        if ($_i < count($_path_array)) {
                            # 变更控制文件信息
                            $_files = ucfirst($_path_array[$_i]);
                            # 指针下移
                            $_i += 1;
                        }
                    }else{
                        # 变更控制文件信息
                        $_files = ucfirst($_path_array[$_i]);
                        # 指针下移
                        $_i += 1;
                    }
                }
                # 使用加载函数引入应用公共方法文件
                $_public = replace(ROOT."/application/{$_catalogue}common/public.php");
                if(is_file($_public))
                    include("{$_public}");
                # 根据配置信息拼接控制器路径
                $_path = $_catalogue."classes/".ucfirst($_files);
                # 初始化重启位置
                load:
                # 验证文件地址是否可以访问
                if(!is_file(replace("application/{$_path}.php"))){
                    if(DEBUG){
                        if(initialize()){
                            goto load;
                        }
                        try {
                            throw new Exception('Origin Loading Error: Not Fount Classes Document');
                        } catch (Exception $e) {
                            self::error(replace("application/{$_path}.php"), $e->getMessage(), "File");
                            exit(0);
                        }
                    }else{
                        $_404 = replace(ROOT_RESOURCE."/public/template/404.html");
                        if(!is_file($_404)){
                            echo("ERROR:404");
                            exit();
                        }else{
                            include("{$_404}");
                        }
                    }
                }
                # 设置引导地址
                set_include_path(ROOT);
                # 判断文件是否存在
                if(!spl_autoload_register(function($_path){
                    # 转化命名空间内容，拆分结构
                    $_file = explode("\\",$_path);
                    # 循环修改命名空间元素首字母
                    for($_i = 0;$_i < count($_file);$_i++){
                        # 修改文件名,类文件名跳过
                        if($_i === (count($_file) - 1))
                            continue;
                        $_file[$_i] = strtolower($_file[$_i]);
                    }
                    # 重组加载信息内容
                    $file = implode(DS,$_file);
                    require_once("{$file}.php");
                })){
                    try {
                        throw new Exception('Origin Loading Error: Registration load failed');
                    } catch (Exception $e) {
                        self::error(replace("application/{$_path}.php"), $e->getMessage(), "File");
                        exit(0);
                    }
                }
                # 链接记录日志
                $_uri = LOG_ACCESS.date('Ymd').'.log';
                $_msg = "[".$_protocol."] [".$_server."] [Request:".$_type."] to ".$_http.$_request.", by user IP:".$_use;
                $_model_msg = date("Y/m/d H:i:s")." [Note]: ".$_msg.PHP_EOL;
                _log($_uri,$_model_msg);
                # 创建class完整信息变量
                $_class = $_class_path = explode("/","Application".DS.$_path);
                for($_u = 0;$_u < count($_class_path);$_u++){
                    if(empty($_u))
                        $_class = ucfirst($_class_path[$_u]);
                    else
                        $_class .= "\\".ucfirst($_class_path[$_u]);
                }
                # 判断类是否存在,当自定义控制与默认控制器都不存在时，系统抛出异常
                if(class_exists($_class)){
                    self::$Class = $_class;
                    # 声明类对象
                    $_object = new $_class();
                }else{
                    try {
                        throw new Exception('Origin Loading Error: Not Fount Control Class');
                    }catch(Exception $e){
                        self::error("{$_class}",$e->getMessage(),"Class");
                        exit(0);
                    }
                }
                # 判断是否有方法标记信息
                if($_path_array[$_i]){
                    # 如果判断标记信息，是否为控制中方法名
                    if(method_exists($_object, $_path_array[$_i])){
                        $_method = $_path_array[$_i];
                    }
                }
                # 判断方法信息是否可以被调用
                if(method_exists($_object, $_method) and is_callable(array($_object, $_method))){
                    self::$Function = $_method;
                    # 执行方法调用
                    $_object->$_method();
                }else{
                    try {
                        throw new Exception('Origin Loading Error: Not Fount Function Object');
                    } catch (Exception $e) {
                        self::error("{$_method}", $e->getMessage(), "Function");
                        exit(0);
                    }
                }
            }
        }
    }
    /**
     * @access protected
     * @param string $uri 路由对象地址
     * @return string
     * @context 路由解析函数
    */
    protected static function route($uri){
        # 创建对象变量
        $_config = null;
        $_start = 0;
        if(strpos("/",$uri) == 0)
            $_start = 1;
        if(strpos($uri,'.'))
            $_path = substr($uri, $_start, strpos($uri,'.')-1);
        else
            $_path = substr($uri, $_start);
        $_receipt = $_path;
        # 创建路由文件目录变量
        $_files = replace(ROOT."/common/config/route.php");
        # 判断路由文件是否存在
        if(is_file($_files)){
            # 获取路由配置信息
            $_config = require_once("{$_files}");
            # 判断路由信息是否有效
            if (is_array($_config) and !empty($_config)){
                # 遍历路由信息，用于比对路由信息
                for ($i = 0; $i < count($_config); $i++){
                    # 判断路由
                    if(!is_array($_config[$i]))
                        continue;
                    if(!key_exists(self::ROUTE_ITEM_MAPPING,$_config[$i]))
                        continue;
                    if(!key_exists(self::ROUTE_ITEM_URI,$_config[$i]))
                        continue;
                    if($_config[$i]["route"] != $_path)
                        continue;
                    # 获取映射信息
                    $_receipt = $_config[$i]['mapping'];
                    # 跳出循环结束验证
                    break;
                }
            }
        }
        return $_receipt;
    }
    /**
     * @access public
     * @param string $obj 未加载对象（class|function）
     * @param string $error 错误信息
     * @param string $type 加载类型
     * @context 加载错误信息
     */
    static function error($obj,$error,$type)
    {
        if(DEBUG or ERROR){
            if(!is_file($_404 = replace(RESOURCE_PUBLIC."/template/404.html"))){
                $_404 = replace(ORIGIN.'template/404.html');
            }
            include("{$_404}");
        }

    }
}