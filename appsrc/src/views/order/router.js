const moduleId = 'order';
let modules = {};

modules['order'] = require('./order.vue');
modules['ordermsg'] = require('./ordermsg.vue');

let children = [];
let routerArr = {
  'order': {title: '订单事宜'},
  'ordermsg': {title: '帮助'}
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



