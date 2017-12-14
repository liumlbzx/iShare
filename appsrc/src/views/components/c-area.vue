<template>

  <section class="layout-c-area">

    <div>

      <select @change="changeProvince">
        <option value="" :selected="selectProvince==''">-请选择省份/直辖市-</option>
        <option v-for="(v,k) of provinces" :selected="selectProvince==k" :value="k" :key="k">{{v.name}}</option>
      </select>

      <select @change="changeCity">
        <option value="" :selected="selectCity==''">-请选择市/区-</option>
        <option v-for="(v,k) of citys" :selected="selectCity==k" :value="k" :key="k">{{v.name}}</option>
      </select>

      <select @change="changeArea">
        <option value="" :selected="selectArea==''">-请选择县/区-</option>
        <option v-for="(v,k) of areas" :selected="selectArea==k" :value="k" :key="k">{{v.name}}</option>
      </select>

    </div>

  </section>

</template>

<script>
  export default {
    name: 'c-area',
    data(){
      return {
        provinces: {},
        citys: {},
        areas: {},
        selectProvince: '',
        selectCity: '',
        selectArea: '',
        $areaObj: null
      };
    },
    props: {
      propArea: ''
    },
    methods: {
      init(){
        require.ensure([],()=>{

          this.$areaObj = require('../../config/area');
          this.provinces = this.$areaObj.levelDatas('province');

          if(this.propArea && this.propArea.length>0)
          {
            let cns = this.propArea.split(',');
            if(cns[0])
            {
              for(let i in this.provinces)
              {
                let v = this.provinces[i];
                if(v.name === cns[0])
                {
                  this.selectProvince = i;
                }
              }
            }

            if(cns[1] && this.selectProvince)
            {
              this.citys = this.$areaObj.levelDatas('city', this.selectProvince);
              for(let i in this.citys)
              {
                let v = this.citys[i];
                if(v.name === cns[1])
                {
                  this.selectCity = i;
                }
              }
            }

            if(cns[2] && this.selectCity)
            {
              this.areas = this.$areaObj.levelDatas('area', this.selectCity);
              for(let i in this.areas)
              {
                let v = this.areas[i];
                if(v.name === cns[2])
                {
                  this.selectArea = i;
                }
              }
            }

          }

        });
      },

      emitChange(){
        this.$emit('change', [
          this.$areaObj.areaDatas[this.selectProvince] ? this.$areaObj.areaDatas[this.selectProvince].name : '',
          this.$areaObj.areaDatas[this.selectCity] ? this.$areaObj.areaDatas[this.selectCity].name : '',
          this.$areaObj.areaDatas[this.selectArea] ? this.$areaObj.areaDatas[this.selectArea].name : '',
        ]);
      },

      changeProvince(e){
        this.selectProvince = e.target.value;

        this.citys = this.$areaObj.levelDatas('city', this.selectProvince);
        this.selectCity = '';
        this.areas = {};
        this.selectArea = '';
        this.emitChange();
      },
      changeCity(e){
        this.selectCity = e.target.value;

        this.areas = this.$areaObj.levelDatas('area', this.selectCity);
        this.selectArea = '';
        this.emitChange();
      },
      changeArea(e){
        this.selectArea = e.target.value;

        this.emitChange();
      }
    },
    mounted(){
      this.init();
    }
  };
</script>

<style lang="less" rel="stylesheet/less">
  @import "../../assets/css/var";
  .layout-c-area {
    >div {
      padding: 50px;
      select {
        margin-bottom: 50px;
        display: block;
        width: 100%;
        background:  rgba(255,255,255,1);
        border: 1px solid #ccc; /*no*/
        border-radius: 0;
        padding: 20px;
        outline:none;
        &:last-of-type {
          margin-bottom: 0;
        }
      }
    }
  }
</style>