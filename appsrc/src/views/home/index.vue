<template>

  <section class="cmain">

    <c-header :propShowLeft="false"></c-header>

    <div class="tui-content" style="padding: 20px;font-size: 16px;">
      <router-link :to="{name:'user/login'}">登录</router-link>&nbsp;&nbsp;&nbsp;&nbsp;
      <router-link :to="{name:'user/profile'}">个人资料</router-link>&nbsp;&nbsp;&nbsp;&nbsp;
    </div>

  </section>

</template>

<script>
  export default {
    name: 'home-index',
    components: {
      'c-header': require('../components/c-header.vue')
    },
    data(){
      return {
      }
    },
    methods: {

    },
    mounted(){
      console.log(1111111);
    }
  };
</script>

<style lang="less" rel="stylesheet/less" scoped>
  .cmain {
    a {
    }
  }
</style>