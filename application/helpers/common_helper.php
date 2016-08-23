<?php
/**
 * 常用函数，通用函数
 * by tangjian 
 */


/**
 * 字符截取 支持UTF8/GBK
 *
 * @param
 *            $string
 * @param
 *            $length
 * @param
 *            $dot
 */
function str_cut ($string, $length, $dot = '', $charset = 'utf-8')
{
    $strlen = strlen($string);
    if ($strlen <= $length)
        return $string;
    $string = str_replace(
            array(
                    ' ',
                    '&nbsp;',
                    '&amp;',
                    '&quot;',
                    '&#039;',
                    '&ldquo;',
                    '&rdquo;',
                    '&mdash;',
                    '&lt;',
                    '&gt;',
                    '&middot;',
                    '&hellip;'
            ), 
            array(
                    '∵',
                    ' ',
                    '&',
                    '"',
                    "'",
                    '"',
                    '"',
                    '—',
                    '<',
                    '>',
                    '·',
                    '…'
            ), $string);
    $strcut = '';
    if ($charset == 'utf-8') {
        $length = intval($length - strlen($dot) - $length / 3);
        $n = $tn = $noc = 0;
        while ($n < strlen($string)) {
            $t = ord($string[$n]);
            if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1;
                $n ++;
                $noc ++;
            } elseif (194 <= $t && $t <= 223) {
                $tn = 2;
                $n += 2;
                $noc += 2;
            } elseif (224 <= $t && $t <= 239) {
                $tn = 3;
                $n += 3;
                $noc += 2;
            } elseif (240 <= $t && $t <= 247) {
                $tn = 4;
                $n += 4;
                $noc += 2;
            } elseif (248 <= $t && $t <= 251) {
                $tn = 5;
                $n += 5;
                $noc += 2;
            } elseif ($t == 252 || $t == 253) {
                $tn = 6;
                $n += 6;
                $noc += 2;
            } else {
                $n ++;
            }
            if ($noc >= $length) {
                break;
            }
        }
        if ($noc > $length) {
            $n -= $tn;
        }
        $strcut = substr($string, 0, $n);
        $strcut = str_replace(
                array(
                        '∵',
                        '&',
                        '"',
                        "'",
                        '"',
                        '"',
                        '—',
                        '<',
                        '>',
                        '·',
                        '…'
                ), 
                array(
                        ' ',
                        '&amp;',
                        '&quot;',
                        '&#039;',
                        '&ldquo;',
                        '&rdquo;',
                        '&mdash;',
                        '&lt;',
                        '&gt;',
                        '&middot;',
                        '&hellip;'
                ), $strcut);
    } else {
        $dotlen = strlen($dot);
        $maxi = $length - $dotlen - 1;
        $current_str = '';
        $search_arr = array(
                '&',
                ' ',
                '"',
                "'",
                '"',
                '"',
                '—',
                '<',
                '>',
                '·',
                '…',
                '∵'
        );
        $replace_arr = array(
                '&amp;',
                '&nbsp;',
                '&quot;',
                '&#039;',
                '&ldquo;',
                '&rdquo;',
                '&mdash;',
                '&lt;',
                '&gt;',
                '&middot;',
                '&hellip;',
                ' '
        );
        $search_flip = array_flip($search_arr);
        for ($i = 0; $i < $maxi; $i ++) {
            $current_str = ord($string[$i]) > 127 ? $string[$i] . $string[++ $i] : $string[$i];
            if (in_array($current_str, $search_arr)) {
                $key = $search_flip[$current_str];
                $current_str = str_replace($search_arr[$key], 
                        $replace_arr[$key], $current_str);
            }
            $strcut .= $current_str;
        }
    }
    return $strcut . $dot;
}

/**
 * 显示信息
 *
 * @param string $message
 *            内容
 * @param string $url_forward
 *            跳转的网址
 * @param string $title
 *            标题
 * @param int $second
 *            停留的时间
 * @return
 *
 *
 *
 */
function show_msg ($message, $url_forward = '', $title = '提示信息', $second = 3)
{
    include (APPPATH . 'views/show_msg.php');
    exit();
}

/**
 * 图片上传函数
 *
 * @param
 *            string 上传文本框的名称
 * @return string 图片保存在数据库里的路径
 */
