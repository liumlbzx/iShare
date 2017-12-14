<style lang="less" rel="stylesheet/less" scoped>
  @import "../../assets/css/var";
  .layout-user-index {
    .layout_top {
      height: 270px;
      background: #ffd62a linear-gradient(-180deg, #ffeb5f 0% , #ffd62a 100%);
      position: relative;
      .i-link-left,.i-link-right {
        position: absolute;
        top:0;
        padding: 35px 35px 0 35px;
        i {
          color: @color-black-normal;
          font-size: 65px;
          font-weight: 800;
        }
      }
      .i-link-left {
        left: 0;
        i {
          position: relative;
          em {
            position: absolute;
            display: inline-flex;
            align-items:center;
            justify-content:center;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            right: -30px;
            top: -30px;
            color: @color-yellow-normal;
            background: @color-black-normal;
            font-style: normal;
            font-size: 24px;
          }
        }
      }
      .i-link-right {
        right: 0;
      }
      .i-link-bottom {
        position: absolute;
        bottom: 35px;
        right: 35px;
        color: #999;
        font-size: 39px;
        display: inline-flex;
        align-items:center;
        span {
          margin: 0 45px;
          border-right: 1px solid @color-black-normal;
          height: 30px;
        }
        a {
          color: @color-black-normal;
        }
      }
    }
    .layout_baseinfo {
      position: relative;
      height: 240px;
      background: #fff;
      padding: 0 50px 0 50px;
      display: flex;
      align-items:center;
      >.i-face {
        position: relative;
        width: 270px;
        height: 270px;
        border-radius: 50%;
        background: #fff;
        top: -60px;
        box-shadow: 0 1px 1px rgba(0,0,0,.3); /*no*/
        display: inline-flex;
        align-items:center;
        span {
          margin: 0 auto;
          width: 250px;
          height: 250px;
          border-radius: 50%;
          display: inline-flex;
          align-items:center;
          justify-content: center;
          overflow: hidden;
          img {
            max-width: 250px;
            max-height: 250px;
          }
        }
      }
      >.i-index2 {
        flex: 1;
        margin: 0 50px 0 50px;
        .i-nickname {
          font-size: 60px;
          color: @color-black-normal;
        }
        .i-level {
          margin-top: 15px;
          font-size: 36px;
          span {
            color: @color-red-normal;
          }
        }
      }
      > i {
        color: @color-black-normal;
        font-weight: 800;
        font-size: 48px;
      }
    }
    .layout_links {
      padding: 0 50px;
      border-top: 1px solid #f9f9f9;/*no*/
      background: #fff;
      display: flex;
      justify-content: space-between;
      > a {
        box-sizing: content-box;
        width: 20%;
        padding: 30px 0;
        height: 130px;
        display: inline-flex;
        flex-direction: column;
        justify-content: space-around;
        text-align: center;
        strong {
          font-size: 48px;
          color: @color-black-normal;
          font-weight: 400;
        }
        span {
          color: #737373;
          font-weight: 200;
        }
      }
    }
  }
</style>

