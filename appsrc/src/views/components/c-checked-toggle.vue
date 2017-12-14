<template>

  <div class="c-checked-toggle i-checked-toggle">
    <label>{{propLabel}}</label>
    <p :class="getClass" @click="toggle">
      <i></i>
    </p>
  </div>

</template>

<script>
  export default {
    name: 'c-checked-toggle',
    data(){
      return {
        checked: null
      }
    },
    props: {
      'propChecked': {},
      'propLabel': {}
    },
    computed: {
      getClass(){
        return this.checked ? 'checked': '';
      }
    },
    methods: {
      toggle(){
        this.checked = !this.checked;
        this.$emit('change', this.checked);
      }
    },
    mounted(){
      this.checked = !!this.propChecked;
    }
  };
</script>

<style lang="less" rel="stylesheet/less">
  //导航栏
  @import "../../assets/css/var";
  .c-checked-toggle {
    height: 180px;
    background: #fff;
    border-bottom:1px solid #eee; /*no*/
    display: flex;
    align-items:center;
    flex-wrap: wrap;
    padding: 0 30px;
    >label {
      color: @color-black-normal;
      font-size: 45px;
      flex: 1;
    }
    >p {
      height: 80px;
      width: 130px;
      border-radius: 40px;
      position: relative;
      background: #f6f6f6;
      border: 1px solid #d3d3d3; /*no*/
      >i {
        position: absolute;
        left: 4px;
        top: 4px;
        display: inline-block;
        width: 70px;
        height: 70px;
        border-radius: 50%;
        box-shadow: 0 0 15px rgba(0,0,0,.3);
        background: #fff;
        transition: transform .3s ease;
      }
      &.checked {
        background: @color-yellow-normal;
        border-color: @color-yellow-normal;
        i {
          transform: translate3d((130px-70px-10px), 0, 0);
        }
      }
    }
  }
</style>