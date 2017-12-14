<template>

  <span class="layout-c-sexual">
    <i v-show="propSex==1" class="alifont af-lanmeixingbienan i_man"></i>
    <i v-show="propSex==2" class="alifont af-lanmeixingbienv i_women"></i>
  </span>

</template>

<script>
  export default {
    name: 'c-sexual',
    props: [
      'propSex'
    ]
  };
</script>

<style lang="less" rel="stylesheet/less" scoped>
  .layout-c-sexual {
    i {
      /*width: 54px;*/
      /*height: 54px;*/
      /*border-radius: 50%;*/
      /*display: inline-flex;*/
      /*align-items:center;*/
      /*justify-content:center;*/
      /*color: #fff;*/
      /*text-shadow: 0 2px 2px rgba(0,0,0,.8); !*no*!*/
      font-size: 42px;
      font-weight: 800;
      &.i_man {
        //background: linear-gradient(-180deg, #666, #333);
        color: #3e6500;
      }
      &.i_women {
        //background: linear-gradient(-180deg, #eee4e9, #f8cae2);
        color: #f8cae2;
      }
    }
  }
</style>