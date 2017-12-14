const moduleId = 'bbs';
let modules = {};

modules['detail'] = require('./detail.vue');

let children = [];
let routerArr = {
	'detail': {title: '帖子详情'},
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



