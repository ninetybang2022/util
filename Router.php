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
     * ���һ��get·�ɹ���
     * @param $rule ·�ɹ���
     * @param $callback �ص�
     */
    public static function get($rule,$callback)
    {
        static::setRule('get',$rule,$callback);
    }

    /**
     * ���post·��
     * @param string $rule ����
     * @param function $callback �ص�
     */
    public static function post($rule,$callback)
    {
        static::setRule('post',$rule,$callback);
    }

    /**
     * ���ص�ǰ����ʽ�ķ�ʽpost get
     * @return string
     */
    public static function getMethodType()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * ͨ�����ù���
     * @param $type post get
     * @param $rule ����
     * @param $callback �ص�
     * TODO:�Ժ����ʵ����ӿ��������� HomeController@index����
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

    //��������
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
     * ����·�ɹ���
     */
    private static function matchRule($pathinfo,$pathinfoToArray,$_pathinfo)
    {
        $count = 'm_'.count($pathinfoToArray);
        $type  = static::getMethodType();
        $countArray = static::$rules[$type][$count];
        //������� /article ����������ֱ�ӷ���
        if(isset($countArray[$_pathinfo]))
        {
            return call_user_func($countArray[$_pathinfo]);
        }
        //ƥ�����
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
     * TODO:����uri�Ժ���� �����ֳ���
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



    //�Ժ�ʵ����Դ
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

