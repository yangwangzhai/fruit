
<?php $this->load->view('admin/header');?>

<div class="container">
<div class="mainbox">
<form action="cpi.php?d=admin&c=common&m=website_save" method="post" enctype="multipart/form-data">
	
    <table border="0" cellpadding="0" cellspacing="0" class="opt" width="100%">         
        <tr>
				<td width="100">路况时效：</td>
				<td><input name="value[weixintime]" type="text" class="txt" style="width: 40px;" value="<?=$web[weixintime]?>" maxlength="8">分钟，在微信上显示最近多少分钟内路况信息</td>
			</tr>       
        <tr>
            <td >无路况提示：</td>
            <td><textarea name="value[bobao_null_msg]" cols="60" rows="6" class="txt" style="width:700px; height:150px;"><?=$web[bobao_null_msg]?></textarea><br><font color="#FF3300">(在没有查询到路况数据的时候，回复该信息)</font></td>
        </tr>
        <tr>
            <td >无酒店提示：</td>
            <td><textarea name="value[hotel_null_msg]" cols="60" rows="6" class="txt" style="width:700px; height:100px;"><?=$web[hotel_null_msg]?></textarea><br><font color="#FF3300">(在没有查询到酒店餐馆数据的时候，回复该信息)</font></td>
        </tr>
        <tr>
            <td ></td>
            <td><input type="submit" name="submit" value=" 提交 " class="btn" tabindex="3" /></td>
        </tr>
    </table>   
</form>
</div>
</div>
</body>
</html>
