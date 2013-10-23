<?php
	date_default_timezone_set("PRC");


	function Escape($str){
		$s = str_replace('\\', '\\\\', $str);
		$s = str_replace("'","\'", $s);
		// You ONLY have to bother about the # and % characters if you enter the data manually into the text file. 
		// CREATE TABLE and all other SQL Statements will handle this automatically for you. 
		// $s = str_replace('%', '%p', $s);
		// $s = str_replace('#','%h', $s);
		return $s;
		// return str_replace(
		// 	'#','%h',str_replace(
		// 		'%', '%p', str_replace(
		// 				'"', '\"', str_replace(
		// 					"'","\'",str_replace(
		// 						'\\', '\\\\', $str)))));
	}

	function saveFile($fileName, $text) {
        if (!$fileName || !$text)
            return false;
        if ($fp = fopen($fileName, "w")) {
            if (@fwrite($fp, $text)) {
                fclose($fp);
                return true;
            } else {
                fclose($fp);
                return false;
            }
        }
        return false;
    }

    function readFileText($fileName){
    	if (!$fileName)
            return false;
        if (is_file($fileName)){
        	
        	return file_get_contents($fileName);
        }
        return '';
    }

    function filePathCombine($path1, $path2)
	{
	  $completedPath = '';
	 
	  if(substr($path1, strlen($path1) - 2, strlen($path1) - 1) !== DIRECTORY_SEPARATOR)
	  {
	    $completedPath = $path1 . DIRECTORY_SEPARATOR;
	  }
	  else
	  {
	    $completedPath = $path1;
	  }
	 
	  if(substr($path2, 0, 1) !== DIRECTORY_SEPARATOR)
	  {
	    $completedPath .= $path2;
	  }
	  else
	  {
	    $completedPath .= substr($path2, 1, strlen($path2) - 1);
	  }
	 
	  return $completedPath;
	}
?>