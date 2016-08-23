var TestLayer = cc.Layer.extend({
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

        this.sprite = new cc.Sprite(res.s_bg);
        this.sprite.attr({
            x: size.width / 2,
            y: size.height / 2,
            scale: 1,
        });
        this.addChild(this.sprite, 0);



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


        //我的烟豆
        this.my_gold_label = new cc.LabelTTF("我的烟豆:"+wx_info.total_gold, font_type, 18);
        this.my_gold_label.x = 140;
        this.my_gold_label.y = 184;
        this.my_gold_label.setAnchorPoint(0, 0);
        this.my_gold_label.setColor(cc.color(234, 230, 131));
        this.addChild(this.my_gold_label, 10);


        return true;
    },menuItemStartCallback:function(node){
        cc.log(node);
    }
});

var TestScene = cc.Scene.extend({
    onEnter: function () {
        this._super();
        var layer = new TestLayer();
        this.addChild(layer);
    }
});

