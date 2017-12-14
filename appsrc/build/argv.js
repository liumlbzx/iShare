const argv = {
	get(key){
		let returnValue = undefined;
		for(let v of process.argv)
		{
			if( v === '--'+key || v.indexOf('--'+key+'=') > -1)
			{
				let kvs = v.split('=');
				returnValue = kvs[1];
				break;
			}
		}
		return returnValue;
	},
	has(key){
		let returnValue = false;
		for(let v of process.argv)
		{
			if( v === '--'+key || v.indexOf('--'+key+'=') > -1)
			{
				returnValue = true;
				break;
			}
		}
		return returnValue;
	}
};
module.exports = argv;
