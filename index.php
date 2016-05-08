<?php
	header("Content-type:text/html;charset=utf-8");
	function toPinyin($hanzi,$encode,$dics){
	    $str='';
	    
		$len = mb_strlen($hanzi,$encode);
	    for($i=0;$i<$len;$i++){
	    	$danzi = mb_substr($hanzi,$i,1,$encode);
			$pos = mb_strpos($dics,$danzi,1,$encode);
	        if($pos&&ord($danzi)>200){    //判断字符的assic编码
				$s = 1;
				//echo mb_substr($dics,47,1,$encode);exit;
	            while(mb_substr($dics,$pos+$s,1,$encode)!=","){
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
	$cn = mysql_pconnect('localhost','root','root');
	if(!$cn){
		die(mysql_error());
	}
	mysql_select_db('reci',$cn) or die(mysql_error());
	
	$dics = file_get_contents('dics.dat');
	$d_encode = mb_detect_encoding($dics,array('GB2312','GBK','UTF-8','CP936'),true);
	if($d_encode != 'UTF-8'){
		$dics = iconv($d_encode,"UTF-8",$dics);
	}
	//wd:搜索关键词，pn：起始行数
	$baidu = file_get_contents("http://www.baidu.com/s?wd=a&pn=300&oq=a&tn=monline_3_dg&ie=utf-8&rsv_idx=1");
	preg_match_all('/<a[\s|\S]*href\s*=\s*"(http:\/\/www\.baidu\.com\/link[\s|\S]+)"/U',$baidu,$baidu_m);
	//var_dump($baidu_m[1]);exit();
	foreach($baidu_m[1] as $val){
		$stream = file_get_contents($val);
		if(!$stream){
			continue;
		}
		$encode = mb_detect_encoding($stream,array('GB2312','GBK','UTF-8'),true);
		//echo $encode;exit;
		if($encode=="GB2312" || $encode=='CP936')
		{
		    $stream = iconv("GBK","UTF-8",$stream);
		}
		else if($encode=="GBK")
		{
		    $stream = iconv("GBK","UTF-8",$stream);
		}
		//echo $stream;exit;
		preg_match_all('/<[a|span|li|strong|h1|h2].*>(.+)<\/[a|span|li|strong|h1|h2]>/U',$stream,$matchs);
		$domains = array();
		//var_dump($matchs);exit();
		foreach($matchs[1] as $val){
			if(strlen($val)<12){
				$val = strip_tags($val);
				if(empty($val)) continue;
				$pinyin = toPinyin($val, 'utf-8',$dics);
				if(preg_match("/[^a-z0-9-]/",$pinyin)){
					continue;
				}
				$suffix = array('.com','.cn','.net');
				if(strlen($pinyin)<10){
					foreach($suffix as $v){
						$url = 'http://checkdomain.xinnet.com/domainCheck?callbackparam=jQuery17203308473502664542_1458194172038&searchRandom=8&prefix='.$pinyin.'&suffix='.$v.'&_=1458194245054';
						//echo $url;
						$result = file_get_contents($url);
						preg_match('/jQuery.*\(\[(.+)\]\)/U',$result,$match);
						//var_dump(json_decode($match[1],true));exit;
						$info = json_decode($match[1],true);
						$comPrice = $cnPrice =$netPrice=0;
						if(count($info['result'][0]['yes'])>0){
							$price = $info['result'][0]['yes'][0]['price'];
							$name = $pinyin.$v;
							if($v=='.com'){
								$comPrice = $info['result'][0]['yes'][0]['price'];
							}else if($v=='.cn'){
								$cnPrice = $info['result'][0]['yes'][0]['price'];
							}else{
								$netPrice = $info['result'][0]['yes'][0]['price'];
							}
							$domain[] = array('com_price'=>$comPrice,'cn_price'=>$cnPrice,'net_price'=>$netPrice,'pinyin'=>$pinyin,'hanzi'=>$val,'update_time'=>time());
							$domains = array_merge($domain,$domains);
						}
					}
					
				}
			}
		}
	}
	//var_dump($domains);
	foreach($domains as $val){
		$result = mysql_query("SELECT * FROM dics WHERE pinyin='".$val['pinyin']."'");
		if(!mysql_num_rows($result)){
			$query = 'INSERT INTO dics (pinyin,com_price,cn_price,net_price,update_time) VALUES('."'".$val['pinyin']."'".','.$val['com_price'].','.$val['cn_price'].','.$val['net_price'].','.$val['update_time'].')';
			mysql_query($query) or die(mysql_error());
			$id = mysql_insert_id();
			
			$query = "INSERT INTO hanzi(hanzi,dics_id) VALUES('".$val['hanzi']."',$id)";
			mysql_query($query) or die(mysql_error());
		}else{
			$_result = mysql_query("SELECT * FROM hanzi WHERE hanzi='".$val['hanzi']."'");
			$row = mysql_fetch_row($result);
			if(!mysql_num_rows($_result)){
				$query = "INSERT INTO hanzi(hanzi,dics_id) VALUES('".$val['hanzi']."',".$row['id'].")";
				mysql_query($query) or die(mysql_error());
			}
		}
		
	}
	echo 'OK';
?>