import Vue from 'vue';

import VueRouter from 'vue-router';
Vue.use(VueRouter);

//配置文件
import Config from './config';
Vue.prototype.$config = Config;

//路由
import routerHome from './views/home/router';
import routerUser from './views/user/router';
import routerSpecial from './views/special/router';
import routerBbs from './views/bbs/router';
import routerOrder from './views/order/router';

import RouterError from './views/error/router'; //404

const myRouter = new VueRouter({
	base: Config.routerBasePath,
	routes: [
		{
			path: '/',//默认路由
			redirect: {name:'home/index'}
		},
		...routerHome,
		...routerUser,
		...routerSpecial,
		...routerBbs,
		...routerOrder,
		
		{
			path: 'empty',
			name: 'empty',
			component: {
				template: '<div>正在开发中</div>'
			}
		},
		...RouterError,
	]
});

let keepAlive_include = [];
let keepAlive_exclude = [];

myRouter.options.routes.forEach((v,k)=>{
	if(v.name)
	{
		if(v.meta && v.meta.keepAlive===true)
		{
			keepAlive_include.push(v.name.replace('/', '-'));
		}
		else
		{
			keepAlive_exclude.push(v.name.replace('/', '-'));
		}
	}
});

new Vue({
	el: '#app',
	data: {
		routerAnim: 'routerIn',
		quitTime: 0,
		canBack: true, //允许回退
	},
	template: `
		<transition :name="routerAnim" v-on:after-leave="afterLeave">
			<keep-alive include="`+keepAlive_include.join(',')+`" exclude="`+keepAlive_exclude.join(',')+`">
				<router-view ></router-view>
			</keep-alive>
		</transition>
	`,
	router: myRouter,
	mounted(){

		document.addEventListener('plusready', ()=>{
			console.log('plus ready.');

			plus.key.addEventListener('backbutton', ()=>{
				console.log('按了返回键');
				this.triggerBack({showTip:true});
			});

		});

	},
	methods: {
		toast(str){
			if((typeof plus) !== 'undefined')
			{
				plus.nativeUI.toast(str);
			}
			else
			{
				alert(str);
			}
		},
		//返回
		triggerBack(opt={}){
			if( 'home/index' === this.$route.name )
			{
				if(opt.showTip===true)
				{
					let waitTime = 2000;
					//2秒内再按一次就退出
					if( (new Date()).getTime() - this.quitTime < waitTime )
					{
						plus.runtime.quit();
					}
					else
					{
						plus.nativeUI.toast('再按一次退出'+Config.name, {duration: Math.min(waitTime, 2000)});
						this.quitTime = (new Date()).getTime();
						window.setTimeout(()=>{
							this.quitTime = 0;
						}, waitTime);
					}
				}
				return false;
			}
			else
			{
				if(this.canBack===false)
				{
					return false;
				}
				else
				{
					this.canBack = false;
					this.routerAnim = 'routerOut';
					if(window.plus)
					{
						plus.webview.currentWebview().back();
					}
					else
					{
						this.$router.back();
					}
					return true;
				}
			}
		},
		afterLeave(){
			this.routerAnim = 'routerIn';
			this.canBack = true;
		}
	}
});

require('normalize.css');
require('../static/iconfont/iconfont.css');
require('./assets/css/ui.less'); //因为只用了mui的弹窗的js功能相关的css，但其他元素如按钮、表单等还是需要自己重写一份