function popupCenter(url, title, w, h) {
	// Fixes dual-screen position						 Most browsers		Firefox
	var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
	var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

	var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
	var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

	var left = ((width / 2) - (w / 2)) + dualScreenLeft;
	var top = ((height / 2) - (h / 2)) + dualScreenTop;
	var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

	// Puts focus on the newWindow
	if (window.focus) {
		newWindow.focus();
	}
}

function nl2br(str) {
	return str.replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '<br>');
}

function displayLoader() {
	$("body").oLoader({
		wholeWindow: true, //makes the loader fit the window size
		lockOverflow: true, //disable scrollbar on body
		backgroundColor: "#000",
		fadeInTime: 1000,
		fadeLevel: 0.5,
		image: "/includes/images/ownageLoader/loader4.gif",
//		style: 3,
//		imagePadding: 5,
//		imageBgColor: "#fe0",
		hideAfter: 1500
	});
}

function showLoader() {
	$("body").oLoader({
		image: "/includes/images/ownageLoader/loader4.gif",
	});
}

function hideLoader() {
	$("body").oLoader("hide");
}


function phoneFormat( text ) {
	var phone = text.replace( /\D/g, "" );

	if ( phone.length == 8 ) {
		phone = phone.replace( /(\d{4})(\d{4})/, "$1-$2" );
	} else if ( phone.length == 9 ) {
		phone = phone.replace( /(\d{2})(\d{3})(\d{4})/, "$1-$2-$3" );
	} else if ( phone.length == 10 ) {
		phone = phone.replace( /(\d{3})(\d{3})(\d{4})/, "$1-$2-$3" );
	} else if ( phone.length == 11 ) {
		phone = phone.replace( /(\d{3})(\d{4})(\d{4})/, "$1-$2-$3" );
	}
	return phone;
}

function formatBytes(bytes) {
	var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
	if (bytes == 0) return '0 Byte';
	var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
	return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
}

function isEmail(email) {
	var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	return regex.test(email);
}

function numberFormat(number, decimals, decPoint, thousandsSep) { // eslint-disable-line camelcase
	number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
	var n = !isFinite(+number) ? 0 : +number
	var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
	var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
	var dec = (typeof decPoint === 'undefined') ? '.' : decPoint
	var s = ''

	var toFixedFix = function (n, prec) {
		var k = Math.pow(10, prec)
		return '' + (Math.round(n * k) / k)
			.toFixed(prec)
	}

	// @todo: for IE parseFloat(0.55).toFixed(0) = 0;
	s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.')
	if (s[0].length > 3) {
		s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
	}
	if ((s[1] || '').length < prec) {
		s[1] = s[1] || ''
		s[1] += new Array(prec - s[1].length + 1).join('0')
	}
	
	return s.join(dec)
}

function limitBytes(sendMsg, maxByte) {
	var chr = "", chrLength = 0, validMsgLength = 0, validChrLength = 0, validMsg = "", bytesVal = "";
	var maxBytes = $("#unsubscribe").prop("checked") ? maxByte - 23 : maxByte;
	
	for ( i = 0; i < sendMsg.length; i++ ) {
		chr = sendMsg.charAt(i);
		if (escape(chr).length > 4) {
			chrLength += 2;
		} else if (chr != "\r") {		// %0D%0A
			chrLength++;
		}
		if ( chrLength <= maxBytes ) {
			validMsgLength = i + 1;
			validChrLength = chrLength;
		}
	}
	if ( chrLength > maxBytes ) {
		alert( maxBytes +"바이트 이상의 메세지는 전송하실 수 없습니다.");
		validMsg = sendMsg.substr(0, validMsgLength);
		$("#message").val( validMsg );
		bytesVal = "<b>" + validChrLength + "</b> / "+ maxBytes;
	} else {
		bytesVal = "<b>" + chrLength + "</b> / "+ maxBytes;
	}
	
	if ( $("#byte-guage").length > 0 ) {
		$("#byte-guage").html( bytesVal );
	}
}