<template>

  <section class="layout-home-about">

    <c-header></c-header>

    <div class="tui-content tui-content-padding">
      <p class="i_p1">
        <img src="http://img1.ffan.com/orig/T1fZ_vBybj1RCvBVdK">
      </p>
      <p class="i_p2">
        {{$config.name}} Ver {{$config.version}}
      </p>
      <p class="i_p3">
        {{$config.name}}是什么？
      </p>
      <p class="i_p4">
        {{$config.name}}是一个以分享资源什么什么什么。。
      </p>
      <p class="i_p3">
        联系我们
      </p>
      <p class="i_p4">
        官方微信：lagua2016<br>
        官方微博：weibo.com/lagua2016<br>
        官方QQ群：2918381
      </p>
    </div>

  </section>

</template>

<script>
  export default {
    name: 'home-about',
    components: {
      'c-header': require('../components/c-header.vue'),
    },
  };
</script>

<style lang="less" rel="stylesheet/less" scoped>
  .layout-home-about {
    .tui-content {
      color: #666;
      .i_p1 {
        text-align: center;
        img {
          width: 264px;
          height: 264px;
          border-radius: 30px;
        }
      }
      .i_p2 {
        text-align: center;
        margin-top: 24px;
      }
      .i_p3 {
        font-size: 48px;
        margin-top: 60px;
      }
      .i_p4 {
        margin-top: 24px;
        line-height: 1.5;
      }
    }
  }
</style>