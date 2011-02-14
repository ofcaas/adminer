<?php
/*
* Function for testing PHP drivers
*
* @author Jakub Cernohuby
* 
*/



/*
* Return string without translate with original lang() function
*
* @param string $str
* @return string
*/
function lang($str)
{
    return $str;
}

/*
* To get content of folder, to get all names of files with drivers
*
* @param string $folder
* @return array
*/

function getContentOfFolder($folder)
{
    $content = scandir($folder);
    foreach($content as $file)
    {
        if($file != ".")
        {
            if($file != "..")
            {
                $names[] = $file;
            }
        }
    }
    return $names;
}

/* To get name of driver from file name
*
* @param string $file
* @return string
*/
function getNameOfDriver($file)
{
    $file_array = explode(".", $file);
    return $file_array[0];
}

/** Connection parameters
* @return array ($server, $username, $password)
*
class Adminer {

	function credentials() {
                global $server;
                global $user;
                global $pass;
                global $driver;
                
		return array($server[$driver], $user[$driver], $pass[$driver]);
	}
}

$adminer = new Adminer();*/
?>
