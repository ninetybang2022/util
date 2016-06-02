<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/2/002
 * Time: 21:09
 */
class Router
{

    private static $ruleTable = array(
        '(:num)'=>'/\d+/',
        '(:any)'=>'/\S+/'
    );
    public static $ruleType = array('post','get');
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
     */
    private static function matchRule($pathinfo,$pathinfoToArray,$_pathinfo)
    {
        $count = 'm_'.count($pathinfoToArray);
        $type  = static::getMethodType();
        $countArray = static::$rules[$type][$count];
        //如果存在 /article 类似这样的直接返回
        if(isset($countArray[$_pathinfo]))
        {
            return call_user_func($countArray[$_pathinfo]);
        }
        //匹配规则
        array_walk($countArray,function($func,$rule) use($pathinfoToArray,$_pathinfo,$pathinfo){
            $ruleArray = explode('/',$rule);
            array_shift($ruleArray);
            $flagParam = static::eachRule($ruleArray,$pathinfoToArray);
            if($flagParam['flag'] == count($pathinfoToArray))
            {
                call_user_func_array($func,$flagParam['param']);
                return;
            }
        });
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



    //以后实现资源
    public static function resource()
    {

    }

}


//Router::get('/hello',function(){
    echo 'goodjobs';
});

// /article/1
Router::get('/article/(:num)',function($id){
    echo $id;
});

Router::get('/users/(:num)',function($id){
    echo $id;
});

Router::get('/users/get/(:num)',function(){
    echo 'aaa';
});

Router::get('/users/l100',function(){
    echo 'aaa';
});


Router::get('/home/(:any)/article/(:num)',function($username,$id){
    echo $username,$id;
});

//Router::get('/useri')

Router::run();

