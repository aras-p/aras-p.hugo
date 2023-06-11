<HTML>
<HEAD>
	<META content="text/html; charset=UTF-8" http-equiv="Content-Type">
	<TITLE>How to name your engine class?</TITLE>
	<LINK href="engineclasses.css" rel=stylesheet type="text/css">
</HEAD>
<body>

<div class="title">How to name your engine class? <div class="ver">v1.0.0 (2011 03 25)</div></div>
<div class="background">
<?php
	$haz = 0;
	if( isset( $_GET["v"] ) ) {
		$p = trim( $_GET["v"] );
		$a = '';
		if (isset($_GET["a"]))
			$a .= trim($_GET["a"]);
		if( strlen($p) > 0 )
		{
			$haz = 1;
			$aPrefix = array('I', 'T', 'C', '');
			$aKind = array('Dynamic', 'Delegating', 'Abstract', 'Static', 'Global', 'Realistic', 'NextGen', 'Threaded', 'PPU', 'SPU', 'GPU', 'Networked');
			$aWhat = array('Lit', 'Unlit', 'Shadowed', 'Serialized', 'Safe', 'Unsafe', 'Fast', 'Approx', 'Surface', 'Node');
			$aAfter = array('Drawing', 'Rendering', 'Simulation', 'Display', 'Interaction', 'Highscore', 'Analytics', 'Traversal', 'Sorting', '');
			$aAfter2 = array('Container', 'Array', 'List', 'Set', 'Map', '', '', '', '');
			$aPatt = array('Generator', 'Serializer', 'Factory', 'Prototype', 'Proxy', 'Strategy', 'Policy', 'Visitor', '', '', '', '');
			
			$p = ucwords($p);
			$p = str_replace (' ', '', $p);
			$phash = md5($p . $a);
			$p = htmlspecialchars($p);
			print "<p>A class for $p:</p>";
			
			$r = '';
			$j = hexdec($phash[0]); $r .= $aPrefix[$j % count($aPrefix)];
			$j = hexdec($phash[1]); $r .= $aKind[$j % count($aKind)];
			$j = hexdec($phash[2]); $r .= $aWhat[$j % count($aWhat)];
			$r .= $p;
			$j = hexdec($phash[3]); $r .= $aAfter[$j % count($aAfter)];
			$j = hexdec($phash[7]); $r .= $aAfter2[$j % count($aAfter2)];
			$j1 = hexdec($phash[4]) % count($aPatt); $r .= $aPatt[$j1];
			$j2 = hexdec($phash[5]) % count($aPatt); if ($j2!=$j1) $r .= $aPatt[$j2];
			$j3 = hexdec($phash[6]) % count($aPatt); if ($j3!=$j1 && $j3!=$j2) $r .= $aPatt[$j3];
			print "<p class='subjtitle'>$r</p>";

			print "<p>";
			print "Awesome, tweet this! <a href='http://twitter.com/share' class='twitter-share-button' data-text='A class name for $p #classnamesthatcantbereal' data-count='none'>Tweet it!</a><script type='text/javascript' src='http://platform.twitter.com/widgets.js'></script>";
			print "</p>\n";

			$rnd = rand();
			print "<a href='?v=$p&a=$rnd'>That is not good, give me a new one!</a>\n";
		}
	}	
?>

<form class="form" method="get">What are you working on?<br>
<input class="inform" type="text" name="v"> <input class="inform" type="submit" value="Gimmeh a class name!"></form>
</div>

<?
if (!$haz)
{
	print "<p>";
	print "<a href='http://twitter.com/share' class='twitter-share-button' data-text='How to name your engine class? #classnamesthatcantbereal' data-count='none'>Tweet it!</a><script type='text/javascript' src='http://platform.twitter.com/widgets.js'></script>";
	print "</p>\n";
}
?>

<div class="footer">
2003-2011, Aras Pranckevicius<br>
<a href="/">[www]</a> <a href="http://twitter.com/aras_p">[twitter]</a> <a href="mailto:nearaz_at_gmail_dot_com">[email]</a>
<br>

<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "UA-299873-1";
urchinTracker();
</script>

</div>

</body>
</html>