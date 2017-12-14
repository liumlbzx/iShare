<style lang="less" rel="stylesheet/less" scoped>
  .tui-data-show{
    >.i-group{
      a{
        color: #282121;
        >.i-data{
          border-bottom: 1px solid #f1f1f1;
          background: #fff;
          display: flex;
          align-items: center;
          height: 0.122222rem;
          padding: 0 0.022222rem;
          >.i_label{
            font-size: 0.041667rem
          }
          >.i_you{
            color: #282121;
            font-weight: 800;
            font-size: 0.044444rem;
            margin-left: 0.027778rem;
          }
          >.i_value {
            color: #666;
            flex:1;
            text-align: right;
            font-size: 45px;
            &.i_red {
              color: #ff3933;
            }
          }
        }
      }
    }
  }

</style>
<template>

  <section class="layout-user-profile layout-has-data-show">

    <c-header></c-header>

    <div class="tui-content">

      <div class="tui-data-show">

        <div class="i-group">
          <router-link  :to="{name: 'order/ordermsg', query:{ value: '修改收货信息'}}">
            <div class="i-data">
              <label class="i_label">修改收货信息</label>
              <p class="i_value"></p>
              <i class="i_you alifont af-xiangyou"></i>
            </div>
          </router-link>

          <router-link  :to="{name: 'order/ordermsg', query:{ value: '订单未受理'}}">
            <div class="i-data">
              <label class="i_label">订单未受理</label>
              <p class="i_value"></p>
              <i class="i_you alifont af-xiangyou"></i>
            </div>
          </router-link>

          <router-link  :to="{name: 'order/ordermsg', query:{ value: '发货地'}}">
            <div class="i-data">
              <label class="i_label">发货地</label>
              <p class="i_value"></p>
              <i class="i_you alifont af-xiangyou"></i>
            </div>
          </router-link>

          <router-link  :to="{name: 'order/ordermsg', query:{ value: '趣币订单什么时候发货'}}">
            <div class="i-data">
              <label class="i_label">趣币订单什么时候发货</label>
              <p class="i_value"></p>
              <i class="i_you alifont af-xiangyou"></i>
            </div>
          </router-link>

          <router-link  :to="{name: 'order/ordermsg', query:{ value: '趣币订单无法提交'}}">
            <div class="i-data">
              <label class="i_label">趣币订单无法提交</label>
              <p class="i_value"></p>
              <i class="i_you alifont af-xiangyou"></i>
            </div>
          </router-link>

          <router-link  :to="{name: 'order/ordermsg', query:{ value: '修改订单信息'}}">
            <div class="i-data">
              <label class="i_label">修改订单信息</label>
              <p class="i_value"></p>
              <i class="i_you alifont af-xiangyou"></i>
            </div>
          </router-link>

          <router-link  :to="{name: 'order/ordermsg', query:{ value: '无法购买某某优惠商品'}}">
            <div class="i-data">
              <label class="i_label">无法购买某某优惠商品</label>
              <p class="i_value"></p>
              <i class="i_you alifont af-xiangyou"></i>
            </div>
          </router-link>

          <router-link  :to="{name: 'order/ordermsg', query:{ value: '待发货订单是什么'}}">
            <div class="i-data">
              <label class="i_label">待发货订单是什么</label>
              <p class="i_value"></p>
              <i class="i_you alifont af-xiangyou"></i>
            </div>
          </router-link>

          <router-link  :to="{name: 'order/ordermsg', query:{ value: '怎么查看订单状态'}}">
            <div class="i-data">
              <label class="i_label">怎么查看订单状态</label>
              <p class="i_value"></p>
              <i class="i_you alifont af-xiangyou"></i>
            </div>
          </router-link>

        </div>



      </div>

    </div>

  </section>

</template>

<script>

  export default {
    name: 'order-order',
    data(){
      return {
      }
    },
    components: {
      'c-header': require('../components/c-header.vue')
    },
    methods: {
    },
    mounted(){
    }
  };
</script>

<style lang="less" rel="stylesheet/less" scoped>

</style>