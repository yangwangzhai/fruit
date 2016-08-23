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
        var pNum = new PictureNumber();
        /////////////////////////////
        // 2. add a menu item with "X" image, which is clicked to quit the program
        //    you may modify it.
        // ask the window size
        var size = this.sizes = cc.winSize;

        this.speed_arr = [1, 0.6, 0.4, 0.4];
        var startSpriteNormal = new cc.Sprite(res.s_start_up_png);
        var startSpriteSelected = new cc.Sprite(res.s_start_down_png);
        var startMenuItem = new cc.MenuItemSprite(
            startSpriteNormal,
            startSpriteSelected,
            this.menuItemStartCallback, this);

        this.start_menu = new cc.Menu(startMenuItem);
        this.start_menu.x = size.width - 93;
        this.start_menu.y = 32;
        this.start_menu.scale = 0.68;
        this.addChild(this.start_menu, 1);

        //下注按钮
        var bet_res_arr = [res.s_apple, res.s_orange, res.s_papaya, res.s_bell, res.s_watermelon, res.s_double_star, res.s_seven, res.s_bar];
        var bet_down_res_arr = [res.s_apple_down, res.s_orange_down, res.s_papaya_down, res.s_bell_down, res.s_watermelon_down, res.s_double_star_down, res.s_seven_down, res.s_bar_down];
        var bet_x_arr = [75, 112, 150, 188, 233, 270, 308, 346];
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
            bet_menu.y = -35;
            bet_menu.scale = 0.68;

            this.addChild(bet_menu, 1);
        }

        //下注的值
        var bet_num_x_arr = [15, 52, 90, 128, 170, 207, 242, 279];
        var bet_num_arr = [this.bet1, this.bet2, this.bet3, this.bet4, this.bet5, this.bet6, this.bet7, this.bet8];
        for(var key in bet_num_arr){
            bet_num_arr[key] = new PictureNumber();
            bet_num_arr[key].buildNumber(0, res.s_num_big);
            bet_num_arr[key].setPosition(size.width - bet_num_x_arr[key], 70);
            bet_num_arr[key].setAnchorPoint(1, 0);
            bet_num_arr[key].scale = 0.68;
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

        // add "HelloWorld" splash screen"
        this.sprite = new cc.Sprite(res.s_bg);
        this.sprite.attr({
            x: size.width / 2,
            y: size.height / 2,
            scale: 0.68,
        });
       this.addChild(this.sprite, 0);

        this.mask_sprite = new cc.Sprite(res.s_mask);
        this.mask_sprite.attr({
            x: 42,
            y: size.height - 77,
            scale: 0.68,
        });
        this.addChild(this.mask_sprite, 0);

        //添加闪一闪
        cc.spriteFrameCache.addSpriteFrames(res.s_mask_plist);
        this.spriteMaskRun();
        this.mask_sprite_width = 65 * 0.65 - 3;
        this.mask_sprite_height = 66 * 0.66 - 4;
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





        //我的烟豆
        var r_num = this.getRandomNum(1,9999);
        this.my_gold = new PictureNumber();
        this.my_gold.buildNumber(r_num, res.s_num_big);
        this.my_gold.setPosition(size.width - 30,size.height - 46);
        this.my_gold.setAnchorPoint(1, 0);
        this.my_gold.scale = 0.68;
        this.my_gold.value = r_num;
        this.addChild(this.my_gold, 3);

        //比倍烟豆
        this.result_gold = new PictureNumber();
        this.result_gold.buildNumber(0, res.s_num_big);
        this.result_gold.setPosition(size.width / 2 - 15,size.height - 46);
        this.result_gold.setAnchorPoint(1, 0);
        this.result_gold.scale = 0.68;


        this.addChild(this.result_gold);

        //
    /*    var allbet5 = new cc.LabelTTF("11" , "Arial-BoldMT", 18);
        allbet5.attr({
            x: 61,
            y:size.height - 46,
            anchorX:1,
            anchorY:0
        });
        allbet5.setColor(cc.color(10,245,252));
        this.addChild(allbet5);*/

        return true;
    }, onBugMe: function (node) {
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


    }, onBugMe2: function (node) {
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
    }, onBugMe3: function (node) {
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


    }, onBugMe4: function (node) {
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
        }


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
        this.speed = this.speed_arr[this.run_circle];
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
    },spriteMaskRun: function(){
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
        this.mask_sprite.runAction( cc.RepeatForever.create(this.spangled_action) );
    },menuItemBetCallback: function(node){
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
                bet_node.buildNumber(responseObj.sum, res.s_num_big);
                //减少烟豆总数
                self.my_gold.value = Number(self.my_gold.value) - 1;
                self.my_gold.buildNumber(self.my_gold.value, res.s_num_big);

                gameid = responseObj.game_id;
            }
        };
        var dataObj = {betId:node.bianhao, gameId: gameid};
        if(gameid < 1) return false;
        var data = postData(dataObj);//转换格式
        xhr.send(data);

    },resetBetVal:function(index){
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
            bet_node.buildNumber(0, res.s_num_big);
        }

        //返回的比倍结果
        this.result_gold.value = xhr_result_gold;
        this.result_gold.buildNumber(xhr_result_gold, res.s_num_big);
    }
});

var PlayScene = cc.Scene.extend({
    onEnter: function () {
        this._super();
        var layer = new PlayLayer();
        this.addChild(layer);
    }
});

