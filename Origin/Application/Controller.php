<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.5
 * @copyright 2015-2017
 * @context: IoC 公共控制器
 */
namespace Origin\Application;

use Origin\Kernel\Load;
use Origin\Kernel\Parameter\Output;
use Origin\Kernel\Graph\View;
use Exception;

/**
 * 功能控制器，负责内核功能方法的预加载调用
*/
class Controller
{
    /**
     * 装载参数信息数组
     * @var array $_Param_Array
    */
    private $_Param = array();
    /**
     * 构造方法，获取当前操作类信息
     * @access public
    */
    function __construct()
    {}
    /**
     * 向模板加载数据信息
     * @access protected
     * @param $key
     * @param $value
     * @return null
    */
    protected function param($key, $value)
    {
        $_regular = '/^[^\_\W]+(\_[^\_\W]+)*$/';
        if(is_true($_regular, $key)){
            $this->_Param[Load::$_Class][$key] = $value;
        }else{
            # 异常提示：变量名称包含非合法符号
            try{
                throw new Exception('Variable name contains non legal symbols');
            }catch(Exception $e){
                eLog($e->getMessage());
                $_output = new Output();
                $_output->exception("Param Error",$e->getMessage(),debug_backtrace(0,1));
                exit();
            }
        }
        return null;
    }
    /**
     * 调用模板方法
     * @access protected
     * @param $view
     * @return null
     */
    protected function view($view=null)
    {
        $_page = Load::$_Function;
        $_regular = '/^[^\_\W]+(\_[^\_\W]+)*(\:[^\_\W]+(\_[^\_\W]+)*)*$/';
        $_dir = str_replace(Config('APPLICATION_CONTROLLER')."/", '',
                str_replace(Config('DEFAULT_APPLICATION')."/", '',
                    str_replace('Application/', '',
                        str_replace('\\', '/', Load::$_Class))));
        if($view !== null and is_true($_regular, $view)){
            $_page = $view;
        }
        View::view($_dir, $_page,$this->_Param[Load::$_Class]);
        return null;
    }
    /**
     * 返回执行对象类名
     * @access protected
     * @return string
     */
    protected function get_class()
    {
        return Load::$_Class;
    }
    /**
     * 返回执行对象方法名
     * @access protected
     * @return string
     */
    protected function get_function()
    {
        return Load::$_Function;
    }
    /**
     * 执行成功提示信息
     * @access protected
     * @param $message
     * @param $url
     * @param $time
     * @return null
    */
    protected function success($message='success',$url='#',$time=3)
    {
        $_setting = array("bgcolor"=>"floralwhite","color"=>"#000000","title"=>"Success");
        Output::output($time, $message, $url, $_setting);
        return null;
    }
    /**
     * 错误提示
     * @access protected
     * @param $message
     * @param $url
     * @param $time
     * @return null
    */
    protected function error($message='error',$url='#',$time=3)
    {
        $_setting = array("bgcolor"=>"orangered","color"=>"floralwhite","title"=>"Error");
        Output::output($time, $message, $url, $_setting);
        return null;
    }
    /**
     * @access public
     * @param array $array
     * @return null
    */
    protected function json($array=null)
    {
        Output::json($array);
        return null;
    }
}