<?php
	/**
	 * PHP对CURL的操作
	 * TODO:下个版本实现文件上传
	 * version 0.0.1
	 */
	class CURL
	{
		private static $curl;
		public static $header;
		const CURL_INIT_FALID = -1;
		const CURL_POST = 1;
		const CURL_GET  = 2;
		
		private static function init()
		{
			static::$curl = curl_init();
			//初始化失败
			if(static::$curl === false)
			{
				return false; 
			}
			return true;
		}
		//配置 头部
		public static function setConfig($userAgent='',$timeout=15)
		{
			$userAgent = $userAgent?:'Mozilla/5.0 (Windows NT 10.0; WOW64) buka_article_server';
			if(!curl_setopt(static::$curl,CURLOPT_USERAGENT,$userAgent) 
				|| !curl_setopt(static::$curl,CURLOPT_TIMEOUT,$timeout)
			 	|| !curl_setopt(static::$curl,CURLOPT_RETURNTRANSFER,true))
			{
				return false;
			}
			return true;
		}
		//公用方法 配置 url和参数
		private static function methodCommon($url)
		{
			if(!static::init() || !static::setConfig() || !curl_setopt(static::$curl,CURLOPT_URL,$url))
			{
				return false;
			}
			return true;
		}
		//执行
		private static function execute()
		{
			$result = curl_exec(static::$curl);
			if(!$result)
			{
				return self::CURL_INIT_FALID;
			}
			return $result;
		}
		
		//get方式
		public static function get($url,$param)
		{
			$paramToBuildString = http_build_query($param);
			$url .= '?'.$paramToBuildString;
			if(!static::methodCommon($url))
			{
				return self::CURL_INIT_FALID;
			}
			return static::execute();	
		}
		
		//post方式
		public static function post($url,$param)
		{
			if(!static::methodCommon($url) || !curl_setopt(static::$curl,CURLOPT_POST,1) || !curl_setopt(static::$curl,CURLOPT_POSTFIELDS,$param))
			{
				return self::CURL_INIT_FALID;
			}
			return static::execute();
		}
		
	}
	
	/*
	 * CURL::post(url,参数);
	 * CURL::get(url,参数);
	 */ 
	 * /
	
