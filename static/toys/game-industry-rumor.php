<html>
<head>
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
	<title>Game Industry Rumor Generator</title>
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
	border: 1px dashed #808080;
	margin-right: 40%;
	padding: 10px;
	font-weight: bold;
	font-size: x-large;
	background-color: #ffe8e8;
	position: relative;
	left: -50px;
	top: 10px;
	z-index: 1;
}
.ver {
	font-size: x-small;
	font-weight: normal;
	color: #808080;
}
.background {
	border: 1px dashed #808080;
	padding: 30px 5% 30px 5%;
	background-color: #f0f0f0;
}
.form {
	color: #004000;
	background-color: #e8ffe8;
	border: 1px solid;
	padding: 10px 20px 10px 20px;
	margin: 50px 1% 0px 1%;
}
div.toytitle {
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

<div class="title"><a href="game-industry-rumor.php">Game Industry Rumors!</a> <div class="ver">v0.1 (2015-10-20)</div></div>
<div class="background">
<?php

function GetStr($i,$a,$spc=1)
{
	$r = $a[ $i % count($a) ];
	if ($spc) $r .= ' ';
	return $r;
}

function GetDesc($p, $g)
{
	$aFill0 = array(
		'This morning', 'This afternoon', 'Today', '', '',
		);
	$aWho = array(
		'Valve', 'Epic', 'Unity', 'EA',
		'Blizzard', 'Ubisoft', 'Activision', 'Microsoft',
		'Sony', 'Nintendo', 'Apple', 'Google',
		'Facebook', 'Oculus', 'Intel', 'Supercell');
	$aVerb0 = array(
		'buys', 'acquires', 'merges with', 'splits off',
		'partners with', 'cancels deals with', 'makes friends with', 'collaborates with',
		'leaves'
		);

	$aFill1 = array(
		'The plan is to', 'This will allow to', 'Future direction is to', 'Representatives are looking forward to',
		);
	$aVerb1 = array(
		'strengthen', 'improve', 'increase', 'align',
		'synergize', 'employ', 'harness', 'fullfill',
		'monetize', 'advertize', 'enable', 'democratize'
		);
	$aNoun1 = array(
		'cloud', 'platform', 'games', 'technology',
		'vision', 'future', 'industry', 'players',
		'gameplay', 'IPO', 'hardware', 'ecosystem',
		'userbase'
		);

	$gotp = (strlen($p) > 0);
	$totalrand = 0;
	if (!$gotp)
	{
		$p = $g;
		if (strlen($g) <= 0) {
			$p = rand(10000,99999);
			$totalrand = 1;
		}
	}

	$p = ucfirst($p);
	$phash = md5($p);
	$p = htmlspecialchars($p);
	print "<div class='subjtitle'>";
	if ($totalrand)
		print "Here's a rumor:";
	else if (!$gotp)
		print "Rumor #<span class='subjtitle'>$p</span>:";
	else
		print "Rumor on <span class='subjtitle'>$p</span>:";
	print "</div>";

	$r = "<div class='toytitle'>";

	$pfirst = hexdec($phash[10]) < 8;

	# who did who
	$j = hexdec($phash[0]); $r .= GetStr($j, $aFill0);
	if ($gotp && $pfirst)
	{
		$r .= $p; $r .= ' ';
	}
	else
	{
		$j = hexdec($phash[1]); $r .= GetStr($j, $aWho);
	}
	$j = hexdec($phash[2]); $r .= GetStr($j, $aVerb0);
	if ($gotp && !$pfirst)
	{
		$r .= $p; $r .= ' ';
	}
	else
	{
		$j = hexdec($phash[3]); $r .= GetStr($j, $aWho);
	}
	$r = rtrim($r); $r .= '. ';

	# future plan
	$j = hexdec($phash[4]); $r .= GetStr($j, $aFill1);
	$j = hexdec($phash[5]); $r .= GetStr($j, $aVerb1); $r .= 'the ';
	$j = hexdec($phash[6]); $r .= GetStr($j, $aNoun1);
	$j = hexdec($phash[7]); if ($j < 8)
	{
		$r .= 'and to ';
		$j = hexdec($phash[8]); $r .= GetStr($j, $aVerb1); $r .= 'the ';
		$j = hexdec($phash[9]); $r .= GetStr($j, $aNoun1);
	}
	$r = rtrim($r); $r .= '. ';


	$r .= "</div>";
	print $r."<br>";

	print "<p>";
	print "Awesome, tweet this! <a href='http://twitter.com/share' class='twitter-share-button' data-text='Game industry rumor $p #GameIndustryRumors' data-count='none'>Tweet it!</a><script type='text/javascript' src='http://platform.twitter.com/widgets.js'></script>";
	print "</p>\n";
}

$p = '';
$g = '';
if( isset( $_GET["v"] ) ) {
	$p = trim( $_GET["v"] );
}
if( isset( $_GET["r"] ) ) {
	$g = trim( $_GET["r"] );
}
GetDesc($p, $g);
?>

<form class="form" method="get">
<input type="hidden" name="r" value="<?php echo rand(10000,99999);?>"> <input type="submit" value="Give me a random rumor">
or, I want a rumor on...
<input class="inform" type="text" name="v"> <input class="inform" type="submit" value="This thing"></form>
</div>

<div class="footer">
2015, Aras Pranckevičius<br>
<a href="../">[www]</a>
<a href="mailto:aras_at_nesnausk_dot_org">[email]</a>
<br>

</div>

</body>
</html>
