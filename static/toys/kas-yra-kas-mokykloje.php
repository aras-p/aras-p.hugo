<HTML>
<HEAD>
	<META content="text/html; charset=UTF-8" http-equiv="Content-Type">
	<TITLE>Kas Yra Kas Mokykloje?</TITLE>
	<style type="text/css">

body {
	color: #000000;
	background-color: #ffffff;
	padding: 0;
	margin: 5% 10% 5% 10%;
	text-align: center;
	font-family: Helvetica, Arial;
}
.title {
	border: 1px dashed #404040;
	margin-right: 40%;
	padding: 10px;
	font-weight: bold;
	font-size: x-large;
	background-color: #40a0ff;
	position: relative;
	left: -50px;
	top: 10px;
	z-index: 1;
}
.ver {
	font-size: x-small;
	font-weight: normal;
	color: #404040;
}
.background {
	border: 1px dashed #808080;
	padding: 30px 5% 30px 5%;
	background-color: #f0f0f0;
}
.form {
	color: #004000;
	background-color: #ffe080;
	border: 1px solid;
	padding: 10px 20px 10px 20px;
	margin: 50px 1% 0px 1%;
}
div.papertitle {
	padding: 10px;
	border: 1px solid #c0c0c0;
	background-color: #e8e8ff;
}
div.subjtitle {
	margin-bottom: 20px;
}
span.subjtitle {
	font-weight: bold;
}
SPAN.subj {
	font-weight: bold;
}

IMG {
	border-width: 0px
}

A:link {
	text-decoration: underline;
	color: #0000a0;
}
A:visited {
	text-decoration: underline;
	color: #6000a0;
}
A:hover {
	text-decoration: underline;
	color: #0000c0;
	background-color: #f0f0f0;
}

.title A:link {
	text-decoration: underline;
	color: inherit;
}
.title A:visited {
	text-decoration: underline;
	color: inherit;
}
.title A:hover {
	text-decoration: underline;
	color: inherit;
	background-color: inherit;
}


.normal {
	font-size: small;
	text-align: justify;
}

.footer {
	padding-top: 50px;
	font-size: xx-small;
	font-family: Verdana, Arial, Helvetica, "Sans Serif";
	text-align: right;
}

