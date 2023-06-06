<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title><?=Html::chars($title)?></title>
	<link href="/resources/css/style.css" rel="stylesheet" type="text/css" />
	<link href="/resources/css/autoform.css" rel="stylesheet" type="text/css" />
	<link href="/resources/css/admin.css" rel="stylesheet" type="text/css" />
	<link href="/resources/css/eva.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="/resources/js/jquery/jquery.js" ></script>
	<script type="text/javascript" src="/resources/js/jquery/jquery-ui.js" ></script>
	<script type="text/javascript" src="/resources/js/admin.js" ></script>
	<script type="text/javascript" src="/resources/js/eva.js" ></script>
	<style type="text/css">/*<![CDATA[*/
		body {
			background-color: white;
		}
		input[type="submit"] {
			padding: 0.25em 1em;
		}
		h1 {
			font-size: 1.4em;
		}
		#logo {
			margin: 0 auto; width: 160px;
			padding: 70px 0 90px;
		}
		#logo a, #logo img {
			border: none;
		}
		#content {
			margin: 0 auto auto; width: 300px;
		}
		.box-info {
			padding: 5px; margin: 20px 0 10px; background-color: #fff9d7; border: 1px solid #e2c822;
		}
		.box-error {
			padding: 5px; margin: 20px 0 10px; background-color: #ffebe8; border: 1px solid #dd3c10;
		}
		legend {
			color: #3d6090 !important;
			padding-left: 0 !important;
			font-weight: bold;
		}
		
		#signin {
			position: relative;
			text-align: left;
		}
		#signin label em {
			display: none;
		}
		#signin.autoform ul.errors {
			display: block;
			margin-top: 2px;
		}
		#signin.autoform td {
			border-top-style: none;
		}
		#signin-submit {
			position: absolute;
			bottom: 35px;
			right: 5px;
		}
	/*]]>*/
	</style>
</head>
<body>
	<div class="main-container">
		<div id="logo">
			<a href="https://www.ukietech.com/"><img src="/resources/images/dashboard/logo.gif" alt="Ukietech Corp." /></a>
		</div>
		<div id="content" >
			<?php
			if (isset($info)) {
				echo '<div class="box-info feedback-message">' . Html::chars($info) . '</div>';
			}
			if (isset($error)) {
				echo '<div class="box-error feedback-message">' . Html::chars($error) . '</div>';
			}
			?>
			<?if (isset($content)) echo $content;?>
		</div>
	</div>
</body>
</html>