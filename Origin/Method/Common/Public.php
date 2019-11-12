<?php
/**
 * coding: utf-8 *
 * system OS: windows2008 *
 * work Tools:Phpstorm *
 * language Ver: php7.1 *
 * agreement: PSR-1 to PSR-11 *
 * filename: IoC.Origin.Function.Method.Public *
 * version: 0.1*
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.1
 * @since 0.1
 * @copyright 2015-2017
 */
/**
 * Mysql数据库操作方法
 * @access public
 * @param string $connect_name 链接名
 * @return object
 */
function Mysql($connect_name=null)
{
    /**
     * 调用Mysql数据库核心包
     */
    $_dao = new \Origin\Kernel\Data\Mysql($connect_name);
    $_dao->__setSQL($_dao);
    return $_dao;
}
/**
 * Redis数据库操作方法
 * @access public
 * @param string $connect_name 链接名
 * @return object
 */
function Redis($connect_name=null)
{
    /**
     * 调用Redis数据库核心包
     */
    $_dao = new \Origin\Kernel\Data\Redis($connect_name);
    $_dao->__setSQL($_dao);
    return $_dao;
}
/**
 * MongoDB数据库操作方法
 * @access public
 * @param string $connect_name 链接名
 * @return object
 */
function Mongodb($connect_name=null)
{
    $_dao = new \Origin\Kernel\Data\Mongodb($connect_name);
    $_dao->__setSQL($_dao);
    return $_dao;
}
/**
 * Input表单提交信息请求方法函数
 * @access public
 * @param string $key
 * @param mixed $default
 * @return string
 */
function Input($key, $default = null)
{
    # 直接调用Request请求器函数
    return Request($key, $default);
}

/**
 * fishhook(鱼钩)钩子调用插件及公共组件方法，也可以用于调用新增公共控制器文件，或者函数包
 * @access public
 * @param $guide
 * @param $throws
 * @return mixed
 */
function J($guide, $throws = 'enable')
{
    /**
     * 使用正则表达式对文件引导信息进行过滤
     * @var mixed $_receipt
     * @var string $_regular
     * @var string $_exception
     * @var mixed $_guide
     */
    $_receipt = null;
    # 创建引导信息验证正则表达式变量
    $_regular = '/^[^\_\W]+(\_[^\_\W]+)*(\:[^\_\W]+(\_[^\_\W]+)*)*$/';
    # 创建特例变量
    $_exception = null;
    # 验证引导信息是否符合要求
    if (preg_match($_regular, $guide) === true) {
        # 判断是否存在连接符号
        if (strpos($guide, ':')) {
            # 拆分为数组结构
            $_guide = explode(':', $guide);
            # 创建默认扩展名
            $_suffix = '.php';
            # 拼接参数信息，并判断是否存在于配置文件中
            if (Configuration('APPLICATION_' . strtoupper($_guide[0])) and Configuration(strtoupper($_guide[0]) . '_SUFFIX')) {
                $_suffix = Configuration(strtoupper($_guide[0]) . '_SUFFIX');
                $_guide[0] = str_replace('/', '', Configuration('APPLICATION_' . strtoupper($_guide[0])));
            }
            $guide = implode(':', $_guide);
            # 判断地址栏中路径信息是否不为空
            if ($_SERVER['PATH_INFO']) $_map = explode('/', $_SERVER['PATH_INFO'])[1];
            # 判断函数处理变量是否被创建
            if (isset($_map))
                # 判断获取值与默认应用文件名是否相同
                if ($_map != __APPLICATION__)
                    # 判断该值是否问应用目录
                    if (is_dir(ROOT . DS . "Apply" . $_map))
                        $_master = $_map . '/';
            # 根据执行结构获取文件路径指向信息
            $_dir = isset($_master) ? $_master : __APPLICATION__;
            # 使用钩子公共方法引入文件
            $_receipt = Loading(str_replace('/', ':', "Apply/" . $_dir . $guide), $_suffix, $throws);
        }
    }
    return $_receipt;
}

/**
 * @access public
 * @param array $page 分页数组
 * @param string $search 搜索条件
 * @param string $cols 页码数量
 * @return array
 * @contact 比较逻辑运算符双向转化方法
 */
