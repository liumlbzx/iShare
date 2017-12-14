<template>

  <header class="c-header">
    <a class="i-left" v-show="propShowLeft" href="javascript:;" @click.prevent.stop="back">
      <i class="alifont af-xiangzuo"></i>
    </a>
    <h1 class="i-title">{{title}}</h1>
    <div class="i-right">
      <slot class="i-right" name="right"></slot>
    </div>
  </header>

</template>

<script>
  export default {
    name: 'c-header',
    data(){
      return {
      }
    },
    props: {
      'propTitle': {},
      'propShowLeft': {
        'default': true
      }
    },
    computed: {
      title(){
        return this.propTitle || this.$route.meta.title;
      }
    },
    methods: {
      back(){
        this.$root.triggerBack();
      }
    },
    mounted(){

    }
  };
</script>

<style lang="less" rel="stylesheet/less">
  //导航栏
  @import "../../assets/css/var";
  .c-header {
    @height: 132px;
    height: @height;
    background: @color-yellow-normal;
    border-bottom: 1px solid #ddd; /*no*/
    position: fixed;
    left:0;
    top: 0;
    width: 100%;
    z-index: 1;
    >.i-left {
      position: absolute;
      left: 0;
      height: @height;
      display: inline-flex;
      align-items:center;
      padding:0 48px;
      color: @color-black-normal;
      text-decoration: none;
      i {
        font-size: 60px;
        font-weight: 800;
      }
      &:active {
        background: #e4bf58;
      }
    }
    >.i-title {
      position: absolute;
      height: @height;
      display: inline-flex;
      align-items:center;
      justify-content:center;
      left: 300px;
      right: 300px;
      font-weight: 400;
      font-size: 48px;
    }
    >.i-right {
      height: @height;
      right: 0;
      position: absolute;
      padding:0 24px;
      display: inline-flex;
      align-items:center;
    }
  }
</style>