function FormatNumber(str) {
	str = str.toString();
	var strTemp = GetNumber(str);
	if (strTemp.length <= 3)
		return strTemp;
	strResult = "";
	for (var i = 0; i < strTemp.length; i++)
		strTemp = strTemp.replace(".", "");
	var m = strTemp.lastIndexOf(",");
	if (m == -1) {
		for (var i = strTemp.length; i >= 0; i--) {
			if (strResult.length > 0 && (strTemp.length - i - 1) % 3 == 0)
				strResult = "." + strResult;
			strResult = strTemp.substring(i, i + 1) + strResult;
		}
	}
	else {
		//phan nguyen
		var strphannguyen = strTemp.substring(0, strTemp.lastIndexOf(","));
		var strphanthapphan = strTemp.substring(strTemp.lastIndexOf(","), strTemp.length);
		//phan thap phan
		var tam = 0;
		for (var i = strphannguyen.length; i >= 0; i--) {

			if (strResult.length > 0 && tam == 4) {
				strResult = "." + strResult;
				tam = 1;
			}


			strResult = strphannguyen.substring(i, i + 1) + strResult;
			tam = tam + 1;
		}
		strResult = strResult + strphanthapphan;
	}

	return strResult;
}
function GetNumber(str) {
	var count = 0;

	for (var i = 0; i < str.length; i++) {
		var temp = str.substring(i, i + 1);
		if (!(temp == "." || temp == "," || (temp >= 0 && temp <= 9))) {
			return str.substring(0, i);
		}
		if (temp == " ")
			return str.substring(0, i);
		if (temp == ",") {
			if (count > 0)
				return str.substring(0, i);
			count++;
		}
	}
	if(str > 0){
		str.replace(".", "");
	}
	return str;
}

function IsNumberInt(str) {
	for (var i = 0; i < str.length; i++) {
		var temp = str.substring(i, i + 1);
		if (!(temp == "," || (temp >= 0 && temp <= 9))) {
			alert("Vui lòng nhập số (0-9)!");
			return str.substring(0, i);
		}
		if (temp == ".") {
			alert("Báº¡n sá»­ dá»¥ng dáº¥u . náº¿u muá»‘n nháº­p sá»‘ láº»!");
			return str.substring(0, i);
		}
		//		            if(temp == " " || temp == ",")
		//		                return str.substring(0, i);
	}
	return str;
}

function NumberIntt(str) {
	for (var i = 0; i < str.length; i++) {
		var temp = str.substring(i, i + 1);
		if (!(temp >= 0 && temp <= 9)) {
			return str.substring(0, i);
		}
		if (temp == " ")
			return str.substring(0, i);
	}
	return str;
}

function ConvertPriceText(strTemp) {

	strTemp = strTemp.replace(/,/g, "");
	var priceTy = parseInt(strTemp / 1000000000, 0)
	var priceTrieu = parseInt((strTemp % 1000000000) / 1000000, 0)
	var priceNgan = parseInt(((strTemp % 1000000000)) % 1000000 / 1000, 0)
	var priceDong = parseInt(((strTemp % 1000000000)) % 1000000 % 1000, 0)
	var strTextPrice = ""
	if (strTemp == "" || strTemp == "0")
		strTextPrice = "Thương lượng";
	if (priceTy > 0 && parseInt(strTemp, 0) > 900000000)
		strTextPrice = strTextPrice + "<b>" + priceTy + "</b> tỷ"
	if (priceTrieu > 0)
		strTextPrice = strTextPrice + "<b>" + priceTrieu + "</b> triệu "
	if (priceNgan > 0)
		strTextPrice = strTextPrice + "</b>" + priceNgan + "</b> ngàn "
	if (document.getElementById("currency").value == "vnd") {
		if (priceTy > 0 || priceTrieu > 0 || priceNgan > 0 || priceDong > 0)
			strTextPrice = strTextPrice + "<b>VNĐ</b>"
	}
	if (document.getElementById("currency").value == "usd") {
		if (priceDong > 0)
			strTextPrice = strTextPrice + priceDong
		if (priceTy > 0 || priceTrieu > 0 || priceNgan > 0 || priceDong > 0)
			strTextPrice = FormatNumber(strTemp) + "<b> USD</b>"
	}
	if (document.getElementById("currency").value == "sjc") {
		if (priceDong > 0)
			strTextPrice = strTextPrice + priceDong
		if (priceTy > 0 || priceTrieu > 0 || priceNgan > 0 || priceDong > 0)
			strTextPrice = FormatNumber(strTemp) + "<b> lượng SJC</b>"
	}
	if (document.getElementById("measures").value == "tongdientich") {
		strTextPrice = strTextPrice + "<b> / Tổng diện tích</b>";
	}
	if (document.getElementById("measures").value == "m2") {
		strTextPrice = strTextPrice + "<b> / Mét vuông</b>";
	}
	if (document.getElementById("measures").value == "thang") {
		strTextPrice = strTextPrice + "<b> / Tháng</b>";
	}
	document.getElementById("price_text").innerHTML = strTextPrice
}
function FocusObj(name) {
	document.getElementById(name).focus()
}
function ChangeCurrency(loai, id) {
	jQuery('#mh4vn' + id).hide();
	jQuery('#mh4usd' + id).hide();
	jQuery('#mh4vang' + id).hide();
	switch (loai) {
		case 1:
			jQuery('#mh4vn' + id).show();
			break;
		case 2:
			jQuery('#mh4usd' + id).show();
			break;
		case 3:
			jQuery('#mh4vang' + id).show();
			break;
	}
}