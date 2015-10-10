<?php
class Iqiyi {

	const USER_AGENT = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.69 Safari/537.36";
	const PROXY = "http://203.195.160.14:80"; //代理ip 端口
	//const PROXY = "";
	static private $deadpara = 832;
	static private $enc_key  = "97596c0abee04ab49ba25564161ad225";

	public static function parse($url,$type){
		$html = static::_cget($url);
		$data = $tvids = $vids = $urls_data = array();
		preg_match('#data-(player|drama)-tvid="([^"]+)"#iU',$html,$tvids);
		preg_match('#data-(player|drama)-videoid="([^"]+)"#iU',$html,$vids);
		$vid = $vids[2]?$vids[2]:'';
		$tvid = $tvids[2]?$tvids[2]:'';

		if(!empty($vid)&&!empty($tvid)){
			$data = self::parseFlv($tvid,$vid,$type);
			return $data;
		}

	}

	// 通过 curl 获取内容
	private static function _cget($url = ''){
		if (!$url) return;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_REFERER, "http://www.baidu.com");
		if (self::PROXY!='') curl_setopt($ch, CURLOPT_PROXY, self::PROXY);
		curl_setopt($ch, CURLOPT_USERAGENT, self::USER_AGENT);
		ob_start();
		curl_exec($ch);
		$html = ob_get_contents();
		ob_end_clean();
		if (curl_errno($ch)){
			curl_close($ch);
			return false;
		}
		curl_close($ch);
		if (!is_string($html) || !strlen($html))
			return false;
		return $html;
	}

	private static function calmd($t,$fileId){
		$local3 = ")(*&^flash@#$%a";
		$local4 = floor(($t / (600)));
		return md5(($local4.$local3) . $fileId);
	}

	private static function getVrsEncodeCode($_arg1){
		$_local6;
		$_local2 = "";
		$_local3 = explode("-",$_arg1);
		$_local4 = count($_local3);
		$_local5 = ($_local4 - 1);
		while ($_local5 >= 0){
			$_local6 = static::getVRSXORCode(intval($_local3[(($_local4 - $_local5) - 1)], 16), $_local5);
			$_local2 = (static::fromCharCode($_local6).$_local2);
			$_local5--;
		};
		return $_local2;
	}

	private static function getVRSXORCode($_arg1, $_arg2){
		$_local3 = ($_arg2 % 3);
		if ($_local3 == 1){
			return (($_arg1 ^ 121));
		};
		if ($_local3 == 2){
			return (($_arg1 ^ 72));
		};
		return (($_arg1 ^ 103));
	}

	private static function fromCharCode($codes){
		if (is_scalar($codes)){
			$codes = func_get_args();
		}
		$str = '';
		foreach ($codes as $code){
			$str .= chr($code);
		}
		return $str;
	}

	//parseFlv 解析网站f4v格式的视频
	private static function parseFlv($tvid,$vid,$type){

		$api_url = "http://cache.video.qiyi.com/vms?key=fvip&src=1702633101b340d8917a69cf8a4b8c7c";
		$api_url = $api_url."&tvId=".$tvid."&vid=".$vid."&vinfo=1&tm=".self::$deadpara."&enc=".md5(self::$enc_key.self::$deadpara.$tvid);

		$video_datas = json_decode(static::_cget($api_url),true);
		if($video_datas['code']=='A000001')
			return false;

		$vs = $video_datas['data']['vp']['tkl'][0]['vs'];    //.data.vp.tkl[0].vs

		$time_url = "http://data.video.qiyi.com/t";
		$time_datas = json_decode(static::_cget($time_url),true);
		$server_time = $time_datas['t'];

		//视频信息
		$data['title'] = $video_datas['data']['vi']['vn'];
		$data['seconds'] = $vs[0]['duration'];
		$vs[0]['type'] = $type;

		//划分视频尺寸 1080p 的视频地址暂时无法获得
		foreach($vs as $val){
			foreach ($val['fs'] as $v){

				$type = $vs[0]['type'];

				$this_link = $v['l'];
				if($val['bid'] ==  4 || $val['bid'] ==  5 || $val['bid'] ==  10){
					$this_link = static::getVrsEncodeCode($this_link);
				}

				$sp = explode('/',$this_link);
				$files = explode('.',$sp[count($sp)-1]);
				$fileId = $files[0];
				$this_key = static::calmd($server_time,$fileId);

				$final_url = "http://data.video.qiyi.com/".$this_key."/videos".$this_link;

				if($val['bid'] ==  96 && ($type=='all' || $type=='fluent')) $urls_data['fluent'][] = $final_url;
				if($val['bid'] ==  1 && ($type=='all' || $type=='normal')) $urls_data['normal'][] = $final_url;
				if($val['bid'] ==  2 && ($type=='all' || $type=='high')) $urls_data['high'][] = $final_url;
				if($val['bid'] ==  4 && ($type=='all' || $type=='super')) $urls_data['SUPER_HIGH'][] = $final_url;
				if($val['bid'] ==  5 && $type=='all') $urls_data['FULL_HD'][] = $final_url;
				if($val['bid'] ==  10 && $type=='all') $urls_data['FOUR_K'][] = $final_url;
			}
		}

		if(!empty($urls_data['fluent'])) $data['极速'] = self::getVideoUrl($urls_data['fluent']);
		if(!empty($urls_data['normal'])) $data['流畅'] = self::getVideoUrl($urls_data['normal']);
		if(!empty($urls_data['high'])) $data['高清'] = self::getVideoUrl($urls_data['high']);
		if(!empty($urls_data['SUPER_HIGH'])) $data['720P'] = self::getVideoUrl($urls_data['SUPER_HIGH']);
		if(!empty($urls_data['FULL_HD'])) $data['1080P'] = self::getVideoUrl($urls_data['FULL_HD']);
		if(!empty($urls_data['FOUR_K'])) $data['4K'] = self::getVideoUrl($urls_data['FOUR_K']);

		return $data;
	}

	//返回最终视频地址
	private static function getVideoUrl($url_data){
		$data = self::rolling_curl($url_data);
		$urls = array();
		foreach($url_data as $val){
			//按顺序排列视频 url
			$urls[] = $data[$val];
		}
		return $urls;
	}

	//rolling_curl curl并发
	private static function rolling_curl($urls){
		$queue = curl_multi_init();
		$map = $responses = array();
		foreach ($urls as $url){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			if (self::PROXY!='') curl_setopt($ch, CURLOPT_PROXY, self::PROXY);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_USERAGENT, self::USER_AGENT);
			curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
			curl_multi_add_handle($queue, $ch);
			$map[(string)$ch] = $url;
		}
		do {
			while (($code = curl_multi_exec($queue, $active)) == CURLM_CALL_MULTI_PERFORM);
			if ($code != CURLM_OK){
				break;
			}
			while ($done = curl_multi_info_read($queue)){
				$results = self::callback_match(curl_multi_getcontent($done['handle']));
				$responses[$map[(string)$done['handle']]] = $results;
				curl_multi_remove_handle($queue, $done['handle']);
				curl_close($done['handle']);
			}
			if ($active > 0){
				curl_multi_select($queue, 0.5);
			}
		} while ($active);
		curl_multi_close($queue);
		return $responses;
	}

	//callback_match 回调获取视频的地址
	private static function callback_match($data){
		preg_match('#"l":"([^"]+)&src=.*?"#i',$data,$matchs);
		if($matchs) $data = $matchs[1];
		return $data;
	}
}
