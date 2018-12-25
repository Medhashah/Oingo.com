<?php
date_default_timezone_set('EST');
echo date("d");
echo date("l");
echo date("T");
echo date("N");
	
	include 'db_functions.php';
		$result = get_filter(2);

		if(count($result)==0)
    		$count = 0;
		else
    		$count = count($result['note_id']);
    	echo $count;
  
  echo $result['note_id'][0];
  echo $result['description'][0];
?>