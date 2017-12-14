
/**
 * 时间格式化
 * @param time
 * @param type
 * @param defaultVal
 * @returns {*}
 */
export const filterDate = (time, type = 'long', {defaultVal=undefined} = {} )=>{
	if( !time || time === '0' )
	{
		return defaultVal
	}

	let ret;
	let date;
	if( /^(\-?)\d{1,10}$/.test(time) )
	{
		date = new Date(time*1000);
	}
	else
	{
		date = new Date(time);
	}

	if(type === 'long')
	{
		ret = date.toLocaleString();
	}
	else if( type === 'date' )
	{
		ret = date.toLocaleDateString();
	}
	else if( type === 'time' )
	{
		ret = date.toLocaleTimeString();
	}
	else if( (typeof type) === 'string' )
	{
		ret = type
		.replace('yyyy', date.getFullYear())
		.replace('MM', (date.getMonth()+1).toString().padStart(2,'0') )
		.replace('dd', date.getDate().toString().padStart(2,'0') )
		.replace('HH', date.getHours().toString().padStart(2,'0') )
		.replace('ii', date.getMinutes().toString().padStart(2,'0') )
		.replace('ss', date.getSeconds().toString().padStart(2,'0') )
		;
	}
	else
	{
		ret = date.toString();
	}
	return ret;
};

/**
 * 默认图片
 * @param datas
 * @param thumb
 * @param col
 * @returns {*}
 */
export const filterThumb = (datas, thumb, col)=>{
	if(!thumb)
	{
		return datas;
	}
	if( datas instanceof Array)
	{
		for(let v of datas)
		{
			v[col] = v[col] ? v[col] : thumb;
		}
	}
	else
	{
		datas[col] = datas[col] ? datas[col] : thumb;
	}
	return datas;
};

//数组转object
export const arrayToObject = (datas, key) => {
	let retDatas = {};
	datas.forEach((value)=>{
		retDatas[value[key]] = value;
	});
	return retDatas;
};