function uploadFile ($filename, $dir_name = 'image')
{
    // 有上传文件时
    if (empty($_FILES)) return '0 没有上传文件'; 
   
    $save_path = 'uploads/' .$dir_name . '/';
    $max_size = 5000 * 1024; // 最大文件大小5M
    $AllowedExtensions = array('jpg','jpeg','png','bmp','gif','3gp'); // 允许格式
    
    $file_size = $_FILES[$filename]['size'];
    if ($file_size > $max_size) {
        return '-1 文件太大了';
    }
    $Extensions = fileext($_FILES[$filename]['name']);
    if (! in_array($Extensions, $AllowedExtensions)) {
        return '-2 请上传指定格式文件';    
    }
    if (! file_exists($save_path)) { // 创建文件夹          
        mkdir($save_path);
    }   
    $save_path .= date("Ymd") . "/";
    if (! file_exists($save_path)) {
        mkdir($save_path);
    }    
    $file_name = date('YmdHis') . '_' . rand(10000, 99999) . '.' . $Extensions;
    $upload_file = $save_path . $file_name;   
    if (move_uploaded_file($_FILES[$filename]['tmp_name'], $upload_file)===false) {
        return '-3 文件上传失败';
    }
    
    return $upload_file;
}

/**
 * 生成缩略图函数
 *
 * @param $imgurl 图片路径            
 * @param $width 缩略图宽度            
 * @param $height 缩略图高度            
 * @return string 生成图片的路径 类似：./uploads/201203/img_100_80.jpg
 */
function mythumb ($imgurl, $width = 200, $height = 200)
{
    $fileext = fileext($imgurl);
    $num = strlen($imgurl) - strlen($fileext) - 1;
    $newimg = substr($imgurl, 0, $num) . "_{$width}_{$height}.{$fileext}";
    
    if (file_exists($newimg))
        return $newimg; // 有，返回
    
    if (file_exists($imgurl)) { // 没有，开始生成
        include_once APPPATH . '/libraries/My_image_class.php';
        $object = new My_image_class();
        $px = getimagesize($imgurl);
        if ($px[0] > 10) {
            $object->imageCustomSizes($imgurl, $newimg, $width, $height);
            return $newimg;
        }
    }
}

/**
 * 生成缩略图函数 用CI的
 *
 * @param $imgurl 图片路径            
 * @param $width 缩略图宽度            
 * @param $height 缩略图高度            
 * @return string 生成图片的路径 类似：./uploads/201203/img_100_80.jpg
 */
function thumb ($imgurl, $width = 200, $height = 200)
{
    if (empty($imgurl))
        return '不能为空';
    
    $save_path = dirname(__FILE__) . '/../../';   
    $imgurl = $save_path.$imgurl;    
         
    if (file_exists($imgurl)==false)  // 原图是否存在
        return '原图不存在';     
  
    $newimg = new_thumbname($imgurl, $width, $height);
    $config['image_library'] = 'gd2';
    $config['source_image'] = $imgurl;
    $config['create_thumb'] = TRUE;
    $config['maintain_ratio'] = TRUE;
    $config['thumb_marker'] = '_' . $width . '_' . $height;
    $config['width'] = $width;
    $config['height'] = $height;   
    
    $CI = &get_instance();
    $CI->load->library('image_lib');
    $CI->image_lib->initialize($config);
    $CI->image_lib->resize();
}

/**
 * 生成缩略图函数 用CI的
 *
 * @param $imgurl 图片路径
 * @param $width 缩略图宽度
 * @param $height 缩略图高度
 * @return string 生成图片的路径 类似：./uploads/201203/img_100_80.jpg
 */
function thumb_crop ($imgurl, $width = 200, $height = 200)
{
   
}

/**
 * 生成缩略图函数 用CI的  同时生成两张图片  100和720像素的
 *
 * @param $imgurl 图片路径
 * @return void
 */
function thumb2($imgurl)
{
    if (empty($imgurl))
        return '不能为空';

    include 'application/libraries/image_moo.php';
    $moo = new Image_moo();
    $moo->load($imgurl);
    $moo->resize_crop(100, 100);
    $moo->save_pa("","_100_100");
    
    $moo->resize(720, 1280);
    $moo->save_pa("","_720_720");
}

/**
 * 取得文件扩展 不包括 点
 *
 * @param $filename 文件名            
 * @return 扩展名
 */
function fileext ($filename)
{
    // 获得文件扩展名
    $temp_arr = explode(".", $filename);
    $file_ext = array_pop($temp_arr);
    $file_ext = trim($file_ext);
    $file_ext = strtolower($file_ext);
    
    return $file_ext;
}

/**
 * 返回新名词 uploads/201203/img_100_80.jpg
 *
 * @param $filename 文件名
 * @return 扩展名
 */
