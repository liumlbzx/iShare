import attrs from './config/attrs';

let env, //环境
		routerBasePath, //路由基础路径
		serverUrl; //服务器

env = location.port > 80 ? 'dev' : 'pro';
routerBasePath = env === 'dev' ? '/' : location.href.substr(0, location.href.indexOf('/index.html'));
serverUrl = env === 'dev' ? '/server/' : 'https://www.ishare.com/server/';

export default {
	name: '拉呱', //app名称
	version: '0.0.1', //当前版本
	attrs: attrs,
	api: {
		serverUrl: serverUrl,
		default: {
			verfy:  `${serverUrl}?r=default/verfy`, //验证码
		},
	},
	routerBasePath: routerBasePath,
};