<!DOCTYPE HTML>
<html>
<head>
<title>解析爱奇艺视频地址</title>
<meta charset="UTF-8">
<meta name="keywords" content="爱奇艺,视频,解析,f4v,下载"/>
<meta name="description" content="用于解析爱奇艺视频的网页小工具, 项目地址: https://github.com/xyuanmu/parseiqiyi"/>
<link rel="shortcut icon" href="http://www.iqiyi.com/favicon.ico" type="image/x-icon"/>
<style>
body{color:#333;font-family:"segoe ui",Arial,sans-serif}#page{width:90%;margin:40px auto 0;padding:20px 0 80px}h1{text-align:center}form{text-align:center;margin:40px 0}.input{width:100%;height:48px;font-size:18px;color:#888;background:#f2f2f2;border:0;padding:0 10px;outline:0;text-align:center;box-sizing:border-box}.input,#submit{transition:color,background .24s ease-out}.input:focus{color:#000;background:#C7EAEA}#proxy{margin-top:18px}#submit{margin:0;padding:8px 18px;font-size:16px;cursor:pointer;color:#fff;background:#32B1BD;border:0;border-radius:3px}#submit:hover{background:#379AA3}#submit:active{background:#238993;padding:9px 18px 7px}.type{margin:15px;display:inline-block;vertical-align:middle;cursor:pointer}pre{font-family:monaco,consolas;font-size:13px;background:#f8f8f8;padding:10px;border:1px solid #e3e3e3;overflow-x:auto}span{color:#E47;font-weight:bold}footer{width:100%;font-size:13px;padding:15px 0;text-align:center;position:fixed;left:0;bottom:0;color:#ddd;background:#182B36}footer a{color:#ddd;text-decoration:none;transition:color .18s ease-out}footer a:hover{color:#fff}@media screen and (max-width:1080px){.input{font-size:16px}pre{font-size:12px}}
</style>
</head>

<body>
	<div id="page">
		<h1>解析爱奇艺视频地址</h1>
		<form action="index.php" method="get">
			<input id="url" class="input" name="url" type="text" placeholder="请在此输入视频地址"<?php echo isset($_GET['url']) && $_GET['url'] != '' ? ' value="' . $_GET['url'] . '"' : ''?>>
			<input id="proxy" class="input" name="proxy" type="text" placeholder="输入国内代理如: 36.37.36.38:80"<?php echo isset($_GET['proxy']) && $_GET['proxy'] != '' ? ' value="' . $_GET['proxy'] . '"' : ''?> style="display:<?php echo isset($_GET['proxy']) && $_GET['proxy'] != '' ? '' : 'none'?>">
			<label class="type"><input type="radio" value="fluent" name="type" <?php echo isset($_GET['type']) && $_GET['type'] == "fluent" ? ' checked="checked"' : ''?>/>极速</label>
			<label class="type"><input type="radio" value="normal" name="type" <?php echo isset($_GET['type']) && $_GET['type'] == 'normal' ? ' checked="checked"' : ''?>/>流畅</label>
			<label class="type"><input type="radio" value="high" name="type" <?php echo isset($_GET['type']) && $_GET['type'] == 'high' ? ' checked="checked"' : ''?>/>高清</label>
			<label class="type"><input type="radio" value="super" name="type" <?php echo isset($_GET['type']) && $_GET['type'] == 'super' ? ' checked="checked"' : ''?>/>720P</label>
			<label class="type"><input type="radio" value="hd" name="type" <?php echo isset($_GET['type']) && $_GET['type'] == 'hd' ? ' checked="checked"' : ''?>/>1080P</label>
			<label class="type"><input type="radio" value="all" name="type" <?php echo isset($_GET['type']) && $_GET['type'] == 'all' || !isset($_GET['type']) ? ' checked="checked"' : ''?>/>全部</label>
			<label class="type"><input type="submit" id="submit" value="解析"/></label>
		</form>
<?php
require "iqiyi.class.php";

function debug($url,$type,$proxy){
	$result = Iqiyi::parse($url,$type,$proxy);
	echo "<pre>\n<span>下载链接10分钟内有效，请尽快下载，若失效刷新本页面！</span> <br>";
	if ($result){
		if ($type == 'high')  $format = "高清";
		if ($type == 'super') $format = "720P";
		if ($type == 'hd')    $format = "1080P";
		if ($result == 404){
			echo "<span>不支持解析VIP视频!</span>";
		}
		else if ($type == 'all'){
			print_r($result);
		} else {
			echo "\n标题：" . $result['title'] . "\n";
			echo "时长：" . $result['seconds'] . "秒\n";
			$value = array_slice($result,2,1);
			if ($value){
				echo "<ol>";
				foreach ($value as $key => $vals){
					foreach ($vals as $val){
						echo "<li>" .$val. "</li>";
					}
				}
			} else {
				echo "<p><span>未解析到" .$format. "格式的视频！</span></p>";
			}
			echo "</ol>";
		}
	}
	else {
		echo "\n<span>获取失败！</span>";
	}
		echo "</pre>\n";
}

###### output video urls ######
$type = isset($_GET['type']) && $_GET['type'] != '' ? $_GET['type'] : 'all';
$proxy = isset($_GET['proxy']) && $_GET['proxy'] != '' ? $_GET['proxy'] : '';
if (isset($_GET['url'])){
	if ($_GET['url'] != ''){
		$url = $_GET['url'];
		debug($url,$type,$proxy);
	} else if ($_GET['url'] == ''){
		$url = 'http://www.iqiyi.com/v_19rroonq48.html';
		debug($url,$type,$proxy);
	}
}
?>
	</div>
	<footer><a href="http://yuanmu.mzzhost.com/">yuanmu.mzzhost.com</a> © All Rights Reserved. <a onclick="display('proxy')" href="javascript:void(0)">使用代理</a></footer>
	<script type="text/javascript">
		function display(id){
		var traget=document.getElementById(id);
			if(traget.style.display=="none"){
				traget.style.display="";
			} else {
				traget.style.display="none";
			}
		}
	</script>
</body>
</html>