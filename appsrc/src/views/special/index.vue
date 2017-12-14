<template>

  <section class="layout-special-index tui-background-gray">

    <c-header></c-header>

    <section class="tui-content tui-padding-bottom">

      <div class="layout_my_special">
        <div class="i-title">
          <strong>我关注的圈子</strong>
          <a href="#">查看全部</a>
        </div>
        <div class="i-body">

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
                <p>圈子标题</p>
                <p>今日：111</p>
              </div>
            </router-link>
          </div>



        </div>
      </div>

      <div class="layout_recommend_special">
        <div class="i-title">
          <strong>推荐圈子</strong>
          <a href="#">所有</a>
        </div>
        <div class="i-body">
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
    name: 'special-index',
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
  .layout-special-index {
    .layout_my_special {
      background: #fff;
      >.i-title {
        padding: 50px;
        border-bottom: 1px solid #f6f6f6;/*no*/
        display: flex;
        align-items:center;
        strong {
          font-size: 48px;
          font-weight: 400;
          flex: 1;
          color: #353535;
        }
        a {
          color: @color-yellow-dark;
          font-size: 42px;
          font-weight: 200;
        }
      }
      >.i-body {

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
          display: flex;
          flex-wrap: wrap;
          > div {
            width: 50%;
            padding: 50px;
            display: inline-flex;
            align-items:center;
            &:active {
              background: #f9f9f9;
            }
            img {
              width: 120px;
              margin-right: 38px;
            }
            >div {
              flex: 1;
              >p:nth-of-type(1) {
                color: #353535;
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
    .layout_recommend_special {
      margin-top: 30px;
      background: #fff;
      >.i-title {
        padding: 50px;
        border-bottom: 1px solid #f6f6f6;/*no*/
        display: flex;
        align-items:center;
        strong {
          font-size: 48px;
          font-weight: 400;
          flex: 1;
          color: #353535;
        }
        a {
          color: @color-yellow-dark;
          font-size: 42px;
          font-weight: 200;
        }
      }
      >.i-body {
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
                font-weight: 200;
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