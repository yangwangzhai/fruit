<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// 照片模型

class Photo_model extends CI_Model 
{

	function __construct()
    {
        parent :: __construct();		
    }
	
	/**
	 * 通过id 获取单条信息
	 * @param int $id
	 * @return array
	 */
	function get_one($id) 
	{		
		$query = $this->db->query("select * from jia_photo where id='$id' limit 1");
		return $query->row_array();		
	}
	
	/**
	 * 生成缩略图， 返回缩略图，按比例缩小，不剪切
	 * @param string $img  图片路径
	 * @param int $width   图片宽度
	 * @param int $height  图片高度
	 * @return string
	 */
	function resize_img($imgurl, $width = 500, $height = 500) 
	{		
		$array = explode('.', $imgurl);
		$newimg = "$array[0]_{$width}_{$height}.$array[1]";
		
		if (file_exists($newimg)) return $newimg;  // 有缩略图了，返回		
		
		if (file_exists($imgurl)) {   // 没有缩略图，开始生成			
			$px = getimagesize($imgurl);
			
			if($px[0] > $width || $px[1] > $height) {		
				$config['image_library'] = 'gd2';
				$config['thumb_marker'] = '';
				$config['source_image'] = $imgurl;
				$config['new_image'] = $newimg;		
				$config['create_thumb'] = TRUE;
				$config['maintain_ratio'] = TRUE;
				$config['width'] = $width;
				$config['height'] = $height;
				$this->load->library('image_lib', $config);		
				$this->image_lib->resize();
				return $newimg;
			} else {
				return $imgurl;
			}
		}
		
			
	}		
	
	
}

/* End of file */