</style>

  <script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-299873-3']);
    _gaq.push(['_trackPageview']);

    (function() {
      var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
      ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
  </script>

</HEAD>
<body>

<div class="title"><a href="kas-yra-kas-mokykloje.php">Kas Yra Kas Mokykloje?</a><div class="ver">v1.0.0 (2013 09 02)</div></div>
<div class="background">
<?php
	if( isset( $_GET["v"] ) ) {
		$p = trim( $_GET["v"] );
		if( strlen($p) > 0 ) {
			$aFill1 = array( 'tai', 'yra', '- tai', '- tai' );
			$aAdj0 = array(
				'truputį', 'šiek tiek', 'kartais', 'dažnai',
				'labai', 'retai', 'neblogai', 'gerai',
				'silpnai', 'pusėtinai', 'smarkiai', 'gražiai',
				'', '', '', '' );
			$aAdj1 = array(
				'pūkuotas', 'juokingas', 'trenktas', 'išsipuošęs',
				'valgomas', 'permatomas', 'žavingas', 'geras',
				'minkštas', 'nekoks', 'kietas' );
			$aWhat1 = array(
				'sąsiuvinis', 'laidas', 'fotelis', 'televizorius',
				'ekranas', 'stalas', 'puodas', 'kamuolys',
				'stiklainis', 'kompaktas', 'augalas', 'gyvūnas',
				'šuo', 'katinas', 'stalčius' );
			// ---------------------------------------------------------------------------------------------------
			$aFill2_1 = array( ', kuris', '; jis', ', tačiau jis', ', taip pat jis', ', bet visgi jis', ', o kartais jis' );
				$a2_1_b = array(
					'lengvesnis už', 'panašus į', 'lengvai telpa į', 'gali valgyti',
					'gali pakelti', 'mėgsta', 'daug didesnis už', 'stato', 'lanksto' );
				$a2_1_c = array(
					'bet kokį', 'kiekvieną', 'bet kurį', 'pirmą pasitaikiusį',
					'patį geriausia', 'pigiausią', 'brangiausią', 'patį populiariausią',
					'mažiausiai naudojamą', 'labiausiai mėgstamą' );
				$a2_1_d = array(
					'plaukiojantį', 'šokinėjantį', 'žiovaujantį', 'valgantį',
					'rašantį', 'visada šviežią', 'amžinai sugedusį' );
				$a2_1_e = array(
					'mokytoją', 'zuikį', 'draugą', 'klasioką', 'plauką',
					'miestą', 'dainininką', 'kirpėją' );
			// ---------------------------------------------------------------------------------------------------
			$a3_1 = array( 'Jo neįmanoma', 'Jį labai sunku', 'Jo negalima', 'Jį patartina', 'Jį galimą', 'Draužiama jį',
					'Rekomenduojama jį', 'Verta jį' );
				$a3_1_a = array(
					'valgyti', 'spaudinėti', 'pastebėti', 'išardyti', 'mėtyti',
					'pagauti', 'vartoti', 'nusiimti',
					'saugoti' );
				$a3_1_b = array(
					'net ir vaikams', 'pirmadieniais', 'su tėvų priežiūra',
					'net ir mokykloje', 'be dantų', 'klasėje', 'palikus nosį namie',
					'gryname ore' );
			// ---------------------------------------------------------------------------------------------------
			$a4_1 = array( 'Jis sužino', 'Jis keičia', 'Jis tirpdo', 'Jis gadina', 'Jis supranta', 'Jis moka' );
				$a4_1_a = array(
					'visas', 'bet kokias', 'lengvai įsimenamas',
					'nepamainomas', 'įkyrias', 'medines', 'fantastiškas', 'gražias',
					'ypač slaptas', 'pačias keisčiausias', 'visas juokingas' );
				$a4_1_b = array(
					'kojines', 'bobutes', 'mokytojas', 'plyteles',
					'kates', 'instrukcijas', 'komandas', 'knygas',
					'dujines virykles', 'boružėles', 'spalvas', 'televizijos laidas',
					'Interneto svetaines', 'ausų formas', 'savaitės dienas' );
			$aFillEnd = array( '.', '...', '!', '.', '!!!', '!', '?!', ' ;)', '.' );
			
			$p = ucfirst($p);
			$phash = md5($p);
			$p = htmlspecialchars($p);
			print "<div class='subjtitle'><span class='subjtitle'>$p</span></div>";
			
			$r = "<span class='subj'>$p</span> ";
			$j = hexdec($phash[0]);	$r .= $aFill1[ $j % count($aFill1) ]; $r .= ' ';
			$j = hexdec($phash[1]);	$r .= $aAdj0[ ($j+3) % count($aAdj0) ]; $r .= ' ';
			$j = hexdec($phash[2]);	$r .= $aAdj1[ $j % count($aAdj1) ]; $r .= ' ';
			$j = hexdec($phash[3]);	$r .= $aWhat1[ $j % count($aWhat1) ];
			$j = hexdec($phash[11]); if ($j >= 4) {
				$j = hexdec($phash[4]);	$r .= $aFill2_1[ $j % count($aFill2_1) ]; $r .= ' ';
				$j = hexdec($phash[2])+hexdec($phash[3]); $r .= $aAdj0[ ($j+3) % count($aAdj0) ]; $r .= ' ';
				$j = hexdec($phash[5]);	$r .= $a2_1_b[ $j % count($a2_1_b) ]; $r .= ' ';
				$j = hexdec($phash[6]);	if( $j < 8 ) { $r .= $a2_1_c[ $j % count($a2_1_c) ]; $r .= ' '; };
				$j = hexdec($phash[7]);	if( $j >= 8 ) { $r .= $a2_1_d[ $j % count($a2_1_d) ]; $r .= ' '; };
				$j = hexdec($phash[8]);	$r .= $a2_1_e[ $j % count($a2_1_e) ];
			}
			$j = hexdec($phash[9]);	$r .= $aFillEnd[ $j % count($aFillEnd) ]; $r .= ' ';
			
			$j = hexdec($phash[10]);	if( $j >= 4 ) {
				$j = hexdec($phash[11]);	$r .= $a3_1[ $j % count($a3_1) ]; $r .= ' ';
				$j = hexdec($phash[12]);	$r .= $a3_1_a[ $j % count($a3_1_a) ]; $r .= ' ';
				$j = hexdec($phash[13]);	$r .= $a3_1_b[ $j % count($a3_1_b) ];
				$j = hexdec($phash[14]);	$r .= $aFillEnd[ $j % count($aFillEnd) ]; $r .= ' ';
			}
			$j = hexdec($phash[13]); if ($j >= 4) {
				$j = hexdec($phash[12]);	$r .= $a4_1[ $j % count($a4_1) ]; $r .= ' ';
				$j = hexdec($phash[14]);	$r .= $a4_1_a[ $j % count($a4_1_a) ]; $r .= ' ';
				$j = hexdec($phash[0]);	$r .= $a4_1_b[ $j % count($a4_1_b) ];
				$j = hexdec($phash[1]);	$r .= $aFillEnd[ $j % count($aFillEnd) ]; $r .= ' ';
			}
			$j = hexdec($phash[14]);	if( $j >= 4 ) {
				$j = hexdec($phash[14])+hexdec($phash[0]);	$r .= $a3_1[ ($j+7) % count($a3_1) ]; $r .= ' ';
				$j = hexdec($phash[13])+hexdec($phash[1]);	$r .= $a3_1_a[ ($j+3) % count($a3_1_a) ]; $r .= ' ';
				$j = hexdec($phash[12])+hexdec($phash[2]);	$r .= $a3_1_b[ ($j+1) % count($a3_1_b) ];
				$j = hexdec($phash[11])+hexdec($phash[3]);	$r .= $aFillEnd[ ($j+5) % count($aFillEnd) ]; $r .= ' ';
			}
			print $r."<br>";
		}
	}
?>

<form class="form" method="get">Kas galėtų būti...<br>
<input class="inform" type="text" name="v"> <input class="inform" type="submit" value="Parodyk!"></form>
</div>

<div class="footer">
2013, Aras Pranckevičius ir Marta Pranckevičiūtė<br>
<a href="../">[www]</a>
<a href="mailto:aras_at_nesnausk_dot_org">[email]</a>
<br>

</body>
</html>