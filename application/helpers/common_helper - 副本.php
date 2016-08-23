<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 常用函数，通用函数
 * tangjian 
 */
 
 /**
 * 后去加密后的 字符
 *
 * @param
 *            string
 * @return string
 */
function get_password ($password)
{
    return md5('gfdgd5454_' . $password);
}

/**
 * 字符截取 支持UTF8/GBK
 * @param $string
 * @param $length
 * @param $dot
 */
function str_cut($string, $length, $dot = '', $charset = 'utf-8') 
{
	$strlen = strlen($string);
	if($strlen <= $length) return $string;
	$string = str_replace(array(' ','&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array('∵',' ', '&', '"', "'", '"', '"', '—', '<', '>', '·', '…'), $string);
	$strcut = '';
	if($charset == 'utf-8') {
		$length = intval($length-strlen($dot)-$length/3);
		$n = $tn = $noc = 0;
		while($n < strlen($string)) {
			$t = ord($string[$n]);
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1; $n++; $noc++;
			} elseif(194 <= $t && $t <= 223) {
				$tn = 2; $n += 2; $noc += 2;
			} elseif(224 <= $t && $t <= 239) {
				$tn = 3; $n += 3; $noc += 2;
			} elseif(240 <= $t && $t <= 247) {
				$tn = 4; $n += 4; $noc += 2;
			} elseif(248 <= $t && $t <= 251) {
				$tn = 5; $n += 5; $noc += 2;
			} elseif($t == 252 || $t == 253) {
				$tn = 6; $n += 6; $noc += 2;
			} else {
				$n++;
			}
			if($noc >= $length) {
				break;
			}
		}
		if($noc > $length) {
			$n -= $tn;
		}
		$strcut = substr($string, 0, $n);
		$strcut = str_replace(array('∵', '&', '"', "'", '"', '"', '—', '<', '>', '·', '…'), array(' ', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), $strcut);
	} else {
		$dotlen = strlen($dot);
		$maxi = $length - $dotlen - 1;
		$current_str = '';
		$search_arr = array('&',' ', '"', "'", '"', '"', '—', '<', '>', '·', '…','∵');
		$replace_arr = array('&amp;','&nbsp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;',' ');
		$search_flip = array_flip($search_arr);
		for ($i = 0; $i < $maxi; $i++) {
			$current_str = ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
			if (in_array($current_str, $search_arr)) {
				$key = $search_flip[$current_str];
				$current_str = str_replace($search_arr[$key], $replace_arr[$key], $current_str);
			}
			$strcut .= $current_str;
		}
	}
	return $strcut.$dot;
}


/**
 * 显示信息
 * @param string $message  内容
 * @param string $url_forward  跳转的网址
 * @param string $title  标题
 * @param int $second  停留的时间
 * @return 
 */
function show_msg($message, $url_forward='', $title='提示信息', $second=3)
{			
	include(APPPATH.'views/show_msg.php');
	exit;
}


/**
 * 生成缩略图函数
 * @param  $imgurl 图片路径
 * @param  $width  缩略图宽度
 * @param  $height 缩略图高度
 * @return string  生成图片的路径 类似：uploads/201203/imgnamexxxx_100_80.jpg
 */
function thumb($imgurl, $width = 100, $height = 100) 
{	
	$array = explode('.', $imgurl);
	$newimg = "$array[0]_{$width}_{$height}.$array[1]";
	
	if (file_exists($newimg)) return $newimg;  // 有缩略图了，返回	
	
	if (file_exists($imgurl)) {   // 没有缩略图，开始生成		
		include_once APPPATH.'/libraries/My_image_class.php';
		$object = new My_image_class();	
		$px = getimagesize($imgurl);
		if($px[0] > 10) {		
			$object->imageCustomSizes($imgurl , $newimg , $width , $height);
			return $newimg;
		}
	}
}


/**
 * 取得文件扩展 不包括 点
 *
 * @param $filename 文件名
 * @return 扩展名
 */
function fileext($filename) {
	return strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
}


/**
 * 获取请求ip
 *
 * @return ip地址
 */
function ip() {
	if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
		$ip = getenv('HTTP_CLIENT_IP');
	} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
		$ip = getenv('HTTP_X_FORWARDED_FOR');
	} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
		$ip = getenv('REMOTE_ADDR');
	} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
}

