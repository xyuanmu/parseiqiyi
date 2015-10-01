<!DOCTYPE HTML>
<html>
<head>
<title>解析爱奇艺视频地址</title>
<meta charset="UTF-8">
<link rel="shortcut icon" href="http://www.iqiyi.com/favicon.ico" type="image/x-icon"/>
<style>
body{color:#333;font-family:"segoe ui",Arial,sans-serif}
#page{width:90%;margin:40px auto 0;padding:20px 0}
h1{text-align:center;}
form{text-align:center;margin:40px 0}
#url{width:100%;height:48px;font-size:18px;color:#888;background:#f2f2f2;border:0;padding:0 10px;outline:0;  text-align:center;transition:color,background .2s ease-out;box-sizing:border-box}
#url:focus{color:#000;background:#C7EAEA}
pre{font-family:monaco,consolas;font-size:13px;background:#f8f8f8;padding:10px;border:1px solid #e3e3e3;overflow-x:auto;}
@media screen and (max-width:1080px){
#url{font-size:16px}
pre{font-size:12px}
}
</style>
</head>

<body>
	<div id="page">
		<h1>解析爱奇艺视频地址</h1>
		<form action="index.php" method="get">
			<input id="url" name="url" type="text" placeholder="输入视频地址按下回车">
		</form>
<?php
require "iqiyi.class.php";

function debug($url){
	$result = Iqiyi::parse($url);
	echo "<pre>\n<span style=color:#E47;font-weight:bold>因为地址有时间限制，请尽快下载，若失效刷新本页面！</span> <br>";
	print_r($result);
	echo '</pre>';
}

###### output video urls ######
$url = isset($_GET['url']) && $_GET['url'] != '' ? $_GET['url'] : 'http://www.iqiyi.com/v_19rroonq48.html';
debug($url);
?>
	</div>
</body>
</html>
