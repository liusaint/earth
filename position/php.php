<?php

/*
* author:ls
* email:liusaint@gmail.com
* date:2015年6月
*/


$point = $_GET['points'];

if(empty($point)){
	//测试数据
	$point[0]['L'] = 110.83717794;
	$point[0]['B'] = 13;
	$point[0]['Name'] = 'pt1';
	$point[1]['L'] = 113;
	$point[1]['B'] = 24;
	$point[1]['Name'] = 'pt2';
	$point[2]['L'] = 105.794566000;
	$point[2]['B'] = 20;
	$point[2]['Name'] = 'pt3';
	$point[3]['L'] = 120;
	$point[3]['B'] = 17;
	$point[3]['Name'] = 'pt4';
}


function draw($point)
{
	//1.计算四个范围。

	$maxL = $point[0]['L'];
	$maxB=$point[0]['B'];
	$minL = $point[0]['L'];
	$minB=$point[0]['B'];

	foreach ($point as $key => $value) {
		$maxL = $maxL <$value['L']?$value['L'] :$maxL;
		$maxB = $maxB <$value['B']?$value['B'] :$maxB;
		$minL = $minL  > $value['L']?$value['L'] :$minL;
		$minB = $minB >$value['B']?$value['B'] :$minB;
	}

	$MAXSIZE = 300; //坐标区域长或宽，最大为300px。判断标准：坐标经度差与纬度差，大的那个为300px。再根据经度差与纬度差的比例计算出短的一边有多少px。

	$diffL = $maxL - $minL;//经度差
	$diffB = $maxB - $minB;//纬度差

	//计算坐标区域$height $width;
	if($diffL == 0){
		$width =$MAXSIZE;
		$height = $MAXSIZE;
		$Rate = $MAXSIZE/(float)$diffB;
	}
	elseif ($diffB == 0) {
		$width =$MAXSIZE;
		$height = $MAXSIZE;
		$Rate = $MAXSIZE/(float)$diffL;
	}else if($diffL >= $diffB){
		$diff = $diffL;
		$width = $MAXSIZE;
		$Rate = $MAXSIZE/(float)$diffL;//单位坐标的有多少个px值。
		$height = $diffB/$diffL*$MAXSIZE;
	}else{
		$diff = $diffB;
		$height = $MAXSIZE;
		$Rate = $MAXSIZE/(float)$diffB;//单位坐标的有多少个px值。
		$width = $diffL/$diffB*$MAXSIZE;
	}

	$img_width = $width + 50;//多出来的50是用来防止基站名字，以及点上的圆点显示不下
	$img_height = $height + 30;//多出来的30是用来防止基站名字，以及点上的圆点显示不下
	$image = imagecreatetruecolor($img_width,$img_height);//生成一个黑色背景的图片。
	$back = imagecolorallocate($image, 255, 255, 255);//背景颜色,白色
	imagefilledrectangle ($image,0,0,$img_width ,$img_height ,$back);//设置背景，其实是用的白色填充矩形。
	$linecolor=  imagecolorallocate($image,0,0,0);//连线的颜色
	$pointcolor = imagecolorallocate($image,29,143,254);//点的颜色
	$textcolor = imagecolorallocate($image,0,0,0);//字体颜色，黑色

	// 根据B,L计算像素位置。计算应该有px。多出来的6，和10，表示所有坐标都向右移动6px,向下移动10px,也是避免基站名和圆点显示不下。
	foreach ($point as $key => $value) {
		if($diffL == 0){
			$point[$key]['Lpx']  =$MAXSIZE/2;
			$point[$key]['Bpx']  = (int)($height - ($value['B'] - $minB)*$Rate)+10;
		}
		elseif ($diffB == 0) {
			$point[$key]['Lpx'] =(int)(($value['L'] - $minL)* $Rate) + 6;
			$point[$key]['Bpx'] = $MAXSIZE/2;
		}else{
			$point[$key]['Lpx'] =  (int)(($value['L'] - $minL)* $Rate) + 6;
			$point[$key]['Bpx'] = (int)($height - ($value['B'] - $minB)*$Rate)+10;
		}
	}


	$pointNum = count($point);

	foreach ($point as $key => $value) {
		$i = $key +1;
		while ($i<$pointNum) {
			imageline($image , $value['Lpx'] , $value['Bpx'], $point[$i]['Lpx'],  $point[$i]['Bpx'] ,  $linecolor );//点与点之间连线
			$i++;
		}
		imagestring( $image ,10 , $value['Lpx'], $value['Bpx'], $value['Name'] ,$linecolor);  //每个点的位置写下点名。10是大小。
		imagefilledellipse($image , $value['Lpx'] , $value['Bpx'], 10 , 10, $pointcolor); //每个点的位置，用一个填充的圆点表示。
	}


	// 输出图像
	header("Content-type: image/jpeg");
	// imagejpeg($image,'d:\1.jpeg');//如果没有路径就输出图片，有路径就保存图片。
	imagejpeg($image);//如果没有路径就输出图片，有路径就保存图片。
}


draw($point);

?>