<?php
/*
ini_set('display_errors',1);
error_reporting(E_ALL); 
*/

//выставить язык
if(isset($_GET['lng']) && $_GET['lng']!='' && $_GET['lng']>0)
{
		$lng= (int) $_GET['lng'];
}
else
{
	$lng=1;
}
$abspth="";
//connect to db and const
require_once $abspth .'';


$pth='https://www.bookclub.ua/';
$orig_directory = $httpimgpth ."goods/k/";
$langPath =($lng==1)?'lngru':'lngua';
$thumbs_directory = $abspth .'.nptb/20171023/images/'.$langPath.'/';
$button_img = $abspth .'.nptb/20171023/buttons/button_'.$langPath.'.jpg';
$background_img = $abspth .'.nptb/20171023/bg/img_blue.jpg';
$diagramWidth=600;
$diagramHeight=440;		
$left_marg=312;
$top_pad = 80;
$lineH=20;//высота отсута между тексовыми блоками

$authorSizeFont=13;
$booksSizeFont=20;
$descriptionSizeFont=11;
$strL=32;
$strBook=17;

$q="SELECT OOPAGES.ID, OOPAGES.CODE, OOPIMAGES.ID, OOPIMAGES.PIC
					FROM OOPAGES
						INNER JOIN OOPIMAGES ON OOPIMAGES.OOPID = OOPAGES.ID AND OOPIMAGES.TYPE=1
					WHERE OOPAGES.SI =1 AND OOPAGES.CODE REGEXP '^5.{6}$' ";
				
$res=q_e($q);
 
$qty=q_nr($res);

if($qty>0)
{
	for($i=0;$i<$qty;$i++)
	{
		$_tempLines=4;//кол-во формируемых строк
		$k_image=q_r($res, $i, 'OOPIMAGES.ID');
		$ext_image=q_r($res, $i, 'OOPIMAGES.PIC');
		$id_goods=q_r($res, $i, 'OOPAGES.ID');
		$price_rozn=q_r($res, $i, 'PRICEVALUES.NUMVAL');
		$code=q_r($res, $i, 'OOPAGES.CODE');
		$image_name=$id_goods."_".$k_image."_k".$ext_image;
		$image_name_code=$code.$ext_image;
		$filename = $orig_directory.$image_name;
		$productObj = new product($id_goods);
		$name=$productObj->getName();
		$description=$productObj->getBrief();
		$name=preg_replace("/-/"," ",$name); 
		$author=$productObj->getAuthor(FALSE, FALSE, $lng);
		$filetiwrite = $thumbs_directory.$image_name_code; 
		$size_img = getimagesize($filename);
		$widthofgetimage=$size_img[0];	
		$heightofgetimage=$size_img[1];
		$image=imagecreatetruecolor($diagramWidth,$diagramHeight);
		$colorBackgr = imageColorAllocate($image, 255, 255, 255);//white
		//$colorBackgr = imagecolorallocatealpha($image, 255, 255, 255, 127);//white
		$colorblack = imageColorAllocate($image, 0, 0, 0);
		$colorblue = imageColorAllocate($image, 0, 133, 176);
		$colorgrey = imageColorAllocate($image, 60, 60, 60);
		imageFilledRectangle($image, 0, 0, $diagramWidth , $diagramHeight , $colorBackgr); 
		
		//draw background
		$src_image_background = imagecreatefromjpeg($background_img);
		imagecopyresampled($image, $src_image_background, 0, 0, 0, 0, 312, 440, 312, 440); 
		
		//draw book
		$src_image = imagecreatefromjpeg($filename);
		imagecopyresampled($image, $src_image, 60, 60, 0, 0, $widthofgetimage, $heightofgetimage, $widthofgetimage, $heightofgetimage);  
		
		
		//draw author
		if($author!=null)
		{	
			$author = iconv('windows-1251', 'utf-8', $author);
			imagettftext($image, $authorSizeFont, 0, $left_marg, $top_pad, $colorblack, "./fonts/tahoma.ttf",$author);
			
		}
		
		//draw book name
		if($name!=null)
		{
			if(strlen($name) > $strBook)
			{
				$_fspace=0;
				$_tempLines =6;
				for ($j=0; $j<2; $j++)
				{
					$_space=strrpos(substr($name, $_fspace, $strBook), ' ');//result digit or false
					$_out = ($_space)? substr($name, $_fspace, $_space):substr($name, $_fspace, $strBook);
					$_fspace =$_space+1; 
					$_out = iconv('windows-1251', 'utf-8', $_out);
					imagettftext($image, $booksSizeFont, 0, $left_marg, ($top_pad + ((2+$j)*($lineH+7))), $colorblue, "./fonts/tahoma.ttf",$_out);
				}
				
			}
			else
			{
				$name = iconv('windows-1251', 'utf-8', $name);
				imagettftext($image, $booksSizeFont, 0, $left_marg, $top_pad + 2*$lineH, $colorblue, "./fonts/tahoma.ttf",$name);
			}
		}
		
		//draw description
		if($description!=null)
		{
			$row_Leght = strlen($description);
			if($row_Leght > $strL)
			{
				$coun = round($row_Leght / $strL);
				$_fspace=0;
				for ($j=0; $j<(int)$coun && $j<5; $j++)
				{
						$_space=strrpos(substr($description, $_fspace, $strL), ' ');
						$_out = ($_space)?substr($description, $_fspace, $_space):substr($description, $_fspace, $strL);
						if($j==4){$_out = $_out.'...';}
						$_fspace +=$_space+1; 
						$_out = iconv('windows-1251', 'utf-8', $_out);
						imagettftext($image, $descriptionSizeFont, 0, $left_marg, ($top_pad + (($_tempLines+$j)*$lineH)), $colorgrey, "./fonts/tahoma.ttf",$_out);
				}
				
			}
			else
			{
				$description = iconv('windows-1251', 'utf-8', $description);
				imagettftext($image, $descriptionSizeFont, 0, $left_marg, $top_pad + $_tempLines*$lineH, $colorgrey, "./fonts/tahoma.ttf",$description);	
			}
			
		}
		
		//draw button
		$src_image_button = imagecreatefromjpeg($button_img);
		imagecopyresampled($image, $src_image_button, $left_marg, $top_pad + 11*$lineH, 0, 0, 138, 45, 138, 45);
		imagejpeg($image, $filetiwrite,100);
		header("Content-type: image/jpeg");
		
		imageDestroy($image);
		imageDestroy($src_image);
		imageDestroy($src_image_background);
		imageDestroy($src_image_button);
		//die;
		
	}//end for
}//end if qty

	
			 
			
			
			








	
	
	
	


	
?>	
	
	
