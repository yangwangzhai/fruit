<?php if (! defined('BASEPATH'))  exit('No direct script access allowed');

class Fruit extends CI_Controller
{  
	private $url_appid = 'wxaa13dc461510723a';//'wxb22508fbae4f4ef4'; //wx5442329a3bf072a0
	private $yrurl = 'wx.thewm.com.cn';  //生产环境    wx93024a4137666ab3   wx.zhenlong.wang
    public function index ()
    {
		if(isset($_GET['test'])){
			$session_id = session_id();
			$phone_os = $_SERVER['HTTP_USER_AGENT'];
			$headurl = "http://wx.qlogo.cn/mmopen/GQfdS1CPWRJWI6Xu0Rn6mUqL3tICLeRiazbwFtr6pC3E5wxM5hM4Efw2CSo17Ow6ibPVns0otmphxY62BibVuBP4Y3743NEFkVO/0";
			$wx_info = array('openid' => 'woM0Mxs3oVcGxDn9vdeEKnL3HpdSo' . $my, 'nickname' => '测试', 'headimgurl' => $headurl, 'sex' => 1);
			$filename = 'static/wxheadimg/'.$wx_info['openid'] . '.jpg';
			$img_local_url = $this->getImg($wx_info['headimgurl'],$filename );
			$headPhoto =base_url(). $img_local_url;
			
			$data['openid'] = $wx_info['openid'];
			$data['nickname'] = $wx_info['nickname'];
			$data['headimgurl'] = $headPhoto;//$wx_info['headimgurl'];
			
			$isexit = $this->db->query("select count(*) as num,nickname,head_img, local_img, score  from zy_fruit_player where openID='".$data['openid']."' ")->row_array();

			if($isexit['num'] > 0){
				if(! file_exists($filename)  || $isexit['head_img'] != $headPhoto  ){							
					$img_local_url = $this->getImg($headPhoto,$filename );
					$headLocalPhoto =base_url(). $img_local_url;
					$data['headimgurl'] = $headLocalPhoto;
				}else{						
					$data['headimgurl'] = $isexit['local_img'] ? $isexit['local_img'] :  base_url().$filename;
				}
				$update_nickname = "";
				if($isexit['nickname'] != $nickname) $update_nickname = "  nickname='".$nickname."' , ";
				//total_gold =".$data['smokeBeansCount']."  ,			
				$this->db->query("update zy_fruit_player set {$update_nickname}  lasttime= ".time()." ,head_img = '" . $headPhoto . "' ,session_id = '" . $session_id . "',phone_os = '" . $phone_os . "' ,local_img = '" . base_url().$filename . "' where openID= '".$openid."' ");//更新烟豆
				$data['smokeBeansCount'] = $isexit['score'];	
				
			}else{
				$img_local_url = $this->getImg($headPhoto,$filename );
				$headLocalPhoto =base_url(). $img_local_url;
				
				$data['headimgurl'] = $headLocalPhoto;
				
				$user_data['openID'] =  $data['openid'];
				$user_data['nickname'] =  $data['nickname'];
				$user_data['head_img'] =  $data['headimgurl'];
				$user_data['local_img'] =  $headLocalPhoto;
				$user_data['sex'] =  0;
				$user_data['addtime'] =  time();
				$user_data['lasttime'] =  time();
				$user_data['score'] = 10000;
				$user_data['session_id'] = $session_id;	
				$user_data['phone_os'] = $phone_os;
				$insert_sql = $this->db->insert_string('zy_fruit_player',$user_data);
				$insert_sql = str_replace('INSERT', 'INSERT ignore ', $insert_sql);
				$this->db->query($insert_sql);
				$data['smokeBeansCount'] = $user_data['score'];
		
			}

		}else{
		
			$state_base64 = base64_encode('http://h5game.gxtianhai.cn/fruit/index.php?c=fruit&m=getUserInfo');
			header('Location: https://open.weixin.qq.com/connect/oauth2/authorize?appid='. $this->url_appid .'&redirect_uri=http://'. $this->yrurl .'/thirdInterface/thirdInterface!autoLogin2.action&response_type=code&scope=snsapi_base&state='.$state_base64.'#wechat_redirect');
		    exit;
		}

        $this->load->view('index', $data);
    } 
	
