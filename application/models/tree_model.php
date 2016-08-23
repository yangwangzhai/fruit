<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tree_model extends CI_Model {

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
		$row = array();
		$id = intval($id);
		$sql = "select * from jia_tree where id='$id' limit 1";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		
		return $row;
	}	
	
	
}

/* End of file */
