<template>

  <section class="tui-content-padding">
    <div class="layout-c-3rd-login">
      <div class="i-3rd-login">
        <!--
        <a href="javascript:;">
          <i class="alifont af-qq"></i>
        </a>
        <a href="javascript:;">
          <i class="alifont af-weibo"></i>
        </a>
        -->
        <a href="javascript:;">
          <i class="alifont af-weixin"></i>
        </a>
      </div>
      <p class="tui-text1 i-tip">点击使用微信即刻登录<br>应用不会收集您的任何隐私信息</p>
    </div>
  </section>


</template>

<script>
  export default {
    name: 'c-3rd-login',
    methods: {

    },
    mounted(){

    }
  };
</script>

<style lang="less" rel="stylesheet/less">
  @import "../../assets/css/var";
  .layout-c-3rd-login {
    > p {
      text-align: center;
      margin-top: 60px;
    }
    >.i-3rd-login {
      display: flex;
      align-items:center;
      justify-content: space-around;
      > a {
        i {
          display: inline-flex;
          width: 240px;
          height: 240px;
          justify-content:center;
          align-items:center;
          color: #fff;
          border-radius: 50%;
          font-size: 120px;
          &.af-qq {
            background: #12b7f5;
            &:active {
              background: #1191c3;
            }
          }
          &.af-weibo {
            background: #e6162d;
            &:active {
              background: #b41526;
            }
          }
          &.af-weixin {
            background: #609700;
            &:active {
              background: #3e6500;
            }
          }

        }
      }
    }
  }
</style>