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

/* To create dbs on test
*
* @param driverTesting object
* @param string "view" to create view, "trigger" to create trigger
*/
function create_db($t, $next = ""){
    global $connection;
    
    //$db = array($t->make_db_name);
    //drop_databases($db);

    create_database($t->make_db_name, "Czech_BIN");
    $connection->select_db($t->make_db_name);
    $connection->query($t->make_db);
    if($next == "view"){
        $connection->query($t->make_db_view);
    }
    if($next == "trigger"){
        $connection->query($t->make_db_trigger);
    }
}

/* To drop dbs on test
*
* @param driverTesting object
*/
function drop_db($t){
    $db = array($t->make_db_name);
    drop_databases($db);
}

function is_key_string($ar){

}

function is_key_value($ar){

}

?>
