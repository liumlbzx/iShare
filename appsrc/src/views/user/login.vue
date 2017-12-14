<style lang="less" rel="stylesheet/less" scoped>
  .layout-user-login {
    .i-tip2 {
      position: absolute;
      left: 0;
      bottom: 60px;
      width: 100%;
      font-size: 36px;
      span {
        color: #eee;
        margin: 0 30px;
        font-size: 30px;
        position: relative;
        top: -6px;
      }
    }
  }
</style>
<template>

  <section class="layout-user-login">

    <c-header></c-header>

    <div class="tui-content">

      <c-app-intro-banner></c-app-intro-banner>

      <c-3rd-login style="margin-top: 50px;"></c-3rd-login>

      <div class="i-tip2">
        <p class="tui-text1">
          <router-link :to="{name:'home/index'}">{{$config.name}}是什么?</router-link><span>|</span><a href="javascript:;">{{$config.name}}用户协议</a>
        </p>
      </div>

    </div>

  </section>

</template>

<script>

  export default {
    name: 'user-login',
    data(){
      return {
      }
    },
    components: {
      'c-header': require('../components/c-header.vue'),
      'c-3rd-login': require('../components/c-3rd-login.vue'),
      'c-app-intro-banner': require('../components/c-app-intro-banner.vue'),
    },
    methods: {
      post(){
      },
    },
    mounted(){
    }
  };
</script>
