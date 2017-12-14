<template>

  <section class="layout-c-detail-reply">

    <div class="layout_title">
      <i class="alifont af-pinglun"></i><strong>全部回复</strong>
      <a href="#" class="tui-btn tui-btn-yellow tui-btn-small">我要回复</a>
    </div>

    <div class="layout_body">

      <div class="layout_reply_list" v-for="(v,k) of [1,2,3,4]" :key="k">

        <div class="i-nickname">
          <a href="#">
            <img src="https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=294463859,3588585994&fm=26&gp=0.jpg">
            <strong>我是昵称</strong>
            <c-sexual propSex="2"></c-sexual>
          </a>
        </div>

        <div class="i-content">
          你要控制你自己
        </div>

        <div class="i-time">
          <span>1F</span>
          <span>2天前</span>
          <a href="#">
            <i class="alifont af-dianzan"></i>
            <em>12</em>
          </a>
        </div>

        <div class="i-replys">
          <div class="i_lists">
            <label>王蛋蛋</label>：恍恍惚惚红红火火
          </div>
          <div class="i_lists">
            <label>啊葡萄</label><span>回复</span><label>@王蛋蛋</label>：同样的
          </div>
          <div class="i_more">
            更多13条评论
          </div>
        </div>

      </div>

    </div>

    <div class="layout_ireply">
      <div contenteditable="true">说鸡不说八，文明你我他～</div>
      <button type="button" class="tui-btn tui-btn-red tui-btn-small" >回复</button>
    </div>

  </section>

</template>

<script>
  export default {
    name: 'c-detail-reply',
    props: [
      'propLevel'
    ],
    components: {
      'c-sexual': require('../components/c-sexual.vue'),
    }
  };
</script>

<style lang="less" rel="stylesheet/less" scoped>
  .layout-c-detail-reply {
    margin-top:45px;
    background: #fff;
    >.layout_title {
      padding: 50px;
      border-bottom:1px solid #f6f6f6; /*no*/
      color: #3a3a3a;
      display: flex;
      align-items:center;
      i {
        font-size: 48px;
        margin-right: 24px;
      }
      strong {
        flex: 1;
      }
      a {

      }
    }
    >.layout_body {
      padding: 25px 0;
    }
    .layout_reply_list {
      padding: 25px 50px;
      >.i-nickname {
        a {
          display: inline-flex;
          align-items:center;
          color: #3a3a3a;
          img {
            width: 84px;
            height: 84px;
            border-radius: 30px;
            background: #f6f6f6;
          }
          strong {
            margin: 0 15px 0 30px;
          }
        }
      }
      >.i-content {
        padding: 50px 0 0 114px;
      }
      >.i-time {
        display: flex;
        align-items:center;
        padding: 50px 0 0 114px;
        span {
          color: #a3a3a3;
          margin-right: 50px;
          &:nth-of-type(2) {
            flex: 1;
          }
        }
        a {
          color: #737373;
          display: inline-flex;
          align-items:center;
          i {
            margin-right: 18px;
            font-size: 60px;
          }
          em {
            font-style: normal;
          }
        }
      }
      >.i-replys {
        margin: 50px 0 0 114px;
        background: #f6f6f6;
        padding: 50px;
        border-radius: 9px;
        >.i_lists {
          margin-top: 30px;
          color: #737373;
          &:first-of-type {
            margin-top: 0;
          }
          label {
            color:#353535;
          }
          span {
            color: #737373;
            margin: 0 9px;
          }
        }
        >.i_more {
          color: #a3a3a3;
          margin-top: 45px;
        }
      }
    }
    >.layout_ireply {
      background-color: #f7f7f7;
      border-top: 1px solid #ddd; /*no*/
      padding: 20px;
      display: flex;
      align-items:center;
      > div {
        outline: none;
        background: #fff;
        flex: 1;
        border: 1px solid #e7e7e7;
        border-radius: 15px;
        resize: none;
        padding: 20px;
        box-sizing: content-box;
        min-height: 42px;
        max-height: 42px*3;
        overflow: auto;
        color: #ccc;
        &:focus {
          color: #333;
        }
      }
      >button {
        margin-left: 20px;
      }
    }
  }
</style>