function Number($page,$search,$cols){
    //执行数字页码
    $n=array();
    if($page['count']>$cols){
        $k=($cols%2==0)?$cols/2:($cols-1)/2;
        if(($page['current']-$k)>1 && ($page['current']+$k)<$page['count']){
            $page['num_begin']=$page['current']-$k;
            $page['num_end']=$page['current']+$k;
            for($i=$page['num_begin'];$i<=$page['num_end'];$i++){
                if($i==$page['current']){
                    array_push($n,array('page'=>$i,'url'=>$page['url'].'?page='.$i.$search));
                }else{
                    array_push($n,array('page'=>$i,'url'=>$page['url'].'?page='.$i.$search));
                }
            }
        }else{
            if(($page['current']-$k)<=1){
                $page['num_begin']=1;
                $page['num_end']=$cols;
                for($i=$page['num_begin'];$i<=$page['num_end'];$i++){
                    if($i==$page['current']){
                        array_push($n,array('page'=>$i,'url'=>$page['url'].'?page='.$i.$search));
                    }else{
                        array_push($n,array('page'=>$i,'url'=>$page['url'].'?page='.$i.$search));
                    }
                }
            }elseif(($page['current']+$k)>=$page['count']){
                $page['num_begin']=$page['count']-($cols-1);
                $page['num_end']=$page['count'];
                for($i=$page['num_begin'];$i<=$page['num_end'];$i++){
                    if($i==$page['current']){
                        array_push($n,array('page'=>$i,'url'=>$page['url'].'?page='.$i.$search));
                    }else{
                        array_push($n,array('page'=>$i,'url'=>$page['url'].'?page='.$i.$search));
                    }
                }
            }else{
                $page['num_begin']=1;
                $page['num_end']=$cols;
                for($i=$page['num_begin'];$i<=$page['num_end'];$i++){
                    if($i==$page['current']){
                        array_push($n,array('page'=>$i,'url'=>$page['url'].'?page='.$i.$search));
                    }else{
                        array_push($n,array('page'=>$i,'url'=>$page['url'].'?page='.$i.$search));
                    }
                }
            }
        }
    }else{
        for($i=1;$i<=$page['count'];$i++){
            if($i==$page['current']){
                array_push($n,array('page'=>$i,'url'=>$page['url'].'?page='.$i.$search));
            }else{
                array_push($n,array('page'=>$i,'url'=>$page['url'].'?page='.$i.$search));
            }
        }
    }
    return $n;
}
/**
 * @access public
 * @param string $url 链接
 * @param string $count 总数
 * @param string $current 当前页
 * @param string $row 分页大小
 * @param string $search 搜索条件
 * @return array
 * @contact 比较逻辑运算符双向转化方法
 */
function Page($url,$count,$current,$row,$search){
    $page=array(
        'url'=>$url,
        'size'=>intval($row),'num_begin'=>0,'num_end'=>0,'count'=>0,'limit'=>0,'current'=>1,//翻页基本参数
        'first_url'=>'','first'=>0,//第一页参数
        'last_url'=>'','last'=>0,//上一页参数
        'next_url'=>'','next'=>0,//下一页参数
        'end_url'=>'','end'=>0,//最后一页参数
    );
    $page['current']=intval($current);
    $page['count']=$count%$page['size']!=0?intval(($count/$page['size'])+1):intval($count/$page['size']);
    //判断页标状态
    if($page['current']<=0) $page['current']=1;
    if($page['current']>$page['count']) $page['current']=$page['count'];
    if($page['count']<=0) $page['current']=$page['count']=1;
    $page['limit']=$page['size']*($page['current']-1);//其实点运算
    $page['page_one']=$page['limit']+1;
    $page['page_end']=($page['limit']+$page['size'])>$count?$count:$page['limit']+$page['size'];
    //判断翻页状态1
    if($page['current']>1){
        $page['last']=$page['current']-1;
    }else{
        $page['last']=1;
    }

    //判断翻页状态2
    if($page['current']>=$page['count']){
        $page['next']=$page['count'];
    }else{
        $page['next']=$page['current']+1;
    }
    $page['first_url']=$page['url'].'?page=1'.$search;//第一页
    $page['last_url']=$page['url'].'?page='.$page['last'].$search;//上一页
    $page['next_url']=$page['url'].'?page='.$page['next'].$search;//下一页
    $page['end_url']=$page['url'].'?page='.$page['count'].$search;//最后一页
    return $page;
}

/**
 * Verify验证函数
 * @param $width
 * @param $height
 * @return object
 */
function Verify($width = 120, $height = 50)
{
    return new \Origin\Kernel\Export\Verify($width, $height);
}