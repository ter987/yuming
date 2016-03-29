<?php
	header("Content-type:text/html;charset=utf-8");
	function toPinyin($hanzi,$encode,$dics){
	    $str='';
	    $s = 1;
		$len = mb_strlen($hanzi,$encode);
	    for($i=0;$i<$len;$i++){
	    	$danzi = mb_substr($hanzi,$i,1,$encode);
			$pos = mb_strpos($dics,$danzi,1,$encode);
	        if($pos&&ord($danzi)>200){    //判断字符的assic编码
	        	//exit($danzi);
	        	echo $pos.'#';
	            while(mb_substr($dics,$pos+$s,1,$encode)!=","){
	            	echo mb_substr($dics,$pos+$s,1,$encode).'-';
	                $str .= mb_substr($dics,$pos+$s,1,$encode);
	                $s++;
	            }
	        }
	        else{
	            $str .= $danzi;
	        }
	    }
	    return strtolower($str);
	}
	$str = '测试是水电费水电费阿萨德阿萨德发斯蒂芬阿萨德撒的发到发';
	$dics = file_get_contents('dics.dat');
	$d_encode = mb_detect_encoding($dics,array('GB2312','GBK','UTF-8','CP936'),true);
	if($d_encode != 'UTF-8'){
		$dics = iconv($d_encode,"UTF-8",$dics);
	}
	$url = 'http://blog.itpub.net/29625597/viewspace-1148243/';
	$stream = file_get_contents($url);
	$encode = mb_detect_encoding($stream,array('GB2312','GBK','UTF-8'));

	if($encode=="GB2312")
	{
	    $stream = iconv("GBK","UTF-8",$stream);
	}
	else if($encode=="GBK")
	{
	    $stream = iconv("GBK","UTF-8",$stream);
	}
	preg_match_all('/<a.*>(.+)<\/a>/U',$stream,$matchs);
	$domains = array();
	$matchs[1] = array('哎胞');
	foreach($matchs[1] as $val){
		if(strlen($val)<12){
			$pinyin = toPinyin($val, 'utf-8',$dics);
			//echo $pinyin;
			$suffix = array('.com','.cn','.net');
			if(strlen($pinyin)<10){
				foreach($suffix as $v){
					$url = 'http://checkdomain.xinnet.com/domainCheck?callbackparam=jQuery17203308473502664542_1458194172038&searchRandom=8&prefix='.$pinyin.'&suffix='.$v.'&_=1458194245054';
					//echo $url;
					$result = file_get_contents($url);
					preg_match('/jQuery.*\(\[(.+)\]\)/U',$result,$match);
					//var_dump(json_decode($match[1],true));exit;
					$info = json_decode($match[1],true);
					if(count($info['result'][0]['yes'])>0){
						$price = $info['result'][0]['yes'][0]['price'];
						$name = $pinyin.$v;
						$domain[] = array('price'=>$price,'name'=>$name);
						$domains = array_merge($domain,$domains);
					}
				}
				
			}
		}
	}
	//var_dump($domains);
	exit();
?>