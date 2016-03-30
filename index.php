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
	//$baidu = file_get_contents("http://www.baidu.com/s?ie=utf-8&newi=1&mod=1&isbd=1&isid=824c578d00025828&wd=a&rsv_spt=1&rsv_iqid=0xefe39d1900019f68&issp=1&f=3&rsv_bp=1&rsv_idx=2&ie=utf-8&tn=baiduhome_pg&rsv_enter=0&oq=Unable%20to%20find%20the%20wrapper%20%26quot%3Bhttps%26quot%3B%20-%20did%20you%20forget%20to%20enable%20it%20when%20you%20co&inputT=8450&rsv_t=8b53G0bAPxUcc93G6VALsFzFehUbZxbrPVD7NLrF2Hnx%2BEWeRPcQb4TJGqlCxcunC9u8&rsv_pq=824c578d00025828&rsv_sug3=11&rsv_sug1=7&rsv_sug7=100&prefixsug=a&rsp=3&rsv_sug4=9260&bs=Unable%20to%20find%20the%20wrapper%20%22https%22%20-%20did%20you%20forget%20to%20enable%20it%20when%20you%20co&rsv_sid=18881_1993_17747_1435_19036_18288_18205_19558_17001_15833_11896_18453&_ss=1&clist=6be600e6979fc1e7&hsug=&f4s=1&csor=1&_cr1=56372");
	//preg_match_all('/<a[\s|\S]*href\s*=\s*"([\s|\S]+)"/U',$baidu,$baidu_m);
	//var_dump($baidu_m);exit();
	$url = 'http://www.baidu.com/link?url=z9zPJRjYg6eTzvqgu71fJ9E0z57wM1VTzHZ4zXt63ywAo7QrjZP6cs5uYH3eQkv7bmOIkJzwD1PU_b60V4rNwFZT2G4bEf8tUP3n7Ed0iyS';
	$stream = file_get_contents($url);
	$encode = mb_detect_encoding($stream,array('GB2312','GBK','UTF-8'));
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
						$domain[] = array('com_price'=>$comPrice,'cn_price'=>$cnPrice,'net_price'=>$netPrice,'pinyin'=>$pinyin,'update_time'=>time());
						$domains = array_merge($domain,$domains);
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
		}
		
	}
	echo 'OK';
?>