<template>

  <section class="layout-user-index tui-background-gray">

    <div class="tui-content no-header tui-padding-bottom">

      <div class="layout_top">
        <router-link :to="{name:'empty'}" class="i-link-left">
          <i class="alifont af-bf-message">
            <em>2</em>
          </i>
        </router-link>
        <router-link :to="{name:'user/setting'}" class="i-link-right">
          <i class="alifont af-shezhi">
          </i>
        </router-link>
        <p class="i-link-bottom">
          <router-link :to="{name:'empty'}">收藏</router-link>
          <span></span>
          <router-link :to="{name:'empty'}">浏览记录</router-link>
        </p>
      </div>

      <router-link :to="{name:'user/profile'}" tag="div" class="layout_baseinfo">
        <p class="i-face">
          <span><img src="http://img1.ffan.com/orig/T1fZ_vBybj1RCvBVdK"></span>
        </p>
        <div class="i-index2">
          <p class="i-nickname">阿波罗的刻录机</p>
          <p class="i-level">
            积分等级: <span>Lv.13</span>
          </p>
        </div>
        <i class="alifont af-xiangyou"></i>
      </router-link>

      <div class="layout_links">
        <router-link :to="{name:'empty'}">
          <strong>24</strong>
          <span>积分</span>
        </router-link>
        <router-link :to="{name:'empty'}">
          <strong>100</strong>
          <span>信用</span>
        </router-link>
        <router-link :to="{name:'empty'}">
          <strong>3</strong>
          <span>好友</span>
        </router-link>
        <router-link :to="{name:'empty'}">
          <strong>38</strong>
          <span>关注</span>
        </router-link>
        <router-link :to="{name:'empty'}">
          <strong>12</strong>
          <span>粉丝</span>
        </router-link>
      </div>

      <div class="tui-data-show">

        <div class="i-group">

          <router-link class="i-data" tag="div" :to="{name:'empty'}">
            <i class="i_zuo alifont af-huifudetiezi"></i>
            <label class="i_label">我的回复</label>
            <p class="i_value"></p>
            <i class="i_you alifont af-xiangyou"></i>
          </router-link>

          <router-link class="i-data" tag="div" :to="{name:'empty'}">
            <i class="i_zuo alifont af-wodechengchangfatie"></i>
            <label class="i_label">我的拉呱</label>
            <p class="i_value"></p>
            <i class="i_you alifont af-xiangyou"></i>
          </router-link>

          <router-link class="i-data" tag="div" :to="{name:'empty'}">
            <i class="i_zuo alifont af-shoucang"></i>
            <label class="i_label">我的收藏</label>
            <p class="i_value"></p>
            <i class="i_you alifont af-xiangyou"></i>
          </router-link>

        </div>

        <div class="i-group">

          <router-link class="i-data" tag="div" :to="{name:'empty'}">
            <i class="i_zuo alifont af-jinbi"></i>
            <label class="i_label">我的金币</label>
            <p class="i_value i_red">1200</p>
            <i class="i_you alifont af-xiangyou"></i>
          </router-link>

          <router-link class="i-data" tag="div" :to="{name:'empty'}">
            <i class="i_zuo alifont af-purse"></i>
            <label class="i_label">我的金元</label>
            <p class="i_value i_red">1200</p>
            <i class="i_you alifont af-xiangyou"></i>
          </router-link>

          <router-link class="i-data" tag="div" :to="{name:'empty'}">
            <i class="i_zuo alifont af-gift"></i>
            <label class="i_label">我的礼物</label>
            <p class="i_value"></p>
            <i class="i_you alifont af-xiangyou"></i>
          </router-link>

        </div>

        <div class="i-group">

          <router-link class="i-data" tag="div" :to="{name:'empty'}">
            <i class="i_zuo alifont af-kefu"></i>
            <label class="i_label">客服中心</label>
            <p class="i_value i_red"></p>
            <i class="i_you alifont af-xiangyou"></i>
          </router-link>

          <router-link class="i-data" tag="div" :to="{name:'empty'}">
            <i class="i_zuo alifont af-yijian"></i>
            <label class="i_label">意见反馈</label>
            <p class="i_value i_red"></p>
            <i class="i_you alifont af-xiangyou"></i>
          </router-link>

          <router-link class="i-data" tag="div" :to="{name:'empty'}">
            <i class="i_zuo alifont af-weibo"></i>
            <label class="i_label">关注官微 热门分享每天更新</label>
            <p class="i_value"></p>
            <i class="i_you alifont af-xiangyou"></i>
          </router-link>

          <router-link class="i-data" tag="div" :to="{name:'empty'}">
            <i class="i_zuo alifont af-weixin"></i>
            <label class="i_label">关注微信号 热门分享每天送达</label>
            <p class="i_value"></p>
            <i class="i_you alifont af-xiangyou"></i>
          </router-link>

        </div>


      </div>

    </div>

  </section>

</template>

<script>
  export default {
    name: 'user-index',
    data(){
      return {
      }
    },
    components: {
    },

    methods: {

    },
    mounted(){
    }
  };
</script>