	function getUserInfo(){
		$session_id = session_id();
		$phone_os = $_SERVER['HTTP_USER_AGENT'];
		$openid = $_REQUEST['openid'];
		$nickname = $_REQUEST['nickName'];
		$headPhoto = $_REQUEST['headPhoto'];
		
		$wx_info = array('openid' => $openid . $my, 'nickname' => $nickname, 'headimgurl' => $headPhoto, 'sex' => 1);
		$filename = 'static/wxheadimg/'.$wx_info['openid'] . '.jpg';
		$img_local_url = $this->getImg($wx_info['headimgurl'],$filename );
		$headPhoto =base_url(). $img_local_url;
		$data['openid'] = $wx_info['openid'];
		$data['nickname'] = $wx_info['nickname'];
		$data['headimgurl'] = $headPhoto;//$wx_info['headimgurl'];
		$data['smokeBeansCount'] = 10000;
		/*$data['openid'] = $openid;
		$data['nickname'] = $nickname;					
		$data['sex'] = 0;
		
		$isexit = $this->db->query("select count(*) as num,nickname,head_img, local_img  from zy_fruit_player where openID='".$openid."' ")->row_array();

		if($isexit['num'] > 0){
			if(! file_exists($filename)  || $isexit['head_img'] != $headPhoto  ){							
				$img_local_url = $this->getImg($headPhoto,$filename );
				$headLocalPhoto =base_url(). $img_local_url;
				$data['headimgurl'] = $headLocalPhoto;
			}else{						
				$data['headimgurl'] = $isexit['local_img'] ? $isexit['local_img'] :  base_url().$filename;
			}
			$update_nickname = "";
			if($isexit['nickname'] != $nickname) $update_nickname = "  nickname='".$nickname."' , ";
			//total_gold =".$data['smokeBeansCount']."  ,			
			$this->db->query("update zy_fruit_player set {$update_nickname}  lasttime= ".time()." ,head_img = '" . $headPhoto . "' ,session_id = '" . $session_id . "',phone_os = '" . $phone_os . "' ,local_img = '" . base_url().$filename . "' where openID= '".$openid."' ");//更新烟豆
			$data['smokeBeansCount'] = $isexit['score'];		
		}else{
			$img_local_url = $this->getImg($headPhoto,$filename );
			$headLocalPhoto =base_url(). $img_local_url;
			
			$data['headimgurl'] = $headLocalPhoto;
			
			$user_data['openID'] =  $openid;
			$user_data['nickname'] =  $nickname;
			$user_data['head_img'] =  $headPhoto;
			$user_data['local_img'] =  $headLocalPhoto;
			$user_data['sex'] =  0;
			$user_data['addtime'] =  time();
			$user_data['lasttime'] =  time();
			$user_data['score'] = 10000;
			$user_data['session_id'] = $session_id;	
			$user_data['phone_os'] = $phone_os;
			$insert_sql = $this->db->insert_string('zy_fruit_player',$user_data);
			$insert_sql = str_replace('INSERT', 'INSERT ignore ', $insert_sql);
			$this->db->query($insert_sql);
			$data['smokeBeansCount'] = $user_data['score'];
	
		}*/
		
		
		
		
		
		
		$this->load->view('index', $data);
	}
	
	function post_http(){
		$post = $_POST['data'];
		echo json_encode(array('a'=>'abc','b'=>'dfe','c'=>$post));
	}
	
