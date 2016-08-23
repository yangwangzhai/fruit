<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// 私信模型

class Message_model extends CI_Model 
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
		$query = $this->db->query("select * from jia_message where id='$id' limit 1");
		return $query->row_array();		
	}	
	
	
}

/* End of file */
