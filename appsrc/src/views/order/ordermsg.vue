<style lang="less" rel="stylesheet/less" scoped>
  .tui-content{
    >h3{
      height:100px;
      text-align:center;
      line-height:100px;
    }
    >textarea{
      width:100%;
      height:400px;
      resize:none;
      padding:30px;
    }
  }
</style>
<template>

  <section class="layout-user-profile layout-has-data-show">

    <c-header></c-header>

    <div class="tui-content">
      <h3>{{value}}</h3>
      <textarea readonly>这里写内容</textarea>
    </div>

  </section>

</template>

<script>

  export default {
    name: 'order-ordermsg',
    data(){
      return {
        value: '',
      }
    },
    components: {
      'c-header': require('../components/c-header.vue')
    },
    methods: {
    },
    mounted(){
      this.value = this.$route.query.value;
    }
  };
</script>

<style lang="less" rel="stylesheet/less" scoped>

</style>