	function save_bet(){
		$bet_id = $this->input->post('betId');
		$game_id = $this->input->post('gameId');
		$openid = $this->input->post('openid');
		$field = 'bet' . ($bet_id + 1);
		$return = array('sum' => 0, 'game_id' => 0 );
		if($game_id > 0){			
			$this->db->query("UPDATE zy_fruit_game SET $field = $field + 1 where id=$game_id");
			$game_new_id = $game_id;
		}else{
			$data[$field] = 1;
			$data['addtime'] = time();
			$data['openid'] = $openid;
			$this->db->insert('zy_fruit_game' , $data);
			$game_new_id = $this->db->insert_id();
		}
		
		$sql = "SELECT * FROM zy_fruit_game WHERE id=$game_new_id";
		$cur_game = $this->db->query( $sql )->row_array();	
		$return['sum'] = $cur_game[$field];
		$return['game_id'] = $game_new_id;
		
		echo json_encode( $return );
		$result_arr = $this->result_arr();
		$result_rand_id = rand(1, 24);
		
		$re_data['result'] = $result_rand_id;
		$re_data['result_bs'] = $result_arr[$result_rand_id]['bs'];
		$this->db->update('zy_fruit_game' , $re_data, array('id' => $game_new_id));
		
	}
	function get_result(){
		$game_id = $this->input->get('gameId');
		$sql = "SELECT * FROM zy_fruit_game WHERE id=$game_id";
		$cur_game = $this->db->query( $sql )->row_array();	
		$result_arr = $this->result_arr();
		$coordinate = $result_arr[ $cur_game['result'] ]['coordinate'];
		$field =  $result_arr[ $cur_game['result'] ]['field'];
		$return['xhr_stop_margin'] = $coordinate[0];
		$return['xhr_stop_num'] = $coordinate[1];
		$return['result_gold'] = intval($cur_game[$field]) * intval( $cur_game['result_bs'] );
		$bet_on_sum = 0;
		for($i = 1; $i < 9; $i++){
			$bet_on_sum += intval($cur_game['bet'.$i]);			
		}
		
		//下注金额的框位置
		$bet_index = intval( str_replace('bet','',$field) ) - 1;
		$return['bet_index'] = $bet_index;
		echo json_encode( $return );
		
	}
	
	function result_arr(){
		/*  
		field 表zy_fruit_game的下注字段bet1：苹果，bet2：橙子，bet3：木瓜，bet4：铃铛，bet5：西瓜，bet6：双星，
		bet7：双七，bet8：BAR
		bs 当前图标的倍数,和背景图对应
		coordinate 当前图标的坐标
		*/
		$result_arr = array(  //旧版按APP来排的
			1 => array('field' => 'bet4', 'bs' => 15, 'coordinate' => array(1,1)),
			2 => array('field' => 'bet8', 'bs' => 50, 'coordinate' => array(1,2)),
			3 => array('field' => 'bet8', 'bs' => 120, 'coordinate' => array(1,3)),
			4 => array('field' => 'bet1', 'bs' => 5, 'coordinate' => array(1,4)),
			5 => array('field' => 'bet1', 'bs' => 3, 'coordinate' => array(1,5)),
			6 => array('field' => 'bet3', 'bs' => 10, 'coordinate' => array(1,6)),
			
			7 => array('field' => 'bet5', 'bs' => 20, 'coordinate' => array(2,1)),
			8 => array('field' => 'bet5', 'bs' => 3, 'coordinate' => array(2,2)),
			9 => array('field' => 'goodluck', 'bs' => 0, 'coordinate' => array(2,3)),
			10 => array('field' => 'bet1', 'bs' => 5, 'coordinate' => array(2,4)),
			11 => array('field' => 'bet2', 'bs' => 3, 'coordinate' => array(2,5)),
			12 => array('field' => 'bet2', 'bs' => 10, 'coordinate' => array(2,6)),
			
			13 => array('field' => 'bet4', 'bs' => 15, 'coordinate' => array(3,1)),
			14 => array('field' => 'bet7', 'bs' => 3, 'coordinate' => array(3,2)),
			15 => array('field' => 'bet7', 'bs' => 40, 'coordinate' => array(3,3)),
			16 => array('field' => 'bet1', 'bs' => 5, 'coordinate' => array(3,4)),
			17 => array('field' => 'bet3', 'bs' => 3, 'coordinate' => array(3,5)),
			18 => array('field' => 'bet3', 'bs' => 10, 'coordinate' => array(3,6)),
			
			19 => array('field' => 'bet6', 'bs' => 30, 'coordinate' => array(4,1)),
			20 => array('field' => 'bet6', 'bs' => 3, 'coordinate' => array(4,2)),
			21 => array('field' => 'goodluck', 'bs' => 0, 'coordinate' => array(4,3)),
			22 => array('field' => 'bet1', 'bs' => 5, 'coordinate' => array(4,4)),
			23 => array('field' => 'bet4', 'bs' => 3, 'coordinate' => array(4,5)),
			24 => array('field' => 'bet2', 'bs' => 10, 'coordinate' => array(4,6)),
		);
		
		$result_arr = array(  
			1 => array('field' => 'bet4', 'bs' => 20, 'coordinate' => array(1,1)),
			2 => array('field' => 'bet8', 'bs' => 40, 'coordinate' => array(1,2)),
			3 => array('field' => 'bet8', 'bs' => 2, 'coordinate' => array(1,3)),
			4 => array('field' => 'bet1', 'bs' => 5, 'coordinate' => array(1,4)),
			5 => array('field' => 'bet1', 'bs' => 2, 'coordinate' => array(1,5)),
			6 => array('field' => 'bet3', 'bs' => 15, 'coordinate' => array(1,6)),
			
			7 => array('field' => 'bet5', 'bs' => 25, 'coordinate' => array(2,1)),
			8 => array('field' => 'bet5', 'bs' => 2, 'coordinate' => array(2,2)),
			9 => array('field' => 'goodluck', 'bs' => 0, 'coordinate' => array(2,3)),
			10 => array('field' => 'bet1', 'bs' => 5, 'coordinate' => array(2,4)),
			11 => array('field' => 'bet2', 'bs' => 2, 'coordinate' => array(2,5)),
			12 => array('field' => 'bet2', 'bs' => 10, 'coordinate' => array(2,6)),
			
			13 => array('field' => 'bet4', 'bs' => 20, 'coordinate' => array(3,1)),
			14 => array('field' => 'bet7', 'bs' => 2, 'coordinate' => array(3,2)),
			15 => array('field' => 'bet7', 'bs' => 35, 'coordinate' => array(3,3)),
			16 => array('field' => 'bet1', 'bs' => 5, 'coordinate' => array(3,4)),
			17 => array('field' => 'bet3', 'bs' => 2, 'coordinate' => array(3,5)),
			18 => array('field' => 'bet3', 'bs' => 15, 'coordinate' => array(3,6)),
			
			19 => array('field' => 'bet6', 'bs' => 30, 'coordinate' => array(4,1)),
			20 => array('field' => 'bet6', 'bs' => 2, 'coordinate' => array(4,2)),
			21 => array('field' => 'goodluck', 'bs' => 0, 'coordinate' => array(4,3)),
			22 => array('field' => 'bet1', 'bs' => 5, 'coordinate' => array(4,4)),
			23 => array('field' => 'bet4', 'bs' => 2, 'coordinate' => array(4,5)),
			24 => array('field' => 'bet2', 'bs' => 10, 'coordinate' => array(4,6)),
		);
		
		return $result_arr;
				
	}
	
