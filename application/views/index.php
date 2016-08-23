<!DOCTYPE HTML>
<html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>幸运水果机</title>
    <meta name="viewport" content="initial-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="full-screen" content="yes"/>
    <meta name="screen-orientation" content="portrait"/>
    <meta name="x5-fullscreen" content="true"/>
    <meta name="360-fullscreen" content="true"/>
    <style>
body, canvas, div {
	-moz-user-select: none;
	-webkit-user-select: none;
	-ms-user-select: none;
	-khtml-user-select: none;
	-webkit-tap-highlight-color: rgba(0, 0, 0, 0);
	
   
}
 body{ background:url('static/fruit/res/loading_bg.png') no-repeat center center;
	 background-attachment:fixed;
	/* background-repeat:no-repeat;*/
	 background-size:cover;
	 -moz-background-size:cover;
	 -webkit-background-size:cover;
   
}
 .bodycss{ background:url('static/fruit/res/loading_bg.png') no-repeat center center;
	 background-attachment:fixed;
	/* background-repeat:no-repeat;*/
	 background-size:cover;
	 -moz-background-size:cover;
	 -webkit-background-size:cover;
   
}
</style>
    </head>

    <body>
    
<script src="static/fruit/res/loading.js"></script>
<canvas id="gameCanvas" width="320" height="480"></canvas>
<script>
 var wx_info = {openid:'<?=$openid?>',nickname:'<?=$nickname?>',headimgurl:'<?=$headimgurl?>',sex:'<?=$sex?>',total_gold:<?=$smokeBeansCount?>};
    (function () {
        var nav = window.navigator;
        var ua = nav.userAgent.toLowerCase();
        var uaResult = /android (\d+(?:\.\d+)+)/i.exec(ua) || /android (\d+(?:\.\d+)+)/i.exec(nav.platform);
        if (uaResult) {
            var osVersion = parseInt(uaResult[1]) || 0;
            var browserCheck = ua.match(/(qzone|micromessenger|qqbrowser)/i);
            if (browserCheck) {
                var gameCanvas = document.getElementById("gameCanvas");
                var ctx = gameCanvas.getContext('2d');
                ctx.fillStyle = '#000000';
                ctx.fillRect(0, 0, 1, 1);
            }
        }
    })();
</script>
<script src="static/fruit/frameworks/cocos2d-html5/CCBoot.js"></script>
<script cocos src="static/fruit/main.js"></script>
    
</body>
</html>