/**
 *	图片上传函数
 *
 * @param string    上传文本框的名称
 * @return string   图片保存在数据库里的路径
 */
function uploadFile($filename){
		
	$attachment_dir = "uploads/".date('Ym')."/";
	!is_dir($attachment_dir) && mkdir($attachment_dir);	
	$AllowedExtensions = array('bmp','gif','jpeg','jpg','png');
	$Extensions = end(explode(".",$_FILES[$filename]['name']));	
	if(!in_array(strtolower($Extensions),$AllowedExtensions)){
		exit("<script>alert('缩略图格式错误！只支持后缀名为bmp,gif,jpeg,jpg,png 的文件');window.history.go(-1)</script>");
	}

	$file_name = date('YmdHis').'_'.rand(10,99).'.'.$Extensions;
	$upload_file = $attachment_dir.$file_name;
	$upload_absolute_file = $upload_file;
	if (move_uploaded_file($_FILES[$filename]['tmp_name'], $upload_absolute_file)) {		
		return $upload_file;
	} else {
		echo ("<script>alert('图片上传失败！');window.history.go(-1)</script>");
	}
}

// 创建文件
function mk_dir($dir, $mode = 0755)
{
  if (is_dir($dir) || @mkdir($dir,$mode)) return true;
  if (!mk_dir(dirname($dir),$mode)) return false;
  return @mkdir($dir,$mode);
}

/**
 * js escape php 实现
 * @param $string           the sting want to be escaped
 * @param $in_encoding
 * @param $out_encoding
 */
function escape($string, $in_encoding = 'UTF-8',$out_encoding = 'UCS-2') {
    $return = '';
    if (function_exists('mb_get_info')) {
        for($x = 0; $x < mb_strlen ( $string, $in_encoding ); $x ++) {
            $str = mb_substr ( $string, $x, 1, $in_encoding );
            if (strlen ( $str ) > 1) { // 多字节字符
                $return .= '%u' . strtoupper ( bin2hex ( mb_convert_encoding ( $str, $out_encoding, $in_encoding ) ) );
            } else {
                $return .= '%' . strtoupper ( bin2hex ( $str ) );
            }
        }
    }
    return $return;
}



/**
 * 写入缓存
 * $name 文件名
 * $data 数据数组
 *
 * @return ip地址
 */
function set_cache ($name, $data)
{

    // 检查目录写权限
    if (@is_writable(APPPATH . 'cache/') === false) {
        return false;
    }
    file_put_contents(APPPATH . 'cache/' . $name . '.php',
    '<?php return ' . var_export($data, TRUE) . ';');
    return true;
}

/**
 * 获取缓存
 * $name 文件名
 *
 * @return array
 */
function get_cache ($name)
{
    $ret = array();
    $filename = APPPATH . 'cache/' . $name . '.php';
    if (file_exists($filename)) {
        $ret = include $filename;
    }

    return $ret;
}

// 显示友好的时间格式
function timeFromNow($dateline) {
    if(empty($dateline)) return false;
    $seconds = time() - $dateline;
    if ($seconds < 60) {
        return "1分钟前";
    }elseif($seconds < 3600){
        return floor($seconds/60)."分钟前";
    }elseif($seconds  < 24*3600){
        return floor($seconds/3600)."小时前";
    }elseif($seconds < 48*3600){
        return date("昨天 H:i", $dateline)."";
    }else{
        return date('m-d', $dateline);
    }
}
 
?>