		/**
	 * 生成缩略图函数  剪切
	 *
	 * @param $imgurl 图片路径            
	 * @param $width 缩略图宽度            
	 * @param $height 缩略图高度            
	 * @return string 生成图片的路径 类似：./uploads/201203/img_100_80.jpg
	 */
	function thumb ($imgurl, $width = 100, $height = 100)
	{
		if (empty($imgurl))
			return '不能为空';
	
		include_once 'application/libraries/image_moo.php';
		$moo = new Image_moo();
		$moo->load($imgurl);
		$moo->resize_crop($width, $height);
		$moo->save_pa('','',true);    
	}
	
	
		/*
	*@通过curl方式获取指定的图片到本地
	*@ 完整的图片地址
	*@ 要存储的文件名
	*/
	function getImg($url = "", $filename = "")
	{
		   //去除URL连接上面可能的引号
			//$url = preg_replace( '/(?:^['"]+|['"/]+$)/', '', $url );
			$hander = curl_init();
			$fp = fopen($filename,'wb');
			curl_setopt($hander,CURLOPT_URL,$url);
			curl_setopt($hander,CURLOPT_FILE,$fp);
			curl_setopt($hander,CURLOPT_HEADER,0);
			curl_setopt($hander,CURLOPT_FOLLOWLOCATION,1);
			//curl_setopt($hander,CURLOPT_RETURNTRANSFER,false);//以数据流的方式返回数据,当为false是直接显示出来
			curl_setopt($hander,CURLOPT_TIMEOUT,60);
			curl_exec($hander);
			curl_close($hander);
			fclose($fp);
			$this->thumb($filename,82,80);
			return $filename;
	}
	
  
}
?>