function countdown(millisec) {
	var timer;
	timer = setInterval(function() {
		millisec = millisec-31;
		if(millisec == 0) {
			clearTimeout(timer);
			window.location.reload();
		}
		else {
			dMod = millisec % 86400000;
			hMod = dMod % 3600000;
			minMod = hMod % 60000;
			sMod = minMod % 1000;
			document.getElementById("timer").innerHTML = (millisec-dMod)/86400000 + " Tagen, " + (dMod-hMod)/3600000 + " Stunden, " + (hMod-minMod)/60000 + " min, " + (minMod-sMod)/1000 + " s und " + ("00"+sMod).slice(-3) + " ms" ;
		}
	},31);
}