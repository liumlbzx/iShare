//计算星座
export const calcXZ = (date)=>{
	date = decodeURIComponent(date);

	if(!/^\d{4}\-\d{1,2}\-\d{1,2}$/.test(date) && !/^\d{4}\/\d{1,2}\/\d{1,2}$/.test(date))
	{
		return null;
	}

	let splitStr;
	if( date.indexOf('-') > -1 )
	{
		splitStr = '-';
	}
	else if( date.indexOf('/') > -1 )
	{
		splitStr = '/';
	}
	else
	{
		return null;
	}

	let birth = date.split(splitStr);
	let bMonth = parseInt(birth[1]);
	let bDay = parseInt(birth[2]);

	let xz;
	switch (bMonth)
	{
		case 1:
			xz = bDay<=19 ? '摩羯': '水瓶';
			break;
		case 2:
			xz = bDay<=18 ? '水瓶': '双鱼';
			break;
		case 3:
			xz = bDay<=20 ? '双鱼': '白羊';
			break;
		case 4:
			xz = bDay<=20 ? '白羊': '金牛';
			break;
		case 5:
			xz = bDay<=20 ? '金牛': '双子';
			break;
		case 6:
			xz = bDay<=21 ? '双子': '巨蟹';
			break;
		case 7:
			xz = bDay<=22 ? '巨蟹': '狮子';
			break;
		case 8:
			xz = bDay<=22 ? '狮子': '处女';
			break;
		case 9:
			xz = bDay<=22 ? '处女': '天秤';
			break;
		case 10:
			xz = bDay<=22 ? '天秤': '天蝎';
			break;
		case 11:
			xz = bDay<=21 ? '天蝎': '射手';
			break;
		case 12:
			xz = bDay<=21 ? '射手': '摩羯';
			break;
		default:
			xz = '未知';
			break;
	}

	xz += '座';
	return xz;
};

//计算周岁
export const calcAge = (date)=>{
	date = decodeURIComponent(date);
	if(!/^\d{4}\-\d{1,2}\-\d{1,2}$/.test(date) && !/^\d{4}\/\d{1,2}\/\d{1,2}$/.test(date))
	{
		return null;
	}

	let splitStr;
	if( date.indexOf('-') > -1 )
	{
		splitStr = '-';
	}
	else if( date.indexOf('/') > -1 )
	{
		splitStr = '/';
	}
	else
	{
		return null;
	}

	let cDate = new Date();
	let cYear = cDate.getFullYear();
	let cMonth = cDate.getMonth()+1;
	let cDay = cDate.getDate();

	let birth = date.split(splitStr);
	let bYear = parseInt(birth[0]);
	let bMonth = parseInt(birth[1]);
	let bDay = parseInt(birth[2]);

	let age = cYear - bYear;
	if(bMonth>cMonth)
	{
		age--;
	}
	else if( bDay > cDay)
	{
		age--;
	}

	return age;
};