var PlayLayer = cc.Layer.extend({
    sprite: null,
    mask_sprite: null,
    mask_sprite_width: 0,
    mask_sprite_height: 0,
    start_sprite: null,
    run_circle: 2,
    speed_arr: null,
    speed: 2,
    stop_num: 0, //停止在第几块从1开始
    quadrel_num:6,  //一行总共有六快
    stop_margin:0,//停止在第几边，从1开始，为上边，2为右边，3是下边，4是左边
    spangled_action:null,
    sizes:null,
    start_menu:null,
	big_small_count:0, //押大押小的数字的变化次数
	my_gold_label:null,
    bet1:null,
    bet2:null,
    bet3:null,
    bet4:null,
    bet5:null,
    bet61:null,
    bet7:null,
    bet8:null,
    ctor: function () {
        //////////////////////////////
        // 1. super init first
        this._super();
        var self = this;
        var g_scale = 1;
        var pNum = new PictureNumber();
        /////////////////////////////
        // 2. add a menu item with "X" image, which is clicked to quit the program
        //    you may modify it.
        // ask the window size
        var size = this.sizes = cc.winSize;

        this.speed_arr = [1, 0.6, 0.4, 0.4];

        //个人头像
        var myavatar = new cc.Sprite(res.s_myavatar);
        myavatar.attr({
            x:50,
            y:20
        });


        //加载微信头像
        cc.loader.loadImg(wx_info.headimgurl, {isCrossOrigin : false }, function(err, img)
        {
            var sprite = new cc.Sprite(wx_info.headimgurl);
            sprite.x = 83;
            sprite.y = 198;
            this.addChild(sprite);

        }.bind(this));
        this.addChild(myavatar);

        var my_info_font_size = 24;
        //昵称
        this.nickname = new cc.LabelTTF(wx_info.nickname, font_type, my_info_font_size);
        this.nickname.x = 140;
        this.nickname.y = 214;
        this.nickname.setAnchorPoint(0, 0);
        this.nickname.setColor(cc.color(92, 219, 230));
        this.addChild(this.nickname, 10);

        //我的烟豆
        this.my_gold_label = new cc.LabelTTF("我的烟豆:"+wx_info.total_gold, font_type, my_info_font_size);
        this.my_gold_label.x = 140;
        this.my_gold_label.y = 184;
        this.my_gold_label.setAnchorPoint(0, 0);
        this.my_gold_label.setColor(cc.color(234, 230, 131));
        this.addChild(this.my_gold_label, 10);

        //我的本次烟豆
        this.my_cur_gold_label = new cc.LabelTTF("本次烟豆:0", font_type, my_info_font_size);
        this.my_cur_gold_label.x = 140;
        this.my_cur_gold_label.y = 158;
        this.my_cur_gold_label.setAnchorPoint(0, 0);
        this.my_cur_gold_label.setColor(cc.color(234, 230, 131));
        this.addChild(this.my_cur_gold_label, 10);



        //开始按钮
        var startSpriteNormal = new cc.Sprite(res.s_start_up_png);
        var startSpriteSelected = new cc.Sprite(res.s_start_down_png);
        var startMenuItem = new cc.MenuItemSprite(
            startSpriteNormal,
            startSpriteSelected,
            this.menuItemStartCallback, this);

        this.start_menu = new cc.Menu(startMenuItem);
        this.start_menu.x = size.width - 132;
        this.start_menu.y = 240;
        this.start_menu.scale = 0.9;
        this.addChild(this.start_menu, 1);

        //退币
        var returnMenuItem = new cc.MenuItemSprite(
            new cc.Sprite(res.s_return_up_png),
            new cc.Sprite(res.s_return_down_png),
            this.menuItemReturnCallback, this);

        this.return_menu = new cc.Menu(returnMenuItem);
        this.return_menu.x = size.width - 225;
        this.return_menu.y = 240;
        this.return_menu.scale = 0.9;
        this.addChild(this.return_menu, 1);

        //押大
        var bigMenuItem = new cc.MenuItemSprite(
            new cc.Sprite(res.s_big_up_png),
            new cc.Sprite(res.s_big_down_png),
            this.menuItemBigCallback, this);
			bigMenuItem.value = 1;
        this.big_menu = new cc.Menu(bigMenuItem);
        this.big_menu.x = size.width - 300;
        this.big_menu.y = 240;
        this.big_menu.scale = 0.9;		
        this.addChild(this.big_menu, 1);

        //押小
        var smallMenuItem = new cc.MenuItemSprite(
            new cc.Sprite(res.s_small_up_png),
            new cc.Sprite(res.s_small_down_png),
            this.menuItemBigCallback, this);
		smallMenuItem.value = 0;
        this.small_menu = new cc.Menu(smallMenuItem);
        this.small_menu.x = size.width - 380;
        this.small_menu.y = 240;
        this.small_menu.scale = 0.9;
		this.small_menu.value = 0;
        this.addChild(this.small_menu, 1);

        //加倍
        var leftMenuItem = new cc.MenuItemSprite(

            new cc.Sprite(res.s_left_up_png),
            new cc.Sprite(res.s_left_down_png),
            this.menuItemLeftCallback, this);

        this.left_menu = new cc.Menu(leftMenuItem);
        this.left_menu.x = size.width - 605;
        this.left_menu.y = 186;
        this.left_menu.scale = 0.8;
        this.left_menu.setRotationY(1);
        this.addChild(this.left_menu, 1);

        //减倍
        var rightMenuItem = new cc.MenuItemSprite(
            new cc.Sprite(res.s_right_up_png),
            new cc.Sprite(res.s_right_down_png),
            this.menuItemRightCallback, this);

        this.right_menu = new cc.Menu(rightMenuItem);
        this.right_menu.x = size.width - 500;
        this.right_menu.y = 191;
        this.right_menu.scale = 0.8;
        //this.left_menu.setPercent(0);
        this.right_menu.setRotationY(0);
        this.addChild(this.right_menu, 1);



        //下注按钮 375
        var bet_res_arr = [res.s_apple, res.s_orange, res.s_papaya, res.s_bell, res.s_watermelon, res.s_double_star, res.s_seven, res.s_bar];
        var bet_down_res_arr = [res.s_apple_down, res.s_orange_down, res.s_papaya_down, res.s_bell_down, res.s_watermelon_down, res.s_double_star_down, res.s_seven_down, res.s_bar_down];
        var bet_x_arr = [44, 118, 200, 275, 355, 435, 510, 590];
        for(var key in bet_res_arr){
            var startSpriteN = new cc.Sprite(bet_res_arr[key]);
            var startSpriteS = new cc.Sprite(bet_down_res_arr[key]);
            var startMenuItems = new cc.MenuItemSprite(
                startSpriteN,
                startSpriteS,
                this.menuItemBetCallback, this);
            startMenuItems.attr({
                bianhao:key,
            });
            var bet_menu = new cc.Menu(startMenuItems);
            bet_menu.x = size.width - bet_x_arr[key];
            bet_menu.y = 50;
            bet_menu.scale = g_scale;

            this.addChild(bet_menu, 1);
        }

        //下注的值,从右边起第一
        var bet_num_x_arr = [32, 104, 178, 250, 327, 402, 477, 552];
        var bet_num_arr = [this.bet1, this.bet2, this.bet3, this.bet4, this.bet5, this.bet6, this.bet7, this.bet8];
        for(var key in bet_num_arr){
            bet_num_arr[key] = new PictureNumber();
            bet_num_arr[key].buildNumber("00", res.s_num_big);
            bet_num_arr[key].setPosition(size.width - bet_num_x_arr[key] , 102);
            bet_num_arr[key].setAnchorPoint(1, 0);
            bet_num_arr[key].scale = g_scale;
            bet_num_arr[key].attr({
                value:0,
                name:'bet_num_'+key
            });

            this.addChild(bet_num_arr[key], 4, 'bet_num_'+key);
            key = Number(key);
            switch (key){
                case 0:
                    this.bet1 = bet_num_arr[key];
                    break;
                case 1:
                    this.bet2 = bet_num_arr[key];
                    break;
                case 2:
                    this.bet3 = bet_num_arr[key];
                    break;
                case 3:
                    this.bet4 = bet_num_arr[key];
                    break;
                case 4:
                    this.bet5 = bet_num_arr[key];
                    break;
                case 5:
                    this.bet6 = bet_num_arr[key];
                    break;
                case 6:
                    this.bet7 = bet_num_arr[key];
                    break;
                case 7:
                    this.bet8 = bet_num_arr[key];
                    break;
            }

        }

		
	   //大小显示	
		this.big_small = new PictureNumber();
        this.big_small.buildNumber("00", res.s_num_big);
        this.big_small.setPosition(size.width/2  ,size.height /2 -46);
        this.big_small.setAnchorPoint(0.5, 0.5);
        this.big_small.scale = g_scale;
        this.big_small.value = 0;
        this.addChild(this.big_small, 3);

        // add "HelloWorld" splash screen"
        this.sprite = new cc.Sprite(res.s_bg);
        this.sprite.attr({
            x: size.width / 2,
            y: size.height / 2,
            scale: 1,
        });
       this.addChild(this.sprite, 0);

        this.mask_sprite = new cc.Sprite(res.s_mask);
        this.mask_sprite.attr({
            x: 116,
            y: size.height - 238,
            scale: g_scale,
        });
        this.addChild(this.mask_sprite, 0);

        //添加闪一闪
        cc.spriteFrameCache.addSpriteFrames(res.s_mask_plist);
        this.spriteMaskRun();
        this.mask_sprite_width = 76 * g_scale - 8;
        this.mask_sprite_height = 69 * g_scale - 8;
        //POST
        /*var xhr = cc.loader.getXMLHttpRequest();
        var statusPostLabel = new cc.LabelTTF("Status:", "Thonburi", 18);
        this.addChild(statusPostLabel, 1);

        statusPostLabel.x = size.width / 2;

        statusPostLabel.y = size.height - 140;
        statusPostLabel.setString("Status: Send Post Request to httpbin.org with plain text");

        xhr.open("POST", "./index.php?c=fruit&m=post_http");
        //set Content-type "text/plain;charset=UTF-8" to post plain text
        xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded;charset=UTF-8");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && (xhr.status >= 200 && xhr.status <= 207)) {
                var httpStatus = xhr.statusText;
                var response = xhr.responseText.substring(0, 100) + "...";
                var jsonobj = eval('(' + xhr.responseText + ')');
                var responseLabel = new cc.LabelTTF("POST Response (100 chars):  \n" + jsonobj.c, "Thonburi", 16);
                self.addChild(responseLabel, 1);
                responseLabel.anchorX = 0;
                responseLabel.anchorY = 1;

                responseLabel.x = size.width / 10 * 3;
                responseLabel.y = size.height / 2;
                statusPostLabel.setString("Status: Got POST response! " + httpStatus);
            }
        };
        var data = {data:'我是你'};
        var params = "";
        if(typeof(data)=="object"){
            for(key in data){
                params+=(key+"="+data[key]+"&");
            }
        }else{
            params = data;
        }
        xhr.send(params);*/

        //我的烟豆财富
        var r_num = this.getRandomNum(1,9999);
        this.my_gold = new PictureNumber();
        this.my_gold.buildNumber(wx_info.total_gold, res.s_num_big);
        this.my_gold.setPosition(size.width - 76,size.height - 191);
        this.my_gold.setAnchorPoint(1, 0);
        this.my_gold.scale = g_scale;
        this.my_gold.value = wx_info.total_gold;
        this.addChild(this.my_gold, 3);

        //比倍烟豆
        this.result_gold = new PictureNumber();
        this.result_gold.buildNumber(0, res.s_num_big);
		this.result_gold.value = 0;
        this.result_gold.setPosition(size.width / 2 - 36,size.height - 191);
        this.result_gold.setAnchorPoint(1, 0);
        this.result_gold.scale = g_scale;
        this.addChild(this.result_gold);

        return true;
    }, onBugMe: function (node) {cc.audioEngine.playEffect(res.s_run,false);
        if (this.stop_margin == 1) {
            this.spriteMaskRun();
            this.start_menu.setEnabled(true);
            this.resetBetVal(xhr_bet_index);//重置下注的值
            return false;
        }
        if(this.stop_margin == 2) this.quadrel_num = this.stop_num;
        var action = cc.moveBy(this.speed, cc.p(0, -this.mask_sprite_height * this.quadrel_num));
        this.mask_sprite.runAction(cc.sequence(
            action,
            cc.callFunc(this.onBugMe2, this))
        );


    }, onBugMe2: function (node) {cc.audioEngine.playEffect(res.s_run,false);
        if (this.stop_margin == 2) {
            this.spriteMaskRun();
            this.start_menu.setEnabled(true);
            this.resetBetVal(xhr_bet_index);//重置下注的值
            return false;
        }
        if(this.stop_margin ==3) this.quadrel_num = this.stop_num;
        var action = cc.moveBy(this.speed, cc.p(-this.mask_sprite_width * this.quadrel_num, 0));
        this.mask_sprite.runAction(cc.sequence(
            action,
            cc.callFunc(this.onBugMe3, this))
        );
    }, onBugMe3: function (node) {cc.audioEngine.playEffect(res.s_run,false);
        if (this.stop_margin == 3){
            this.spriteMaskRun();
            this.start_menu.setEnabled(true);
            this.resetBetVal(xhr_bet_index);//重置下注的值
            return false;
        }
        if(this.stop_margin == 4) this.quadrel_num = this.stop_num;
        var action = cc.moveBy(this.speed, cc.p(0, this.mask_sprite_height * this.quadrel_num));
        this.mask_sprite.runAction(cc.sequence(
            action,
            cc.callFunc(this.onBugMe4, this))
        );


    }, onBugMe4: function (node) {cc.audioEngine.playEffect(res.s_run,false);
        this.startRun();
    }, menuItemStartCallback: function (node) {
        //set arguments with <URL>?xxx=xxx&yyy=yyy
        var self = this;
        //检查是否退避
        var result_gold = Number(this.result_gold.value);
        if(result_gold > 0){
            this.result_gold.buildNumber(0, res.s_num_big);
            this.result_gold.value = 0;
            this.my_gold.value = Number(this.my_gold.value) + result_gold;
            this.my_gold.buildNumber(this.my_gold.value, res.s_num_big);			
			//我的烟豆
			this.my_gold_label.setString("我的烟豆:" + this.my_gold.value);
			
            var bet_node = this.getChildByName('bet_num_'+xhr_bet_index);
            bet_node.value = 0;
            bet_node.buildNumber(0, res.s_num_big);
            xhr_bet_index = -1;
            return false;
        }

        if (gameid > 0){
            xhr.open("GET", "./index.php?c=fruit&m=get_result&gameId=" + gameid, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && (xhr.status >= 200 && xhr.status <= 207)) {
                    var responseObj = eval('(' + xhr.responseText + ')');
                    xhr_stop_margin = responseObj.xhr_stop_margin;
                    xhr_stop_num = responseObj.xhr_stop_num;
                    xhr_result_gold = responseObj.result_gold;
                    gameid = 0;
                    xhr_bet_index = responseObj.bet_index;
                    //重置
                    self.stop_margin = 0;
                    self.quadrel_num = 6;
                }
            };
            xhr.send();
        }else{
			//alert('您未下注！');
            var tipUI = new TipUI("您未下注！");
            this.addChild(tipUI,100);
			return false;			
		}

        cc.audioEngine.playEffect(res.s_run,false);
        this.start_menu.setEnabled(false);
        this.mask_sprite.initWithFile('res/mask_1.png');
        this.mask_sprite.stopAllActions();
        this.mask_sprite.initWithFile('res/mask_1.png');
        this.run_circle = 2;//重新赋值
        var surplus_quadrel_num;
        var action;
        var stop_margin ;
        if(this.stop_margin == 0){
            this.startRun();
        }else{
            if(this.stop_margin == 1 || this.stop_margin == 2) {
                this.speed = 0.6;
            }
            stop_margin =  this.stop_margin;
            this.stop_margin =  -1 ;
            surplus_quadrel_num = 6 - this.quadrel_num;//剩余多少格没走完一边
        }
        if(stop_margin == 1){

            this.quadrel_num = 6;
            if(surplus_quadrel_num == 0) {
                this.onBugMe(1);
                return false;
            }
            action = cc.moveBy(this.speed, cc.p(this.mask_sprite_width * surplus_quadrel_num, 0));
            this.mask_sprite.runAction(cc.sequence(
                action,
                cc.callFunc(this.onBugMe, this)
                )
            );
        }
        if(stop_margin == 2){
            this.quadrel_num = 6;
            if(surplus_quadrel_num == 0) {
                this.onBugMe2(1);
                return false;
            }
            action = cc.moveBy(this.speed, cc.p(0, -this.mask_sprite_height * surplus_quadrel_num));
            this.mask_sprite.runAction(cc.sequence(
                action,
                cc.callFunc(this.onBugMe2, this)
                )
            );
        }
        if(stop_margin == 3){
            this.quadrel_num = 6;
            if(surplus_quadrel_num == 0) {
                this.onBugMe3(1);
                return false;
            }
            action = cc.moveBy(this.speed, cc.p(-this.mask_sprite_width * surplus_quadrel_num, 0));
            this.mask_sprite.runAction(cc.sequence(
                action,
                cc.callFunc(this.onBugMe3, this)
                )
            );

        }
        if(stop_margin == 4){
            this.quadrel_num = 6;
            if(surplus_quadrel_num == 0) {
                this.onBugMe4(1);
                return false;
            }
            action = cc.moveBy(this.speed, cc.p(0, this.mask_sprite_height * surplus_quadrel_num));
            this.mask_sprite.runAction(cc.sequence(
                action,
                cc.callFunc(this.onBugMe4, this)
                )
            );

        }


    }, startRun: function () {

        if (this.run_circle == 0) {
            this.stop_margin = xhr_stop_margin;//;this.getRandomNum(1, 4);
            this.stop_num = xhr_stop_num; //this.getRandomNum(1, 6);
            cc.log(this.stop_margin+','+this.stop_num);
        }
        if (this.stop_margin == 4) {
            this.spriteMaskRun();
            this.start_menu.setEnabled(true);
            this.resetBetVal(xhr_bet_index);//重置下注的值
            return false;
        }
        this.speed = this.speed_arr[this.run_circle];//跑动的速度变化
        if(this.stop_margin == 1) this.quadrel_num = this.stop_num;
        var action = cc.moveBy(this.speed, cc.p(this.mask_sprite_width * this.quadrel_num, 0));
        this.mask_sprite.runAction(cc.sequence(
            action,
            cc.callFunc(this.onBugMe, this))
        );
        this.run_circle--;
    }, getRandomNum: function (Min, Max) {
        var Range = Max - Min;
        var Rand = Math.random();
        return (Min + Math.round(Rand * Range));
    },spriteMaskRun: function(){  //一闪一闪的光圈
        var animFrames = [];
        for (var i = 1; i >=0; i--) { //循环加载每一帧图片 v
            var frameName = "mask_"+ i + ".png";
            var frame = cc.spriteFrameCache.getSpriteFrame(frameName);
            animFrames.push(frame);
        }
        var animation = new cc.Animation(animFrames, 0.8/2);                //定义图片播放间隔
        // animation.setRestoreOriginalFrame(true);
        this.spangled_action = cc.Animate.create(animation);//闪烁动画
        this.spangled_action.setTag(1);
       // this.mask_sprite.runAction( cc.RepeatForever.create(this.spangled_action) );
    },menuItemBetCallback: function(node){   //点击下注按钮
        var bet_on_audio = [res.s_bet_1, res.s_bet_2, res.s_bet_3, res.s_bet_4, res.s_bet_5, res.s_bet_6, res.s_bet_7, res.s_bet_8];
        cc.audioEngine.playEffect(bet_on_audio[node.bianhao],false);
        var bet_node = this.getChildByName('bet_num_'+node.bianhao);
        var sum = bet_node.value + 1;
        var self = this;
        xhr.open("POST", "./index.php?c=fruit&m=save_bet");
        xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded;charset=UTF-8");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && (xhr.status >= 200 && xhr.status <= 207)) {
                var httpStatus = xhr.statusText;
                var responseObj = {sum:0, game_id:0};
                 responseObj = eval('(' + xhr.responseText + ')');
                //设置下注的值增加
                bet_node.value = responseObj.sum;
                var cur_num = bet_node.value;
                if(cur_num<10) cur_num = "0" + cur_num;
                bet_node.buildNumber(cur_num, res.s_num_big);
                //减少烟豆总数
                self.my_gold.value = Number(self.my_gold.value) - 1;
                self.my_gold.buildNumber(self.my_gold.value, res.s_num_big);
				//我的烟豆
				self.my_gold_label.setString("我的烟豆:" + self.my_gold.value);
				
                gameid = responseObj.game_id;
            }
        };
        var dataObj = {betId:node.bianhao, gameId: gameid, openid:wx_info.openid};

        var data = postData(dataObj);//转换格式
        xhr.send(data);

    },menuItemBigCallback:function(node){  //押大押小
	    if(Number(this.result_gold.value) < 1) return false; //不中就返回
		this.schedule(function nothing() {  
            this.getBigSmall(node)  
        }, 0.1, 50);
	
	},menuItemReturnCallback:function(node){  //退币
		
		var result_gold = Number(this.result_gold.value);
        if(result_gold > 0){
            cc.audioEngine.playEffect(res.s_return_gold,false);
            this.result_gold.buildNumber(0, res.s_num_big);
            this.result_gold.value = 0;
            this.my_gold.value = Number(this.my_gold.value) + result_gold;
            this.my_gold.buildNumber(this.my_gold.value, res.s_num_big);
			//我的烟豆
			this.my_gold_label.setString("我的烟豆:" + this.my_gold.value);
			
            var bet_node = this.getChildByName('bet_num_'+xhr_bet_index);
            bet_node.value = 0;
            bet_node.buildNumber("00", res.s_num_big);
            xhr_bet_index = -1;
            return false;
        }
		
	},menuItemRightCallback:function(node){
        //减倍
        if(Number(this.result_gold.value) < 1) return false; //不中就返回

        cc.audioEngine.playEffect(res.s_right_left,false);
        var old_result_gold = Number(this.result_gold.value);
        this.result_gold.value = parseInt(old_result_gold/2)
        this.result_gold.buildNumber(this.result_gold.value, res.s_num_big);
        //减少财富烟豆
        this.my_gold.value = Number(this.my_gold.value) + old_result_gold -this.result_gold.value;
        this.my_gold.buildNumber(this.my_gold.value, res.s_num_big);
        //我的烟豆
        this.my_gold_label.setString("我的烟豆:" + this.my_gold.value);
				
	},menuItemLeftCallback:function(node){
        //加倍
        if(Number(this.result_gold.value) < 1) return false; //不中就返回
        if(Number(this.result_gold.value) * 2 > Number(this.my_gold.value))return false;

        cc.audioEngine.playEffect(res.s_right_left,false);
        var old_result_gold = Number(this.result_gold.value);
        this.result_gold.value = old_result_gold * 2;
        this.result_gold.buildNumber(this.result_gold.value, res.s_num_big);

        //减少财富烟豆
        this.my_gold.value = Number(this.my_gold.value) - old_result_gold;
        this.my_gold.buildNumber(this.my_gold.value, res.s_num_big);
        //我的烟豆
        this.my_gold_label.setString("我的烟豆:" + this.my_gold.value);

	
	},resetBetVal:function(index){  //把不中的下注值清空
        cc.log('index:'+index);

        var bet_num_arr = [this.bet1, this.bet2, this.bet3, this.bet4, this.bet5, this.bet6, this.bet7, this.bet8];
        for(var key in bet_num_arr) {
            key = Number(key);
            if(key == index){
                cc.log(bet_num_arr[key]);
                continue;//如果是后台结果返回的值就跳过
            }
            var bet_node = this.getChildByName('bet_num_'+key);
            bet_node.value = 0;
            bet_node.buildNumber("00", res.s_num_big);
        }

        //返回的比倍结果
        this.result_gold.value = xhr_result_gold;
        this.result_gold.buildNumber(xhr_result_gold, res.s_num_big);
    },getBigSmall:function(node){  //押大押小的数字滚动
		var num = this.getRandomNum(1, 14);
		var cur_num = num;
		if(num<10) num = "0" + num;
		this.big_small.buildNumber(""+num+"", res.s_num_big);
		this.big_small.value = cur_num;
		this.big_small_count ++;
        cc.audioEngine.playEffect(res.s_big_small,false);
		if(this.big_small_count == 51){
			var big_small_value = node.value;//0为押小，1为押大
			if(cur_num < 8 && big_small_value == 0){
				this.result_gold.value = Number(this.result_gold.value) * 2;
				//cc.log(cur_num);
			}else if(cur_num > 7 && big_small_value == 1){
				this.result_gold.value = Number(this.result_gold.value) * 2;
				//cc.log(cur_num);
			}else{
				this.result_gold.value = 0;
			}
			this.result_gold.buildNumber(this.result_gold.value, res.s_num_big);
			this.big_small_count = 0;
			var bet_node = this.getChildByName('bet_num_'+xhr_bet_index);
            bet_node.value = 0;
            bet_node.buildNumber("00", res.s_num_big);
			
		}
	}
});

var PlayScene = cc.Scene.extend({
    onEnter: function () {
        this._super();
        var layer = new PlayLayer();
        this.addChild(layer);
    }
});

