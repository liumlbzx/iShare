//todo 从服务器读取写入缓存
const datas = {

	100000: {
		name: '上海市',
		level: 1,
		children: [
			100100,
			100200,
		]
	},
	100100: {
		name: '浦东区'
	},
	100200: {
		name: '静安区'
	},
	200000: {
		name: '江苏省',
		level: 1,
		children: [
			200100,
			200200,
		]
	},
	200100: {
		name: '盐城市',
		level: 2,
		children: [
			200101,
			200102
		]
	},
	200101: {
		name: '亭湖区',
		level: 3
	},
	200102: {
		name: '盐都区',
		level: 3
	},
	200200: {
		name: '南京市',
		level: 2,
	},
};

export const areaDatas = datas;

export const levelDatas = (type='province', parentId=null)=>{
	let retDatas = {};
	if(type==='province')
	{
		for(let i in datas)
		{
			let v = datas[i];
			if(v.level==1)
			{
				retDatas[i] = {
					name: v.name,
					level: v.level
				};
			}
		}
	}
	else if(type==='city'||type==='area')
	{
		let cityIds = datas[parentId] ? datas[parentId].children : null;
		if(!cityIds)
		{
			retDatas = {};
		}
		else
		{
			for(let cityId of cityIds)
			{
				let v = datas[cityId];
				retDatas[cityId] = {
					name: v.name,
					level: v.level
				};
			}
		}
	}

	return retDatas;
};