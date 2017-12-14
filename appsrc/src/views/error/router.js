const moduleId = 'error';
let modules = {};

modules['404'] = require('./404.vue');

let children = [];
let routerArr = {
	'404': {title: '页面不存在', auth: false},
};
for(let k in routerArr)
{
	let meta = routerArr[k] || {};
	let cRouter = {
		path: k==='404' ? '*' : `/${moduleId}/${k}`,
		name: `${moduleId}/${k}`,
		component: modules[k],
		meta: meta
	};
	children.push(cRouter);
}

export default children;



