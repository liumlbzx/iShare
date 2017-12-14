<style lang="less" rel="stylesheet/less" scoped>
  .layout-profile-edit {
    .i-userface {
      line-height: 0;
      margin: 0 auto;
      text-align: center;
      >p {
        border: 1px solid #ddd; /*no*/
        background:#fff;
        display: inline-flex;
        justify-content:center;
        align-items:center;
        width: 800px;
        height: 800px;
        img {
          max-width: 800px;
          max-height: 800px;
        }
      }
    }
  }
</style>

<template>

  <section class="tui-background-gray layout-profile-edit">

    <c-header :propTitle="propTitle">
    </c-header>

    <div class="tui-content">

      <form class="tui-form" name="form1">

        <template v-if="field==='face'">
          <input type="text" style="display: none;" v-model="value" maxlength="200" data-patter="^[\s\S]{2,30}$">
          <div class="i-userface">
            <p>
              <img :src="value" @click="chooseFace">
            </p>
          </div>
          <div class="i-tip" style="text-align: center;">
            头像禁止包含露点、色情、暴恐、政治内容哟～
          </div>
        </template>

        <template v-if="field==='nickname'">
          <div class="i-input-row">
            <input type="text" v-model="value" placeholder="请输入2-30位中文、英文、数字" maxlength="30" data-patter="^[\s\S]{2,30}$">
          </div>
          <div class="i-tip">
            <span class="color-red">一个月</span>只能修改一次昵称<br>
            昵称禁止包含色情、暴恐、政治词汇哟～
          </div>
        </template>

        <template v-if="field==='intro'">
          <div class="i-input-row">
            <input type="text" v-model="value" placeholder="100字以内介绍自己" maxlength="100" data-patter="^[\s\S]{0,100}$">
          </div>
          <div class="i-tip">
            <span class="color-red">一个月</span>只能修改一次介绍<br>
            禁止包含色情、暴恐、政治词汇哟～
          </div>
        </template>

        <template v-if="field==='sex'">
          <label class="i-radio-row" v-for="(v,k) of userAttrs['sex']" :key="k">
            <span>{{v}}</span>
            <i class="alifont af-gougou_1" :class="{checked:value==k}"></i>
            <input type="radio" name="sex" :value="k" :checked="value==k" v-model="value">
          </label>
        </template>

        <template v-if="field==='age'">
          <div class="i-input-row" @click="editAge">
            <input type="text" :value="formatAge" placeholder="选择你的出生年月吧" readonly>
          </div>
        </template>

        <template v-if="field==='friendwant'">
          <label class="i-radio-row" v-for="(v,k) of userAttrs['friendwant']" :key="k">
            <span>{{v}}</span>
            <i class="alifont af-gougou_1" :class="{checked:value==k}"></i>
            <input type="radio" name="sex" :value="k" :checked="value==k" v-model="value">
          </label>
        </template>

        <template v-if="field==='sexual'">
          <label class="i-radio-row" v-for="(v,k) of userAttrs['sexual']" :key="k">
            <span>{{v}}</span>
            <i class="alifont af-gougou_1" :class="{checked:value==k}"></i>
            <input type="radio" name="sex" :value="k" :checked="value==k" v-model="value">
          </label>
        </template>

        <template v-if="field==='marry'">
          <label class="i-radio-row" v-for="(v,k) of userAttrs['marry']" :key="k">
            <span>{{v}}</span>
            <i class="alifont af-gougou_1" :class="{checked:value==k}"></i>
            <input type="radio" name="sex" :value="k" :checked="value==k" v-model="value">
          </label>
        </template>

        <template v-if="field==='location'">
          <div class="i-input-row">
            <input type="text" v-model="value" placeholder="当前常活动的城市" readonly>
          </div>
          <c-area :propArea="value" v-on:change="changeLocation"></c-area>
        </template>

        <template v-if="field==='hometown'">
          <div class="i-input-row">
            <input type="text" v-model="value" placeholder="家乡" readonly>
          </div>
          <c-area :propArea="value" v-on:change="changeHomeTown"></c-area>
        </template>

      </form>

      <div class="tui-content-padding">
        <button class="tui-btn tui-btn-block tui-btn-yellow" @click="post">保存</button>
      </div>


    </div>


  </section>

</template>

<script>
  import {calcAge, calcXZ} from '../../models/user';

  const TITLE = {
    face: '头像',
    nickname: '昵称',
    intro: '个人简介',
    sex: '性别',
    age: '年龄',
    friendwant: '交友意向',
    sexual: '性取向',
    marry: '婚姻状况',
    location: '所在地',
    hometown: '家乡'
  };

  export default {
    name: 'user-profile-edit',
    data(){
      return {
        propTitle: '',
        field: '',
        value: '',
        userAttrs: this.$config.attrs.user,
      }
    },
    components: {
      'c-header': require('../components/c-header.vue'),
      'c-area': require('../components/c-area.vue'),
    },
    computed: {
      formatAge(){
        if(this.value)
        {
          let age = calcAge(this.value);
          let xz = calcXZ(this.value);
          return `${age}岁，${xz}`;
        }
        else
        {
          return null;
        }
      },

    },
    methods: {
      editAge(){
        let cDate = new Date();

        let date = this.value;
        if(date)
        {
          let splitStr;
          if( date.indexOf('-') > -1 )
          {
            splitStr = '-';
          }
          else if( date.indexOf('/') > -1 )
          {
            splitStr = '/';
          }
          else
          {
            return false;
          }

          let birth = date.split(splitStr);

          cDate.setFullYear(parseInt(birth[0]));
          cDate.setMonth(parseInt(birth[1])-1);
          cDate.setDate(parseInt(birth[2]));
        }


        let minDate = new Date();
        minDate.setFullYear(1920);
        let maxDate = new Date();

        plus.nativeUI.pickDate(
          (e)=>{
            let date = e.date;
            this.value = date.getFullYear()+'-'+(date.getMonth()+1)+'-'+date.getDate()
          },
          ()=>{},
          {
            title: '选择出生日期',
            date: cDate,
            minDate: minDate,
            maxDate: maxDate,
          }
        );
      },
      //更改常住地
      changeLocation(args){
        let s = '';
        if(args[0])
        {
          s = args[0];
          if(args[1])
          {
            s += ','+args[1];
            if(args[2])
            {
              s += ','+args[2];
            }
          }
        }
        this.value = s;
      },
      //更改家乡
      changeHomeTown(args){
        let s = '';
        if(args[0])
        {
          s = args[0];
          if(args[1])
          {
            s += ','+args[1];
            if(args[2])
            {
              s += ','+args[2];
            }
          }
        }
        this.value = s;
      },
      //选择头像
      chooseFace(){
        plus.gallery.pick(
          (file)=>{
            if(file)
            {
              let fileExtension = require('file-extension');
              let ext = fileExtension(file);
              if(ext!=='jpg' && ext!=='webp' && ext!=='png')
              {
                plus.nativeUI.alert('请选择jpg,webp,png图片');
                return false;
              }
              this.value = file;
            }
            else
            {
              plus.nativeUI.alert('图片返回失败');
            }
          },
          ()=>{},
          {
            filter: 'image',
            multiple: false,
            system: false
          }
        );
      },
      post(){
        //todo 如果是头像，判断value是否是file://开头，如果是，则先上传文件，然后再保存
        //
      }

    },
    mounted(){
      this.field = this.$route.query.field;

      this.value = this.$route.query.value;
      this.propTitle = '编辑'+TITLE[this.field];
    }
  };
</script>
