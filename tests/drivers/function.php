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

/* When keys of array are string, return true
*
* @param array assoc
* @return boolean
*/
function is_array_key_string($ar){
    $is_string = true;
    
    foreach($ar as $key => $val){
        if(!is_string($key)){
            $is_string = false;
        }
    }
    return $is_string;
}

/* When vals of array are string, return true
*
* @param array assoc
* @return boolean
*/
function is_array_val_string($ar){
    $is_string = true;

    foreach($ar as $key => $val){
        if(!is_string($val)){
            $is_string = false;
        }
    }
    return $is_string;
}

/* When member of array are string, return true
*
* @param array assoc
* @return boolean
*/
function is_array_string($ar){
    $is_string = true;

    foreach($ar as $key){
        if(!is_string($key)){
            $is_string = false;
        }
    }
    return $is_string;
}

/* When vals of array are arrays, return true
*
* @param array assoc
* @return boolean
*/
function is_array_val_array($ar){
    $is_array = true;

    foreach($ar as $key => $val){
        if(!is_array($val)){
            $is_array = false;
        }
    }
    return $is_array;
}

/* When vals of array are number, return true
*
* @param array assoc
* @return boolean
*/
function is_array_val_numeric($ar){
    $is_numeric = true;

    foreach($ar as $key => $val){
        if(!is_numeric($val)){
            $is_numeric = false;
        }
    }
    return $is_numeric;
}

?>
