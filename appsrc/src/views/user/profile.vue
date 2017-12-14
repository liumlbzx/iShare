<template>

  <section class="tui-background-gray">

    <c-header></c-header>

    <div class="tui-content tui-padding-bottom">

      <div class="tui-data-show">

        <div class="i-group">
          <router-link class="i-data i-img" tag="div" :to="{name:'user/profile-edit', query:{field: 'face', value: 'http://img1.ffan.com/orig/T1fZ_vBybj1RCvBVdK'}}">
            <label class="i_label">头像</label>
            <p class="i_value">
              <img src="http://img1.ffan.com/orig/T1fZ_vBybj1RCvBVdK">
            </p>
          </router-link>

        </div>

        <div class="i-group">
          <router-link class="i-data" tag="div" :to="{name:'user/profile-edit', query:{field: 'nickname', value: '刻录机'}}">
            <label class="i_label">昵称</label>
            <p class="i_value">刻录机</p>
            <i class="i_you alifont af-xiangyou"></i>
          </router-link>

          <router-link class="i-data" tag="div" :to="{name:'user/profile-edit', query:{field: 'intro'}}">
            <label class="i_label">一句话介绍自己</label>
            <p class="i_value">更改</p>
            <i class="i_you alifont af-xiangyou"></i>
          </router-link>
        </div>

        <div class="i-group">

          <router-link class="i-data" tag="div" :to="{name:'user/profile-edit', query:{field: 'sex',value:11}}">
            <label class="i_label">性别</label>
            <p class="i_value i_red">待完善</p>
            <i class="i_you alifont af-xiangyou"></i>
          </router-link>

          <router-link class="i-data" tag="div" :to="{name:'user/profile-edit', query:{field: 'age',value:'1987-12-08'}}">
            <label class="i_label">年龄</label>
            <p class="i_value">29</p>
            <i class="i_you alifont af-xiangyou"></i>
          </router-link>

          <router-link class="i-data" tag="div" :to="{name:'user/profile-edit', query:{field: 'friendwant'}}">
            <label class="i_label">交友意向</label>
            <p class="i_value i_red">待完善</p>
            <i class="i_you alifont af-xiangyou"></i>
          </router-link>

          <router-link class="i-data" tag="div" :to="{name:'user/profile-edit', query:{field: 'sexual'}}">
            <label class="i_label">性取向</label>
            <p class="i_value i_red">待完善</p>
            <i class="i_you alifont af-xiangyou"></i>
          </router-link>

          <router-link class="i-data" tag="div" :to="{name:'user/profile-edit', query:{field: 'marry'}}">
            <label class="i_label">婚恋状况</label>
            <p class="i_value i_red">待完善</p>
            <i class="i_you alifont af-xiangyou"></i>
          </router-link>

        </div>

        <div class="i-group">

          <router-link class="i-data" tag="div" :to="{name:'user/profile-edit', query:{field: 'location',value:'上海市,浦东区'}}">
            <label class="i_label">所在地</label>
            <p class="i_value">上海浦东</p>
            <i class="i_you alifont af-xiangyou"></i>
          </router-link>

          <router-link class="i-data" tag="div" :to="{name:'user/profile-edit', query:{field: 'hometown',value:'江苏省,盐城市,盐都区'}}">
            <label class="i_label">家乡</label>
            <p class="i_value">江苏盐城</p>
            <i class="i_you alifont af-xiangyou"></i>
          </router-link>

        </div>


      </div>

      <div class="tui-content-padding">
        <button class="tui-btn tui-btn-block tui-btn-red">退出当前账号</button>
      </div>

    </div>

  </section>

</template>

<script>
  export default {
    name: 'user-profile',
    data(){
      return {
      }
    },
    components: {
      'c-header': require('../components/c-header.vue'),
    },

    methods: {

    },
    mounted(){
    }
  };
</script>
