<template>

  <span class="layout-c-userlevel" v-show="propLevel">
    Lv.{{propLevel}}
  </span>

</template>

<script>
  export default {
    name: 'c-userlevel',
    props: [
      'propLevel'
    ]
  };
</script>

<style lang="less" rel="stylesheet/less" scoped>
  .layout-c-userlevel {
    font-size: 36px;
  }
</style>