function new_thumbname ($imgurl,$width,$height)
{
    if(empty($imgurl)) return '';
    
    $fileext = fileext($imgurl);
    $num = strlen($imgurl) - strlen($fileext) - 1;
    $newimg = substr($imgurl, 0, $num) . "_{$width}_{$height}.{$fileext}";
    return $newimg;
}



/**
 * 获取请求ip
 *
 * @return ip地址
 */
function ip ()
{
    if (getenv('HTTP_CLIENT_IP') &&
             strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR') &&
             strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('REMOTE_ADDR') &&
             strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] &&
             strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return preg_match('/[\d\.]{7,15}/', $ip, $matches) ? $matches[0] : '';
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

/**
 * 对数据执行 trim 去左右两边空格
 * mixed $data 数组或者字符串
 *
 * @return mixed
 */
function trims ($data)
{
    if (is_array($data)) {
        foreach ($data as &$r) {
            $r = trims($r);
        }
    } else {
        $data = trim($data);
    }
    
    return $data;
}

/**
 * 时间处理
 */
function times ($time, $type = 0)
{
    if ($type == 0) {
        return date('Y-m-d', $time);
    } else {
        return date('Y-m-d H:i:s', $time);
    }
}

/**
 * 获取分类 指定id 的信息
 */
function category ($catid, $type = 'name')
{
    $a = get_cache('category');
    return $a[$catid][$type];
}

/**
 * 获取分类 指定id 的信息
 */
function getcitys ($catid, $type = 'name')
{
    $a = get_cache('citys');
    return $a[$catid][$type];
}

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
 * 取消反引用 返回经stripslashes处理过的字符串或数组 
 *
 * @param $string 需要处理的字符串或数组            
 * @return mixed
 */
function new_stripslashes ($string)
{
    if (! is_array($string))
        return stripslashes($string);
    foreach ($string as $key => $val)
        $string[$key] = new_stripslashes($val);
    return $string;
}

/**
 * 将字符串转换为数组
 *
 * @param string $data            
 * @return array
 *
 */
function string2array ($data)
{
    if ($data == '')
        return array();
    @eval("\$array = $data;");
    return $array;
}

/**
 * 将数组转换为字符串
 *
 * @param array $data            
 * @param bool $isformdata            
 * @return string
 *
 */
function array2string ($data, $isformdata = 1)
{
    if ($data == '')
        return '';
    if ($isformdata)
        $data = new_stripslashes($data);
    return (var_export($data, TRUE)); // addslashes
}

/**
 * 得到子级 id 包括自己
 *
 * @param
 *            int
 * @return string
 *
 */
function get_child ($myid)
{
    $ret = $myid;
    $data = get_cache('category');
    foreach ($data as $id => $a) {
        if ($a['parentid'] == $myid) {
            $ret .= ',' . $id;
        }
    }
    
    return $ret;
}

/**
 * 得到子级 id 包括自己
 *
 * @param
 *            int
 * @return array
 *
 */
function get_childarray ($myid)
{
    $return = array();
    $data = get_cache('category');
    foreach ($data as $id => $a) {
        if ($a['parentid'] == $myid) {
            $return[$id] = $a;
        }
    }
    
    return $return;
}

// 获取限制条件 返回数组
function getwheres ($intkeys, $strkeys, $randkeys, $likekeys, $pre = '')
{
    $wherearr = array();
    $urls = array();
    
    foreach ($intkeys as $var) {
        $value = isset($_GET[$var]) ? stripsearchkey($_GET[$var]) : '';
        if (strlen($value)) {
            $wherearr[] = "{$pre}{$var}='" . intval($value) . "'";
            $urls[] = "$var=$value";
        }
    }
    
    foreach ($strkeys as $var) {
        $value = isset($_GET[$var]) ? stripsearchkey($_GET[$var]) : '';
        if (strlen($value)) {
            $wherearr[] = "{$pre}{$var}='$value'";
            $urls[] = "$var=" . rawurlencode($value);
        }
    }
    
    foreach ($randkeys as $vars) {
        $value1 = isset($_GET[$vars[1] . '1']) ? $vars[0]($_GET[$vars[1] . '1']) : '';
        $value2 = isset($_GET[$vars[1] . '2']) ? $vars[0]($_GET[$vars[1] . '2']) : '';
        if ($value1) {
            $wherearr[] = "{$pre}{$vars[1]}>='$value1'";
            $urls[] = "{$vars[1]}1=" . rawurlencode($_GET[$vars[1] . '1']);
        }
        if ($value2) {
            $wherearr[] = "{$pre}{$vars[1]}<='$value2'";
            $urls[] = "{$vars[1]}2=" . rawurlencode($_GET[$vars[1] . '2']);
        }
    }
    
    foreach ($likekeys as $var) {
        $value = isset($_GET[$var]) ? stripsearchkey($_GET[$var]) : '';
        if (strlen($value) > 1) {
            $wherearr[] = "{$pre}{$var} LIKE BINARY '%$value%'";
            $urls[] = "$var=" . rawurlencode($value);
        }
    }
    
    return array(
            'wherearr' => $wherearr,
            'urls' => $urls
    );
}

// 获取下拉框 选项信息
function getSelect ($data, $value = '', $type = 'key')
{
    $str = '';
    foreach ($data as $k => $v) {
        if ($type == 'key') {
            $seled = ($value == $k && $value) ? 'selected="selected"' : '';
            $str .= "<option value=\"{$k}\" {$seled}>{$v}</option>";
        } else {
            $seled = ($value == $v && $value) ? 'selected="selected"' : '';
            $str .= "<option value=\"{$v}\" {$seled}>{$v}</option>";
        }
    }
    return $str;
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

// 获取会员信息 单条
function getMember($id) {
    $CI = &get_instance();
    $query = $CI->db->query("select * from maps_member where id=$id limit 1");
    return $query->row_array();
}

// 获取会员信息 昵称 单条
function getNickName($uid) { 
    if(empty($uid)) return "";
    $CI = &get_instance();
    $query = $CI->db->query("select nickname from maps_member where id=$uid limit 1");
    $user = $query->row_array();
    return $user['nickname'];
}

// 获取会员信息 昵称 多条
function getAdminName($array) {
    if(empty($array)) return array();
    
    $str_ids = implode(",", $array); 
    $CI = &get_instance();
    $query = $CI->db->query("select id,truename,username from maps_admin where id in($str_ids)");   
    return $query->result_array();
}

// 数据加上会员信息， 头像 昵称，返回二维数组
function addMember($list) { 
    foreach($list as $row) {
        if(!empty($row['uid'])) $ids[] = $row['uid'];
    }
    
    // 获取 注册会员的昵称
    $memberlist = getAdminName($ids);
   
    foreach($list as &$row) {      
        foreach($memberlist as $member) {
            if($row['uid'] == $member[id]) {
                $row['username'] = $member[username];
                $row['truename'] = $member[truename];
            }
        }
        
    }
    
    return $list;
}

//回复内容
function getReplyList($repid,$rtype='guestbook',$isadm=0) {
     if(is_array($repid)) {
     	$idsql = "and repid in('".implode("','",$repid)."')";
     } else {
     	$idsql = "and repid='{$repid}'";
     }
     $CI = &get_instance();
     $query = $CI->db->query("select id,repid,uid,content,addtime from maps_recontent where rtype='{$rtype}' {$idsql} order by id asc");
     $list = $query->result_array();
     $data['list'] = addMember($list);
     //print_r($data['list']);
     $strArr = array();
     foreach($data['list'] as &$value) {       
        $time = timeFromNow($value[addtime]);  
        if($isadm) {    
        	$strArr[$value[repid]].= "<div id=\"reply-item-{$value[id]}\">{$value[truename]}回复：{$value[content]} &nbsp;&nbsp;{$time} <a href=\"javascript:delReply({$value[id]},'{$rtype}');\">删除</a></div>"; 
        } else {
        	$strArr[$value[repid]].= "<div>{$value[truename]}回复：{$value[content]} &nbsp;&nbsp;{$time}</div>"; 
        }      
     }	
    
     return $strArr;
}

//图文item
function getRuleNewsItem($ruleid) {
     if(is_array($ruleid)) {
     	$idsql = "ruleid in('".implode("','",$ruleid)."')";
     } else {
     	$idsql = "ruleid='{$ruleid}'";
     }
     $CI = &get_instance();
     $query = $CI->db->query("select * from maps_wxrules_news where {$idsql} order by id asc");
     $list = $query->result_array();

     $strArr = array();
     foreach($list as &$value) {       
        $value['time'] = timeFromNow($value[addtime]);        
        $strArr[$value["ruleid"]][] = $value;
     }	
     return $strArr;
}

	//curl
	function getDataFromCurl($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1');
		$data = curl_exec($ch);
		$status = curl_getinfo($ch);
		$errno = curl_errno($ch);
		curl_close($ch);
		return $data;
	}
?>
