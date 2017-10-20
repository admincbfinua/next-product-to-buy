<?php
	if (isset($_GET['nptbimage']) && (int)$_GET['nptbimage']>0 && ((int)$_GET['lng']==1 || (int)$_GET['lng']==2))
	{
		$code=(int) $_GET['nptbimage'];
		$langPath =((int)$_GET['lng']==1)?'lngru':'lngua';
		$abspth="";
		$file=$abspth.".nptb/20171023/images/".$langPath."/$code.jpg";
		if(file_exists($file))
		{
			header("Content-type: image/jpeg");
			readfile("https://www.bookclub.ua/.nptb/20171023/images/".$langPath."/$code.jpg");
		}
		else
		{
			//header("Content-type: image/gif");
			//readfile("http://www.bookclub.ua/imgmail/1.gif");
			
			//или создаем файл
			$lng=(int)$_GET['lng'];
			include($abspth.'.nptb/20171023/create_ifnofiles.php');
			//окончание создания файла 
		}
	}
	else
	{
		header("Content-type: image/gif");
		readfile("https://www.bookclub.ua/imgmail/1.gif");
	}
	
	


	
?>	
	
	
