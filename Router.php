<?php

/**
 * 路由类用于指定路由规则
 * @version 0.0.2
 * @author nineytbang@gmail.com
 */
class Router
{

    private static $ruleTable = [
        '(:num)'=>'/^\d+$/',
        '(:any)'=>'/^\S+$/'
    ];
    private static $err404 = ['handler'=>''];
    public static $ruleType = ['post','get'];
    public static $rules;

    /**
     * 添加一个get路由规则
     * @param $rule 路由规则
     * @param $callback 回调
     */
    public static function get($rule,$callback)
    {
        static::setRule('get',$rule,$callback);
    }

    /**
     * 添加post路由
     * @param string $rule 规则
     * @param function $callback 回调
     */
    public static function post($rule,$callback)
    {
        static::setRule('post',$rule,$callback);
    }

    /**
     * 返回当前请求方式的方式post get
     * @return string
     */
    public static function getMethodType()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * 通用配置规则
     * @param $type post get
     * @param $rule 规则
     * @param $callback 回调
     * TODO:以后可能实现添加控制器方法 HomeController@index方法
     */
    private static function setRule($type,$rule,$callback)
    {
        if(array_search($type,static::$ruleType)===false)
        {
            throw new Exception('is not suport'.$type);
        }
        if(is_string($rule) && is_callable($callback))
        {
            $count = count(explode('/',$rule))-1;
            static::$rules[$type]['m_'.$count][$rule] = $callback;
            return;
        }
        throw new Exception('is arguments error');
    }

    /**
     * 配置错误404的规则
     * @param $callback 一个回调方法或者是一个字符串
     */
    public static function setError404($callback)
    {
        if(is_string($callback) || is_callable($callback))
        {
            return static::$err404['handler'] = $callback;
        }
        throw new Exception('callback is not callable');
    }

    /**
     * 404错误
     */
    public static function err404()
    {
        header('HTTP/1.1 404 Not Found');
        header('status:404 Not Found');
        if(static::$err404['handler'])
        {
            if(is_callable(static::$err404['handler']))
            {
                call_user_func(static::$err404['handler']);
            }
            return;
        }
        die('not found 404');
    }

    //遍历规则
    public static function eachRule($ruleArray,$pathinfoToArray)
    {
        $flag = 0;
        $param = array();
        foreach($ruleArray as $key=>$val)
        {
            if($val == $pathinfoToArray[$key])
            {
                $flag++;
            }
            if(isset(static::$ruleTable[$val]))
            {
                if(preg_match(static::$ruleTable[$val],$pathinfoToArray[$key]))
                {
                    $param[] = $pathinfoToArray[$key];
                    $flag++;
                }
            }
        }
        return array('flag'=>$flag,'param'=>$param);

    }

    /**
     * 配置路由规则
     * @param String $pathinfo 路由规则
     * @param String $pathinfoToArray
     * @param String $_pathinfo
     */
    private static function matchRule($pathinfo,$pathinfoToArray,$_pathinfo)
    {
        $count = 'm_'.count($pathinfoToArray);
        $type  = static::getMethodType();
        $countArray = isset(static::$rules[$type][$count])?static::$rules[$type][$count]:false;
        if($countArray)
        {
            //如果存在 /article 类似这样的直接返回
            if(isset($countArray[$_pathinfo]))
            {
                call_user_func($countArray[$_pathinfo]);
                return;
            }
            else
            {
                //匹配规则
                foreach($countArray as $rule=>$func)
                {
                    $ruleArray = explode('/',$rule);
                    array_shift($ruleArray);
                    $flagParam = static::eachRule($ruleArray,$pathinfoToArray);
//                    var_dump($flagParam);
                    if($flagParam['flag'] == count($pathinfoToArray))
                    {
                        return call_user_func_array($func,$flagParam['param']);
                    }
                }
            }
        }
        static::err404();

    }


    /**
     * TODO:解析uri以后可能 单独分出来
     */
    private static function parseUri()
    {
        if(isset($_SERVER['PATH_INFO']))
        {
            $_pathinfo = strtolower($_SERVER['PATH_INFO']);
            $pathinfo = ltrim($_pathinfo,'/');
            $pathinfoToArray = explode('/',$pathinfo);
            static::matchRule($pathinfo,$pathinfoToArray,$_pathinfo);
            return;
        }
        throw new Exception('plase open patinfo');
    }

    public static function run()
    {
        static::parseUri();
    }


    /**
     * TODO:下个版本实现资源
     */
    public static function resource()
    {

    }

}

try{

    Router::setError404(function(){
        header('Charset:utf-8');
        echo '<h1>Sorry page not found</h1>';
    });

    Router::get('/user/(:num)',function($id){
        echo $id;
    });
    Router::get('/users/(:num)',function($id){
        echo $id;
    });
    Router::get('/article/(:num)',function($id){
        echo 'get';
        echo $id;
    });

    Router::get('/user/(:any)/(:num)',function($username,$aid){
        echo $username;
        echo '<br />';
        echo $aid;
    });
    Router::run();
}
catch(Exception $e)
{
    echo $e->getMessage();
}


