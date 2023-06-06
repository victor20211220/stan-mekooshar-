<?php

/**
 * Kit.
 *
 * Dummy Request library.
 *
 * @version  $Id: request.php 2 2009-10-07 01:47:16Z eprev $
 * @package  System
 */

class Request extends System_Request {

	public static function generateUri($controller = false, $action = false, $params = false, $getParams = false)
	{
		$uri = self::$protocol . '://' . self::$host . '/';

		if( !$controller )
		{
		$controller = !empty ( self::$controller ) ? ltrim(self::$controller, '/') : '';
		}
		else
		{
		$controller .= '/';
		}

		if( !$action )
		{
		$action = !empty ( self::$action ) ? self::$action . '/' : '';
		}
		else
		{
		$action .= '/';
		}

		$paramsUri = '';
		if( !empty ( $params ) )
		{
			if($params === true)
			{
				$params = self::getParams();
			}

			if(is_array( $params ) ) {
				$paramsUri = implode('/', $params);
			} else {
				$paramsUri =  $params;
			}
			$paramsUri = trim($paramsUri, '/') . '/';
		}

		if( empty ($paramsUri) && $action == 'index/' ) {
			$action = '';
		}

		$uri .= $controller . $action . $paramsUri;

		if( $uri[strlen($uri)-1] != '/' )
		{
			$uri .= '/';
		}

		$getParamsUri = '';
		if( !empty ($getParams) )
		{
			if( $getParams === true )
			{
				$getParams = self::getGetParams();
			}

			if(is_array( $getParams ) )
			{
				$getParamsUri = http_build_query($getParams);
			}
			else
			{
				$getParamsUri = $getParams;
			}
		}

		$uri .= !empty($getParamsUri) ? '?' . $getParamsUri : '';

		return $uri;
	}

	public static function getQuery($params = false, $value = 1)
	{
		$curentParams = $_GET;
		if($params !== false) {
			if(is_array($params)) {
				$curentParams = array_merge($curentParams, $params);
			} else {
				$curentParams[$params] = $value;
			}
		}

		return '?' .http_build_query($curentParams);
	}

	public static function getQueryWithoutKeys($params = false, $value = 1, $key = false)
	{
		$curentParams = $_GET;
		if($params !== false) {
			if(is_array($params)) {
				$curentParams = array_merge($curentParams, $params);
			} else {
				$curentParams[$params] = $value;
			}
		}

		if($key !== false) {
			if(is_array($key)) {
				foreach($key as $k){
					unset($curentParams[$k]);
				}
			} else {
				unset($curentParams[$key]);
			}
		}

		return '?' .http_build_query($curentParams);
	}
};
