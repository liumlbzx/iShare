<template>

  <section class="tui-background-gray">

    <c-header></c-header>

    <div class="tui-content tui-padding-bottom">

      <div class="tui-form">

        <c-checked-toggle propLabel="帖子被回复" :propChecked="false" @change="changeTest"></c-checked-toggle>

        <c-checked-toggle propLabel="评论被回复" :propChecked="true" @change="changeTest"></c-checked-toggle>

        <c-checked-toggle propLabel="圈子热帖推送" :propChecked="true" @change="changeTest"></c-checked-toggle>

        <c-checked-toggle propLabel="私信提醒" :propChecked="true" @change="changeTest"></c-checked-toggle>

      </div>

    </div>

  </section>

</template>

<script>
  export default {
    name: 'user-notice-setting',
    data(){
      return {
      }
    },
    components: {
      'c-header': require('../components/c-header.vue'),
      'c-checked-toggle': require('../components/c-checked-toggle.vue')
    },

    methods: {
      changeTest(v){
        console.log(v);
      }
    },
    mounted(){
    }
  };
</script>
