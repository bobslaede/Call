<?php
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors',true);

require 'Mobile_Detect.php';
require 'CallMe.php';

$options = array(
	'numbers' => array(
		'Denmark' => array(
			'cell' => '+4540574975'
		)
	),
	'skype' => 'jeppe.dyrby',
	'latitude' => '-658452172246743490',
);

$callme = new CallMe($options);
?><!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7 ]> <html class="no-js ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]>    <html class="no-js ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]>    <html class="no-js ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>Contact me</title>
  <meta name="description" content="">
  <meta name="author" content="">
	<meta name="HandheldFriendly" content="True">
	<meta name="MobileOptimized" content="320">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<meta http-equiv="cleartype" content="on">
  <link rel="stylesheet" href="style.css">
</head>
<body>

	<h1>bobslaede</h1>
	<h2>Jeppe Dyrby</h2>
	<div id="choices">
		<ul>
<?php
$buttons = $callme->getCallTypes();
$location = $callme->getLocation();
foreach ($buttons as $button) {
	$href = $button['href'];			
	$title = '';
	$sub = '';
	switch ($button['type']) {
		default:
			$title = 'Call';
			$sub = $location;
			break;
		case 'text':
			$title = 'SMS';
			$sub = $location;
			break;
		case 'skypephone':
			$title = 'Skype to Phone';
			$sub = $location;
			break;
		case 'skypecall':
			$title = 'Skype to Skype';
			break;
	}
?>
			<li>
				<a class="button" href="<?=$href?>"><?=$title?><span><?=$sub?></span></a>
			</li>
<?
}
?>
		</ul>
	</div>

</body>
</html>
