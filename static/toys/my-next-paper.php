<HTML>
<HEAD>
	<META content="text/html; charset=UTF-8" http-equiv="Content-Type">
	<TITLE>Graphics Paper Idea Generator</TITLE>
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

<div class="title"><a href="my-next-paper.php">Graphics Paper Ideas!</a> <div class="ver">v0.1 (2013-06-25)</div></div>
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
	$aTitle0 = array(
		'Q-', 'Quasi-', 'Mega-', 'Open', 'Real', 'O-', '3D-', '*' );
	$aTitle1 = array(
		'Reality', 'Shapes', 'Fab', 'Brush', 'Texel', 'Shader' );

	$aPre0 = array(
		'Improving', 'A New Approach to', 'Reconsidering', 'Optimizing', 'On the' );
	$aAdj0 = array(
		'User-Assisted', 'Probabilistic', 'Online', 'Realtime', 'Video-based', 'Efficient',
		'Robust', 'Low-budget', 'High-Quality', 'Reconfigurable', 'Stereoscopic',
		'Adaptive', 'Dynamic', 'Image-based', 'Global', 'Monte-Carlo' );
	$aNoun0 = array(
		'Point Set', 'Mesh', 'Skeleton', 'Hair', 'Shape', 'Shadow', 'Geometry',
		'Image', 'BRDF', 'Light', 'Material', 'Shader', 'Luminance', 'Rainbow' );
	$aVerb0 = array(
		'Denoising', 'Compositing', 'Grading', 'Rendering', 'Compression',
		'Modeling', 'Animation', 'Simulation', 'Manipulation', 'Segmentation',
		'Merging', 'Capturing', 'Visualization', 'Fabrication', 'Blurring',
		'Preprocessing' );
	$aFill0 = array(
		'through', 'via', 'using', 'in', 'for', 'using', 'based on', 'in presence of');

	$aAdj1 = array(
		'Photographic', 'Realistic', 'On-the-fly', 'Motion', 'Generalized', 'Arbitrary',
		'Polygonal', 'Photonic', 'Overcomplete', 'Crowdsourced', 'Adaptive', 'Multi-Layered',
		'Inconsistent', 'Sparse', 'Translucent' );
	$aNoun1 = array(
		'Shadows', 'Databases', 'Distance Fields', 'Motion Sets', 'Light Cuts', 'Surfaces',
		'Height Fields', 'Point Clouds', 'Light Fields', 'Wavelets', 'Textures' );
	$aNoun2 = array(
		'Graphs', 'Terrain', 'Mixtures', 'Pipeline', 'Projection', 'Functions', 'Optimization',
		'Characters', 'Sampling', 'Illumination', 'Occlusion', 'Splines' );

	$aWhere0 = array(
		'on the GPU', 'in the Cloud', 'on mobile' );

	$gotp = (strlen($p) > 0);
	$totalrand = 0;
	if (!$gotp) {
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
		print "Here's an idea:";
	else if (!$gotp)
		print "Paper idea #<span class='subjtitle'>$p</span>:";
	else
		print "Paper on <span class='subjtitle'>$p</span>:";
	print "</div>";

	$r = "<div class='papertitle'>";

	$titlekind = hexdec($phash[9]) % 3;
	if ($gotp && $titlekind==0)
		$r .= "<span class='subj'>$p</span>: ";
	else {
		$j = hexdec($phash[12]); if ($j > 10) {
			$j = hexdec($phash[12]);	$r .= GetStr($j, $aTitle0, 0);
			$j = hexdec($phash[13]);	$r .= GetStr($j, $aTitle1, 0);
			$r .= ': ';
		}
	}
	$j = hexdec($phash[11]); if ($j > 8) $r .= GetStr($j+3, $aPre0);
	$j = hexdec($phash[10]); if ($j > 4) $r .= GetStr($j+3, $aAdj0);
	if ($gotp && $titlekind==1)
		$r .= "<span class='subj'>$p</span> ";
	else
		$j = hexdec($phash[1]);	$r .= GetStr($j, $aNoun0);
	$j = hexdec($phash[2]);	$r .= GetStr($j, $aVerb0);
	$j = hexdec($phash[3]);	$r .= GetStr($j, $aFill0);
	$j = hexdec($phash[4]);	$r .= GetStr($j, $aAdj1);
	if ($gotp && $titlekind==2)
		$r .= "<span class='subj'>$p</span> ";
	else
		$j = hexdec($phash[5]);	$r .= GetStr($j, $aNoun1);

	$j = hexdec($phash[8]); if ($j < 4) {
		$j = hexdec($phash[7]);	$r .= GetStr($j, $aFill0);
		$j = hexdec($phash[6]); if ($j < 4) $r .= GetStr($j+3, $aAdj1); if ($j > 12) $r .= GetStr($j+7, $aAdj0);
		$j = hexdec($phash[14]);	$r .= GetStr($j, $aNoun2);
	}

	$j = hexdec($phash[9]); if ($j > 12) {
		$r = rtrim($r);
		$r .= ', ';
		$j = hexdec($phash[8]); $r .= GetStr($j, $aWhere0);
	}

	$r .= "</div>";
	print $r."<br>";
	print "<p>";
	//print "Awesome, tweet this! <a href='http://twitter.com/share' class='twitter-share-button' data-text='Graphics paper $p #SiggraphPapers' data-count='none'>Tweet it!</a><script type='text/javascript' src='http://platform.twitter.com/widgets.js'></script>";
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
<input type="hidden" name="r" value="<?php echo rand(10000,99999);?>"> <input type="submit" value="Give me a random idea">
or, I want a paper on...
<input class="inform" type="text" name="v"> <input class="inform" type="submit" value="This thing"></form>
</div>

<div class="footer">
2013, Aras Pranckevičius<br>
<a href="../">[www]</a>
<a href="mailto:aras_at_nesnausk_dot_org">[email]</a>
<a href="http://www.ywing.net/graphicspaper.php">[inspiration]</a>
<br>

</div>

</body>
</html>
