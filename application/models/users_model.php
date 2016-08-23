<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// 会员表模型

class Users_model extends CI_Model {

	function __construct()
    {
        parent :: __construct();		
    }
	
	/**
	 * 通过uid 获取会员信息
	 * @param int $uid
	 * @return array
	 */
	function get_one($uid) 
	{		
		$return = array();
		$uid = intval($uid);
		$sql = "select * from jia_users where uid='$uid' limit 1";
		$query = $this->db->query($sql);
		$return = $query->row_array();
		
		return $return;
	}
	
	/**
	 * 通过uid更新 会员信息
	 * @param array $data
	 * @param int $uid
	 */
	function update($data, $uid) 
	{		
		$uid = intval($uid);
		$this->db->update('jia_users', $data, 'uid = '.$uid);
	}
	
	/**
	 * 获取 treeid 
	 * @param string $string
	 * @param int 
	 */
	function get_treeid($string) 
	{		
		if ($string == '') {
			return 0;			
		} elseif (strpos($string, ',')) {
			return substr($string, 0, strpos($string, ','));
		} else {
			return $string;
		}
	}
	
	/**
	 * 获取 家谱名 
	 * @param id $treeid
	 * @param string 
	 */
	function get_treename($treeid) 
	{		
		if (intval($treeid) ==0) return '';
		
		$treeid = intval($treeid);		
		$query = $this->db->query("select title from jia_tree where id='$treeid' limit 1");
		$row = $query->row_array();
		return $row['title'];
	}
	
	
	/**
	 * 审核通过 添加treeid 到 view_treeid 字段
	 * @param int $treeid
	 * @param int $uid
	 * @
	 */
	function add_view_treeid($treeid, $uid) 
	{
		$users = $this->get_one($uid);
		$data = array(
				'view_treeid'=> $users['view_treeid'] ? $users['view_treeid'].','.$treeid : $treeid
				);
		$this->update($data, $uid);
		
		$this->session->set_userdata($data);
	}
	
	
}

/* End of file */
