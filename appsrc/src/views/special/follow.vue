<template>

  <section class="layout-special-follow">

    <c-header></c-header>

    <section class="tui-content tui-padding-bottom">

      <div class="layout_special">

        <div class="i_empty">
          <img src="./img/special_empty.jpg">
          <p class="p1">您关注的圈子会在这里</p>
          <p class="p2">
            <router-link :to="{name:'empty'}" class="tui-btn tui-btn-yellow">查看全部圈子</router-link>
          </p>
        </div>

        <div class="i_list">
          <router-link :to="{name:'empty'}" tag="div" v-for="(v,k) of [1,2,3,4]" :key="k">
            <img src="http://img1.ffan.com/orig/T1fZ_vBybj1RCvBVdK">
            <div>
              <p>
                <strong>圈子标题</strong>
                <span>今日: 320</span>
              </p>
              <p>留图要留种，好人一生平安</p>
            </div>
          </router-link>
        </div>

      </div>

    </section>

  </section>

</template>

<script>
  export default {
    name: 'special-follow',
    components: {
      'c-header': require('../components/c-header.vue'),
    },
    data(){
      return {
      }
    },
    methods: {

    },
    mounted(){

    }
  };
</script>

<style lang="less" rel="stylesheet/less" scoped>
  @import "../../assets/css/var";
  .layout-special-follow {
    background: #fff;

    .layout_special {

      >.i_empty {
        padding: 50px;
        text-align: center;
        img {
          width: 164px;
        }
        .p1 {
          color: #a3a3a3;
          margin-top: 30px;
        }
        .p2 {
          margin-top: 45px;
          .tui-btn {
            color: @color-black-normal !important;
            letter-spacing: 0;
          }
        }
      }

      >.i_list {
        > div {
          display: flex;
          align-items:center;
          &:active {
            background: #f9f9f9;
          }
          img {
            width: 160px;
            margin-right: 50px;
            margin-left: 50px;
          }
          > div {
            flex: 1;
            padding: 50px 30px 50px 0;
            border-bottom: 1px solid #f6f6f6; /*no*/
            > p:nth-of-type(1) {
              strong {
                color: #353535;
              }
              span {
                float: right;
                color: @color-yellow-dark;
                font-size: 36px;
              }
            }
            >p:nth-of-type(2) {
              margin-top: 24px;
              color: #a3a3a3;
            }
          }
        }
      }

    }
  }
</style>