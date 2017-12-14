const moduleId = 'user';
let modules = {};

modules['login'] = require('./login.vue');
modules['index'] = require('./index.vue');
modules['profile'] = require('./profile.vue');
modules['profile-edit'] = require('./profile-edit.vue');
modules['setting'] = require('./setting.vue');
modules['notice-setting'] = require('./notice-setting.vue');

let children = [];
let routerArr = {
	'login': {title: '登录'},
	'index': {title: '个人中心'},
	'profile': {title: '个人资料'},
	'profile-edit': {title: '编辑个人资料'},
	'setting': {title: '设置'},
	'notice-setting': {title: '新消息通知'},
};
for(let k in routerArr)
{
	let meta = routerArr[k] || {};
	let cRouter = {
		path: `/${moduleId}/${k}`,
		name: `${moduleId}/${k}`,
		component: modules[k],
		meta: meta
	};
	children.push(cRouter);
}

export default children;



