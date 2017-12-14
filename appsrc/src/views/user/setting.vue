<template>

  <section class="tui-background-gray">

    <c-header></c-header>

    <div class="tui-content tui-padding-bottom">

      <div class="tui-data-show">

        <div class="i-group">

          <router-link class="i-data" tag="div" :to="{name:'user/notice-setting'}">
            <label class="i_label">新消息通知</label>
            <p class="i_value"></p>
            <i class="i_you alifont af-xiangyou"></i>
          </router-link>

        </div>

        <div class="i-group">

          <router-link class="i-data" tag="div" :to="{name:'empty'}">
            <label class="i_label">版本更新</label>
            <p class="i_value">当前版本 {{$config.version}}</p>
          </router-link>

          <router-link class="i-data" tag="div" :to="{name:'home/about'}">
            <label class="i_label">关于{{$config.name}}</label>
            <p class="i_value"></p>
            <i class="i_you alifont af-xiangyou"></i>
          </router-link>

          <router-link class="i-data" tag="div" :to="{name:'empty'}">
            <label class="i_label">清空图片缓存</label>
            <p class="i_value"></p>
            <i class="i_you alifont af-xiangyou"></i>
          </router-link>

          <router-link class="i-data" tag="div" :to="{name:'empty'}">
            <label class="i_label">清空历史数据</label>
            <p class="i_value"></p>
            <i class="i_you alifont af-xiangyou"></i>
          </router-link>

        </div>

      </div>

    </div>

  </section>

</template>

<script>
  export default {
    name: 'user-setting',
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
