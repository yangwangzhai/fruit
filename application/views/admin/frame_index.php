<!DOCTYPE html>
<html>
<head>
<title>【<?=TITLE?>】后台管理中心</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<link rel="stylesheet" href="static/admin_img/admincp.css?1"
	type="text/css" media="all" />
<script type="text/javascript" src="static/js/jquery-1.7.1.min.js"></script>

<script type="text/javascript">
$(document).ready(function(){
    $('.nav li').click(function(){
    	$('.nav li').removeClass();
    	$(this).addClass("tabon");
    	$(".frame_left > ul").hide().eq($('.nav li').index(this)).show();
    });
    
    $('.frame_left a').click(function(){
    	$('.frame_left a').removeClass();			
    	$(this).addClass("on");		
        });

/*$("#leftdaaa").animate({ 
    width: "10px",
    height: "100%", 
    fontSize: "10em", 
    borderWidth: 10
}, 1000 );
*/
});
</script>
<style>
html,body {
	width: 100%;
	height: 100%;
	overflow: hidden;
}
</style>

</head>
<body scroll="no">
	<div class="mainhd">
		<div class="logo">
			<img src="./static/admin_img/logo.png">
		</div>
		<div class="nav">
			<ul>
				<li class="tabon"><a href="#">内容管理</a></li>
                <?php if($_SESSION['GroupID']==1){?>
					<li><a href="#">系统管理</a></li>
                <?php }?>
			</ul>
		</div>
		<div class="uinfo">
			<p>
				欢迎您, <em><?php echo $_SESSION['TrueName']?$_SESSION['TrueName']:$_SESSION['Username'];?></em> 
               <a href="/cpi.php?d=admin&c=admin&m=index&m=edit&id=<?=$this->session->userdata('UID')?>" target="main">个人资料</a>
               <a href="cpi.php?c=page&m=def" target="_blank">首页预览</a>
                <a href="cpi.php?d=admin&c=common&m=login_out" target="_top">退出</a>
			</p>

		</div>
	</div>
    
	<table cellpadding="0" cellspacing="0" width="100%" height="100%">
		<tr>
			<td valign="top" width="160"
				style="background: #F2F9FD; width: 160px; padding-top: 15px;">
				<div class="frame_left">
					<ul>
                    	<li><a href="index.php?d=admin&c=sets" target="main">▪首页公告</a></li>
						
					</ul>
					
 					<ul style="display: none;">				
						<li><a href="index.php?d=admin&c=admin&m=index" target="main"> ▪用户管理</a></li>
                        <!--<li><a href="./cpi.php?d=admin&c=sets&m=index" target="main"> ▪系统设置</a></li>-->	
					</ul>                   
				</div>
			</td>

			<td valign="top" height="100%"><iframe
					src="./index.php?d=admin&c=notice" name="main" width="100%"
					height="96%" frameborder="0" scrolling="yes"
					style="overflow: auto;"></iframe></td>
		</tr>
	</table>
</body>
</html>