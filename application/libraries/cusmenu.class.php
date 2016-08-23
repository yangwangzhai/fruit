<?php
class customWxMenu {
	var $appID     = '';
	var $appsecret = '';
	var $menuArr   = '';
	
    function __construct ($appID,$appsecret,$menuArr) 
    {
         $this-> appID 	   = $appID;
         $this-> appsecret = $appsecret;
         $this-> menuArr   = $menuArr;
    }
	
	/**************************************************************
	 *
	 *  使用特定function对数组中所有元素做处理
	 *  @param  string  &$array     要处理的字符串
	 *  @param  string  $function   要执行的函数
	 *  @return boolean $apply_to_keys_also     是否也应用到key上
	 *  @access public
	 *
	 *************************************************************/
	function arrayRecursive(&$array, $function, $apply_to_keys_also = false) {
	    static $recursive_counter = 0;
	    if (++$recursive_counter > 1000) {
	        die('possible deep recursion attack');
	    }
	    foreach ($array as $key => $value) {
	        if (is_array($value)) {
	            self::arrayRecursive($array[$key], $function, $apply_to_keys_also);
	        } else {
	            $array[$key] = $function($value);
	        }
	  
	        if ($apply_to_keys_also && is_string($key)) {
	            $new_key = $function($key);
	            if ($new_key != $key) {
	                $array[$new_key] = $array[$key];
	                unset($array[$key]);
	            }
	        }
	    }
	    $recursive_counter--;
	}
	  
	/**************************************************************
	 *
	 *  将数组转换为JSON字符串（兼容中文）
	 *  @param  array   $array      要转换的数组
	 *  @return string      转换得到的json字符串
	 *  @access public
	 *
	 *************************************************************/
	function JSON($array) {
	    $this->arrayRecursive($array, 'urlencode', true);
	    $json = json_encode($array);
	    return urldecode($json);
	}
	
	//自定义菜单中获取access_token
	function get_access_token() {
		$url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appID."&secret=".$this->appsecret;
		$json=$this->http_request_json($url);//这个地方不能用file_get_contents
		$data=json_decode($json,true);
		if($data['access_token']){
			return $data['access_token'];
		}else{
			return "获取access_token错误";
		}		
	}
	
	//查询自定义菜单
	function getWxCusmenu() {
		$url="https://api.weixin.qq.com/cgi-bin/menu/get?access_token=".$this->get_access_token();
		$json=$this->http_request_json($url);//这个地方不能用file_get_contents
		return json_decode($json,true);		
	}
	
	//删除自定义菜单
	function delWxCusmenu() {
		$url="https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".$this->get_access_token();
		$json=$this->http_request_json($url);//这个地方不能用file_get_contents
		return json_decode($json,true);		
	}
	
	//因为url是https 所有请求不能用file_get_contents,用curl请求json 数据
	function http_request_json($url) {  
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;  
	}
	
	//按钮菜单
	function get_menu() {
		return $this->menuArr;
	}
	
	function apply() {
	        $token_str = $this->get_access_token();
	        $apply_url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$token_str}";
	        $data = $this->JSON($this->get_menu());//json化中文不变
	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, $apply_url);
	        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        $result = curl_exec($ch);
	        curl_close($ch);
	        return json_decode($result,true);
	        /*
	       	return array(
	            'msg'=>($result['errcode']==0?'同步成功':'同步失败'),
	            'state'=>$result['errcode']==0 ? 0 : 1
	        );
	        */
	}
}

//$CM = new customWxMenu('','',$menuArr);
//print_r($CM->apply());







