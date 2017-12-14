const moduleId = 'special';
let modules = {};

modules['index'] = require('./index.vue');
modules['list'] = require('./list.vue');
modules['follow'] = require('./follow.vue');

let children = [];
let routerArr = {
	'index': {title: '圈子'},
	'list': {title: '所有圈子'},
	'follow': {title: '关注的圈子'},
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



