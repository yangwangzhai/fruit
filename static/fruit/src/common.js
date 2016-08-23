/**
 * Created by Administrator on 2016/8/16.
 */
var xhr;//XMLHttpRequest
var gameid = 0;//初始化当局游戏id，如果提交过则返回gameid，点击“开始”按钮，gameid归为0，即新游戏开始
var xhr_stop_margin = 0;
var xhr_stop_num = 0;
var xhr_bet_index = -1;//停止在的水果的图标的下注框的位置index
var xhr_result_gold = 0;//返回的比倍结果值
var font_type = 'Arial';
function postData(data){
  //  var data = {data:'我是你'};
    var params = "";
    if(typeof(data)=="object"){
        for(key in data){
            params+=(key+"="+data[key]+"&");
        }
    }else{
        params = data;
    }
    return params;
}
