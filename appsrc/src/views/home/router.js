const moduleId = 'home';
let modules = {};

modules['index'] = require('./index.vue');
modules['about'] = require('./about.vue');

let children = [];
let routerArr = {
	'index': {title: '首页'},
	'about': {title: '关于我们'},
};
for(let k in routerArr)
{
	let meta = routerArr[k];
	let cRouter = {
		path: `/${moduleId}/${k}`,
		name: `${moduleId}/${k}`,
		component: modules[k],
		meta: meta
	};
	children.push(cRouter);
}

export default children;



