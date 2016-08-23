/**
 * Created by Administrator on 2016/8/12.
 */
var PictureNumber = cc.Sprite.extend({
    m_Number:null,
    m_NumberTexture:null,
    ctor:function(){
        this._super();

    },
    buildNumber:function(paramNumber, paramTexture)
    {
        this.setNumber(paramNumber);
        this.setNumberTexture(cc.textureCache.addImage(paramTexture));
        return this.build();
    },
    build:function(){

        var iNumCount = (this.m_Number+"").length;   //取得字符个数
        var stSize = this.m_NumberTexture.getContentSize(); //取得纹理大小，要求纹理中每个数字都是等宽等高，并依照0123456789排列

        var iNumWidth = parseInt( stSize.width / 10);	//纹理中每个数字的宽度
        var iNumHeight =  parseInt( stSize.height);    //纹理中每个数字的高度

        var pRT = new cc.RenderTexture(iNumWidth * iNumCount, iNumHeight); //创建渲染纹理对象，并数字确定宽度



        pRT.begin();
        for (var i = 0; i < iNumCount; i++)
        {
            var pSprite   = new cc.Sprite(); //创建精灵对象，用于绘制数字
            pSprite.setAnchorPoint(0, 0);
            pSprite.setTexture(this.m_NumberTexture);
            var iNumber = (this.m_Number+"")[i];
            //设置要显示数字的纹理区域，这个区域是指参数中paramTexture中区域
            var stRect = new cc.rect(iNumber * iNumWidth, 0, iNumWidth, iNumHeight);
            pSprite.setTextureRect(stRect, false, cc.size(stRect.width, stRect.height));
            pSprite.setPosition(i * iNumWidth, 0);    	          //计算显示的偏移位置
            pSprite.visit(); //渲染到pRT中
        }
        pRT.end();
        //取得生成的纹理
        this.setTexture(pRT.getSprite().getTexture());
        //设置显示的内容
        var stRect = new cc.rect(0, 0, iNumWidth * iNumCount, iNumHeight);
        this.setTextureRect(stRect, false, cc.size(stRect.width, stRect.height));
        //默认的情况下，通过CCRenderTexture得到的纹理是倒立的，这里需要做一下翻转
       // this.setFlippedY(true);
    },

    setNumber:function(paramNumber){
        this.m_Number = paramNumber;
    },
    getNumber:function(){
        return this.m_Number;
    },
    setNumberTexture:function(paramTexture)
    {
        this.m_NumberTexture = paramTexture;
    }
});
