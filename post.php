<?php echo '<?xml version="1.0" encoding="UTF-8"?>'."\n" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">

<head>
<title>SfC Karlsruhe Adventskalender</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<link href='https://fonts.googleapis.com/css?family=Love+Ya+Like+A+Sister' rel='stylesheet' type='text/css'>
<style type="text/css">
html, body { margin: 0; padding: 0; }

#year {
	font-family: 'Love Ya Like A Sister';
	font-size: 26pt;
	color: #ffffee;
	top: 187px; left: 404px;
	}
#imgMap {
	width: 780px;
	height: 659px;
	background-image: url('pics/adventskalender.jpg');
}
#imgMap div {
	margin: 0;
	padding: 0; 
	position: absolute; 
}
#imgMap div a {
	position: absolute;
	width: 110px;
	height: 110px;
}
#imgMap div a:hover {
	margin: -1px 0 0 -1px;
	border: 1px solid #999;
}
.t {
	display: none;
	position: absolute;
	top: 1000px;
	left: 1000px;
}

#t14 { top: 57px; left: 171px; }
#t14 a:hover { background-image: url("pics/hover/14.jpg"); }
#t22 { top: 57px; left: 281px; }
#t22 a:hover { background-image: url("pics/hover/22.jpg"); }
#t3 { top: 57px; left: 391px; }
#t3 a:hover { background-image: url("pics/hover/3.jpg"); }
#t11 { top: 57px; left: 501px; }
#t11 a:hover { background-image: url("pics/hover/11.jpg"); }
#t20 { top: 57px; left: 611px; }
#t20 a:hover { background-image: url("pics/hover/20.jpg"); }

#t1 { top: 167px; left: 501px; }
#t1 a:hover { background-image: url("pics/hover/1.jpg"); }
#t13 { top: 167px; left: 611px; }
#t13 a:hover { background-image: url("pics/hover/13.jpg"); }

#t10 { top: 277px; left: 61px; }
#t10 a:hover { background-image: url("pics/hover/10.jpg"); }
#t24 { top: 277px; left: 171px; }
#t24 a:hover { background-image: url("pics/hover/24.jpg"); }
#t6 { top: 277px; left: 281px; }
#t6 a:hover { background-image: url("pics/hover/6.jpg"); }
#t9 { top: 277px; left: 391px; }
#t9 a:hover { background-image: url("pics/hover/9.jpg"); }
#t7 { top: 277px; left: 501px; }
#t7 a:hover { background-image: url("pics/hover/7.jpg"); }
#t15 { top: 277px; left: 611px; }
#t15 a:hover { background-image: url("pics/hover/15.jpg"); }

#t8 { top: 387px; left: 61px; }
#t8 a:hover { background-image: url("pics/hover/8.jpg"); }
#t2 { top: 387px; left: 171px; }
#t2 a:hover { background-image: url("pics/hover/2.jpg"); }
#t18 { top: 387px; left: 281px; }
#t18 a:hover { background-image: url("pics/hover/18.jpg"); }
#t23 { top: 387px; left: 391px; }
#t23 a:hover { background-image: url("pics/hover/23.jpg"); }
#t4 { top: 387px; left: 611px; }
#t4 a:hover { background-image: url("pics/hover/4.jpg"); }

#t16 { top: 497px; left: 61px; }
#t16 a:hover { background-image: url("pics/hover/16.jpg"); }
#t12 { top: 497px; left: 171px; }
#t12 a:hover { background-image: url("pics/hover/12.jpg"); }
#t5 { top: 497px; left: 281px; }
#t5 a:hover { background-image: url("pics/hover/5.jpg"); }
#t19 { top: 497px; left: 391px; }
#t19 a:hover { background-image: url("pics/hover/19.jpg"); }
#t17 { top: 497px; left: 501px; }
#t17 a:hover { background-image: url("pics/hover/17.jpg"); }
#t21 { top: 497px; left: 611px; }
#t21 a:hover { background-image: url("pics/hover/21.jpg"); }
</style>
</head>

<body>
<div id="imgMap">
<div id=year><?php date_default_timezone_set('Europe/Berlin'); echo date("Y"); ?></div>
<div id="t1"><a href="door.php?nr=1"></a></div>
<div id="t2"><a href="door.php?nr=2"></a></div>
<div id="t3"><a href="door.php?nr=3"></a></div>
<div id="t4"><a href="door.php?nr=4"></a></div>
<div id="t5"><a href="door.php?nr=5"></a></div>
<div id="t6"><a href="door.php?nr=6"></a></div>
<div id="t7"><a href="door.php?nr=7"></a></div>
<div id="t8"><a href="door.php?nr=8"></a></div>
<div id="t9"><a href="door.php?nr=9"></a></div>
<div id="t10"><a href="door.php?nr=10"></a></div>
<div id="t11"><a href="door.php?nr=11"></a></div>
<div id="t12"><a href="door.php?nr=12"></a></div>
<div id="t13"><a href="door.php?nr=13"></a></div>
<div id="t14"><a href="door.php?nr=14"></a></div>
<div id="t15"><a href="door.php?nr=15"></a></div>
<div id="t16"><a href="door.php?nr=16"></a></div>
<div id="t17"><a href="door.php?nr=17"></a></div>
<div id="t18"><a href="door.php?nr=18"></a></div>
<div id="t19"><a href="door.php?nr=19"></a></div>
<div id="t20"><a href="door.php?nr=20"></a></div>
<div id="t21"><a href="door.php?nr=21"></a></div>
<div id="t22"><a href="door.php?nr=22"></a></div>
<div id="t23"><a href="door.php?nr=23"></a></div>
<div id="t24"><a href="door.php?nr=24"></a></div>
</div>
<img class="t" src="pics/hover/1.jpg" alt="1"/>
<img class="t" src="pics/hover/2.jpg" alt="2"/>
<img class="t" src="pics/hover/3.jpg" alt="3"/>
<img class="t" src="pics/hover/4.jpg" alt="4"/>
<img class="t" src="pics/hover/5.jpg" alt="5"/>
<img class="t" src="pics/hover/6.jpg" alt="6"/>
<img class="t" src="pics/hover/7.jpg" alt="7"/>
<img class="t" src="pics/hover/8.jpg" alt="8"/>
<img class="t" src="pics/hover/9.jpg" alt="9"/>
<img class="t" src="pics/hover/10.jpg" alt="10"/>
<img class="t" src="pics/hover/11.jpg" alt="11"/>
<img class="t" src="pics/hover/12.jpg" alt="12"/>
<img class="t" src="pics/hover/13.jpg" alt="13"/>
<img class="t" src="pics/hover/14.jpg" alt="14"/>
<img class="t" src="pics/hover/15.jpg" alt="15"/>
<img class="t" src="pics/hover/16.jpg" alt="16"/>
<img class="t" src="pics/hover/17.jpg" alt="17"/>
<img class="t" src="pics/hover/18.jpg" alt="18"/>
<img class="t" src="pics/hover/19.jpg" alt="19"/>
<img class="t" src="pics/hover/20.jpg" alt="20"/>
<img class="t" src="pics/hover/21.jpg" alt="21"/>
<img class="t" src="pics/hover/22.jpg" alt="22"/>
<img class="t" src="pics/hover/23.jpg" alt="23"/>
<img class="t" src="pics/hover/24.jpg" alt="24"/>
</body>

</html>
