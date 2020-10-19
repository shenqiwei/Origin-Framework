<?php
/**
 * @access public
 * @param string $error_title 异常标题
 * @param string|array $error_msg 异常信息数组
 * @param array $error_file 异常文件描述数组
 * @context 应用异常显示模块
 */
function exception($error_title,$error_msg,$error_file)
{
    $_error_msg = array(
        "msg" => "{$error_title} [Error Code:0000-0] {$error_msg}",
        "file" => "{$error_file[0]["file"]}",
        "line" => "{$error_file[0]["line"]}",
        "function" => "{$error_file[0]["function"]}",
        "class" => "{$error_file[0]["class"]}"
    );
    if(is_array($error_msg))
        $_error_msg["msg"] = "{$error_title} [Error Code:{$error_msg[0]}] {$error_msg[2]}";
    include(replace(ORIGIN.'template/500.html'));
    eLog($_error_msg["msg"]);
    eLog("in:{$_error_msg["file"]}");
    eLog("line:{$_error_msg["line"]}");
    exit(0);
}
/**
 * @access public
 * @param array $error_arr 异常信息数组
 * @context 底层异常显示模块
 */
function base($error_arr)
{
    $_error_msg = $error_arr["message"];
    $_error_msg = explode("#",$_error_msg);
    $_error_zero = explode(" in ",$_error_msg[0]);
    $_error_zero[1] = explode(":",str_replace(ROOT,null,$_error_zero[1]));
    $_error_zero_line = intval($_error_zero[1][1]);
    array_push($_error_zero,"Line : ". $_error_zero_line);
    array_push($_error_zero, trim(str_replace($_error_zero_line,null,$_error_zero[1][1]))." :");
    $_error_zero[1] = $_error_zero[1][0];
    $_error_zero[1] = "In : ".$_error_zero[1];
    array_splice($_error_msg,0,1,$_error_zero);
    include(replace(ORIGIN.'template/501.html'));
    exit(0);
}
# 设置异常捕捉回调函数
register_shutdown_function("danger");
/**
 * @access public
 * @return array
 * @context 危险异常捕捉函数
 */
function danger()
{
    $_error = error_get_last();
    define("E_FATAL",  E_ERROR | E_USER_ERROR |  E_CORE_ERROR |
        E_COMPILE_ERROR | E_RECOVERABLE_ERROR| E_PARSE );
    if($_error && ($_error["type"]===($_error["type"] & E_FATAL))) {
        if(DEBUG){
            base($_error);
        }
    }
    return null;
}