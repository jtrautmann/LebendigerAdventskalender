function UnCrypt(s, d) {
	var r='';
	for(var i=0; i<s.length; i++) { 
		n = s.charCodeAt(i);
		if(n>=8364) { n = 128; }
		r += String.fromCharCode(n-d); 
	}
	return r;
}

function linkTo_UnCryptLink(s, shift) {
	location.href = UnCrypt(s, shift);
}

function linkTo_UnCryptMailto(s, shift)	{
	location.href='mailto:' + UnCrypt(s, shift);
}