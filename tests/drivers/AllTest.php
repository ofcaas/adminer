<?php

/**
 * Description of AllTest
 *
 * @author Jakub Cernohuby
 */
define("DRIVER_FOLDER", "../../adminer/drivers/");

require_once 'PHPUnit/Framework.php';
require_once 'driverTesting.php'; // Common driver test class.
require_once 'function.php'; // Functions required to testing.


$drivers_array = getContentOfFolder(DRIVER_FOLDER);

foreach($drivers_array as $d_file)
{
    $_GET['driver_test'] = $d_file;
    
    // Create a test suite that contains the tests
    // from the ArrayTest class.
    $suite = new PHPUnit_Framework_TestSuite('driverTesting');

    // Run the tests.
    $suite->run();

}

?>
