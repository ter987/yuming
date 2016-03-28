<?php
	function toPinyin($hanzi){
		
	}
	$dics = file_get_contents('dics.dat');
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
	foreach($matchs[1] as $val){
		if(strlen($val)<12){
			
		}
	}
	var_dump($matchs);
	exit();
?>