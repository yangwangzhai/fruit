<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Person_model extends CI_Model 
{

	function __construct()
    {
        parent :: __construct();		
    }
	
	/**
	 * 获取成员信息， 主表和副表的一起
	 * @param int $uid
	 * @return array
	 */
	function get_one($uid) 
	{		
		$return = array();
		$sql = "select * from jia_person a,jia_person_detail b where a.id=b.uid and a.id=$uid limit 1";
		$query = $this->db->query($sql);
		$return = $query->row_array();
		
		return $return;
	}
	
	/**
	 * 根据主表字段插入信息，主表和副表一起, 返回新插入的ID
	 * @param array $data  name, treeid ...
	 * @return id
	 */
	function add_one($data) 
	{		
		$returnid = 0;
		if ( ! $data['name'] OR ! $data['treeid']) return 0;
		
		$this->db->insert('jia_person', $data);
		$returnid = $this->db->insert_id();  
		
		$this->db->insert('jia_person_detail', array('uid'=>$returnid));
			
		return $returnid;
	}
	
	/**
	 * 插入主表，返回id
	 * 
	 * @param array $person  点击的这个人的信息
	 * @param array $post   表单信息
	 * @return id
	 */
	function add_person($person, $post)
	{		
		$return_uid = 0;
		$tree = array(			
				'name' => htmlspecialchars(trim($post['name'])),
				'sex' => $post['sex'],
				'death' => intval($post['death']),
				'treeid' => $person['treeid']
				);
		
		switch ($post['type']) {
			case '配偶' : 
				$tree['pid'] = $person['pid'];
				$tree['mainid'] = $person['id'];
				$this->db->insert('jia_person', $tree); 
				$return_uid = $this->db->insert_id();  // 返回的ID	
				break;	
				
			case '子女' : 
				// 子女父id 只能用主父id
				$tree['pid'] = $person['mainid'] ? $person['mainid'] : $person['id'];				 
				$this->db->insert('jia_person', $tree);
				$return_uid = $this->db->insert_id();  // 返回的ID
				break;
						
			case '兄妹' : 				
				$tree['pid'] = $person['pid'];
				$this->db->insert('jia_person', $tree);
				$return_uid = $this->db->insert_id();  // 返回的ID
				break;
				
			case '父母' : 
				if ($person['pid'] == 0) {  // 如果还没有添加父母的
					$tree['pid'] = 0;			
					$this->db->insert('jia_person', $tree);
					$return_uid = $this->db->insert_id();  // 返回的ID
								
					// 更新自己的 父id
					$this->db->query("update jia_person set pid=$return_uid where pid=0 and treeid=$person[treeid] and id!=$return_uid");					
					
				} else {  // 已经有父母的，后面添加的都为过门的 不管男女
					$father = $this->get_one($person['pid']);
					$tree['pid'] = $father['pid'];
					$tree['mainid'] = $person['pid'];
					$this->db->insert('jia_person', $tree); 
					$return_uid = $this->db->insert_id();  // 返回的ID		
				}		
				break;				
			
		}
		
		return $return_uid;
	}	
	
	
	/**
	 * 上传图片，返回路径的字符串  ./uploads/3434343.jpg	
	 * @param string $filename
	 * @return string
	 */
	function upload_pic($filename)
	{
		$save_path = 'uploads/';		
		$save_path .= date("Ym") . "/";		
		if (!file_exists($save_path)) {
			mkdir($save_path);
		}
		$config['upload_path'] = $save_path;
		$config['allowed_types'] = 'gif|jpg|jpeg|png|bmp';
		$config['max_size'] = '2000';
		$config['max_width'] = '1024';
		$config['max_height'] = '768';
		$config['encrypt_name'] = TRUE;
		$this->load->library('upload', $config);
		
		if ( ! $this->upload->do_upload($filename)) {
	   		$error = $this->upload->display_errors();
			show_msg($error,'','图片上传错误');
	    } 
		
		$retdata = $this->upload->data();
		$retfile = $config['upload_path'].$retdata['file_name'];
		
		return $retfile;		
	}
	
	
	/**
	 * 根据家谱ID 获取成员
	 * @param int $treeid  家谱id
	 * @param int $pid     父id
	 * @param int $level  
	 * @return string html
	 */
	function get_tree($treeid, $pid = 0, $level = 0) 
	{			
		$strs = $level_nbsp = $temp = '';	
			
		$query = $this->db->query("SELECT * FROM jia_person WHERE pid=$pid AND treeid=$treeid AND mainid=0");
		$category_arr = $query->result_array();
		
		if ($level && count($category_arr)>0) {
			$strs .= "<ul>";
		}
		$level++;
		
		foreach ( $category_arr as $category ) {
			$id = $category['id'];
			$name = $category['name'];
			$sexclass = $category['sex'] ? 'blue' : 'red';	
			// 有配偶的			
			$query = $this->db->query("select * from jia_person WHERE mainid=$id limit 1");
			$spouse = $query->row_array();
			
			if ($spouse) {
				$sexclass2 = $spouse['sex'] ? 'blue' : 'red';
				$strs .= "<li><a href=\"#\" id=\"$id\" class=\"$sexclass\">$name</a>(<a href=\"#\" id=\"$spouse[id]\" class=\"$sexclass2\">$spouse[name]</a>)";	
			} else {
				$strs .= "<li><a href=\"#\" id=\"$id\" class=\"$sexclass\">$name</a>";
			}
			$strs .= $this->get_tree($treeid, $id, $level);	
		}
		
		if (count($category_arr)>0) {
			$strs .= "</li></ul>";
		}
		
		return $strs;
	}

}

/* End of file */