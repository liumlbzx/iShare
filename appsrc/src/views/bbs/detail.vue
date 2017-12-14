<template>

  <section class="layout-bbs-detail tui-background-gray">

    <c-header></c-header>

    <section class="tui-content">

      <div class="layout_sender">

        <router-link class="i_face" :to="{name:'empty'}" tag="div">
          <img src="https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=294463859,3588585994&fm=26&gp=0.jpg">
        </router-link>

        <router-link class="i_pos1" :to="{name:'empty'}" tag="div">
          <p class="i_nickname">我是妙语</p>
          <div class="i_pos1_1">
            <c-sexual propSex="2"></c-sexual>
            <c-userlevel propLevel="34"></c-userlevel>
          </div>
        </router-link>

        <div class="i_time">
          <span>08-06</span>
          <span>太原市</span>
        </div>

      </div>

      <div class="layout_detail">

        <div class="i-title">
          熊孩子说：好吃不胖
        </div>

        <div class="i-content">
          <p>
            上周末和舅舅家的熊孩子去吃火锅<br>上周末和舅舅家的熊孩子去吃火锅<br>
            上周末和舅舅家的熊孩子去吃火锅<br>上周末和舅舅家的熊孩子去吃火锅
          </p>
          <p>
            <img src="https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=294463859,3588585994&fm=26&gp=0.jpg">
          </p>
          <p>
            拍摄了好多照片
          </p>
          <div class="i-mulpics">
            <span>
              <img src="https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=294463859,3588585994&fm=26&gp=0.jpg">
            </span>
            <span>
              <img src="https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=294463859,3588585994&fm=26&gp=0.jpg">
            </span>
            <span>
              <img src="https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=294463859,3588585994&fm=26&gp=0.jpg">
            </span>
            <span>
              <img src="https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=294463859,3588585994&fm=26&gp=0.jpg">
            </span>
            <span>
              <img src="https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=294463859,3588585994&fm=26&gp=0.jpg">
            </span>
            <span>
              <img src="https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=294463859,3588585994&fm=26&gp=0.jpg">
            </span>
            <span>
              <img src="https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=294463859,3588585994&fm=26&gp=0.jpg">
            </span>
            <span>
              <img src="https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=294463859,3588585994&fm=26&gp=0.jpg">
            </span>
            <span>
              <img src="https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=294463859,3588585994&fm=26&gp=0.jpg">
            </span>
          </div>
        </div>

        <div class="i-buytip">
          作者设置了更多内容付费查看&nbsp;&nbsp;<button class="tui-btn tui-btn-red tui-btn-small">确认付费吗</button>
        </div>

        <div class="i-content" v-if="1==0">
          <p>
            付费照片
          </p>
          <div class="i-mulpics">
            <span>
              <img src="https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=294463859,3588585994&fm=26&gp=0.jpg">
            </span>
            <span>
              <img src="https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=294463859,3588585994&fm=26&gp=0.jpg">
            </span>
            <span>
              <img src="https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=294463859,3588585994&fm=26&gp=0.jpg">
            </span>
            <span>
              <img src="https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=294463859,3588585994&fm=26&gp=0.jpg">
            </span>
            <span>
              <img src="https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=294463859,3588585994&fm=26&gp=0.jpg">
            </span>
            <span>
              <img src="https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=294463859,3588585994&fm=26&gp=0.jpg">
            </span>
            <span>
              <img src="https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=294463859,3588585994&fm=26&gp=0.jpg">
            </span>
            <span>
              <img src="https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=294463859,3588585994&fm=26&gp=0.jpg">
            </span>
            <span>
              <img src="https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=294463859,3588585994&fm=26&gp=0.jpg">
            </span>
          </div>
        </div>

      </div>

      <div class="layout_cnts">
        <a href="#" @click.prevent="$root.toast(`查看了92次`)"><i class="alifont af-chakan"></i><em>92</em></a>
        <a href="#" @click.prevent="$root.toast(`付费了3次`)"><i class="alifont af-jinbi"></i><em>3</em></a>
        <a href="#"><i class="alifont af-gift"></i><em>12</em></a>
      </div>

      <div class="layout_cnts1">
        <p>送礼更能表达心意哟~</p>
        <a href="#"><i class="alifont af-dianzan"></i><em>999</em></a>
        <a href="#"><i class="alifont af-shoucang"></i><em>99</em></a>
        <a href="#"><i class="alifont af-pinglun"></i><em>91</em></a>
      </div>

      <c-detail-reply></c-detail-reply>

    </section>

  </section>

</template>

<script>
  export default {
    name: 'bbs-detail',
    components: {
      'c-header': require('../components/c-header.vue'),
      'c-sexual': require('../components/c-sexual.vue'),
      'c-userlevel': require('../components/c-userlevel.vue'),
      'c-detail-reply': require('./c-detail-reply.vue'),
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
  .layout-bbs-detail {
    >.tui-content {
      padding-bottom: 138px;
    }
    .layout_sender {
      padding: 50px;
      display: flex;
      align-items: flex-start;
      background: #fff;
      >.i_face {
        img {
          width: 100px;
          height: 100px;
          border-radius: 50%;
        }
      }
      >.i_pos1 {
        flex: 1;
        margin: 0 30px;
        >.i_nickname {
          height:50px;
          color: #353535;
          font-weight: 600;
        }
        >.i_pos1_1 {
          height: 50px;
          span {
            margin-right: 9px;
          }
        }
      }
      >.i_time {
        display: inline-flex;
        color: #a3a3a3;
        font-size: 39px;
        padding-top: 50px;
        span {
          margin-left: 15px;
        }
      }

    }
    .layout_detail {
      background: #fff;
      padding: 50px 50px 0 50px;
      >.i-title {
        font-size: 48px;
      }
      >.i-content {
        margin-top: 50px;
        color: #333;
        line-height: 1.6;
        p {
          margin-bottom: 30px;
        }
        img {
          max-width: 100%;
        }
        .i-mulpics {
          margin-bottom: 30px;
          display: flex;
          flex-wrap: wrap;
          justify-content: space-between;
          > span {
            width: 315px;
            height: 315px;
            border-radius: 18px;
            overflow: hidden;
            margin-bottom: 18px;
            justify-content:center;
            align-items:center;
            background: #f6f6f6;
            img {
              max-width: 315px;
              max-height: 315px;
            }
          }
        }
      }
      >.i-buytip {
        padding: 30px;
        border: 1px dashed #ddd; /*no*/
        background: #fff;
        color: #a5a5a5;
      }
    }
    .layout_cnts,.layout_cnts1 {
      background: #fff;
      display: flex;
      justify-content: flex-end;
      align-items:center;
      > a {
        padding: 0 45px;
        text-align: center;
        > i {
          display: flex;
          align-items:center;
          justify-content:center;
          width: 100%;
          height: 90px;
          font-size: 66px;
          color: @color-black-normal;
        }
        > em {
          display: flex;
          font-style: normal;
          height: 48px;
          align-items:center;
          justify-content:center;
          font-size: 36px;
          color: #3f3f3f;
          position: relative;
        }
        &:active,&.active {
          i,em {
            color: @color-yellow-dark;
          }
        }
      }
    }
    .layout_cnts {
      padding: 30px 0;
    }
    .layout_cnts1 {
      background: #fff;
      position: fixed;
      bottom: 0;
      width: 100%;
      left:0;
      height: 138px;
      overflow: hidden;
      > p {
        flex:1;
        padding-left: 50px;
        color: #a3a3a3;
      }
    }
  }
</style>