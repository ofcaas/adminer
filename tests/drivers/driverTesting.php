<?php
/*
* Testing PHP drivers
*
* @author Jakub Cernohuby
*/
require_once 'SetTesting.php'; //setting of test
require_once 'function.php'; //functions required to testing
require_once '../../adminer/include/functions.inc.php';


/*
* Main test class
*/ 
class driverTesting extends PHPUnit_Framework_TestCase
{
    protected $_c = null;
    protected $array_minDb = array();
    protected $driver = "";
    protected $d_name = "";
    public $connection = "";
    /*
    * Function before start test
    */    
    public function setUp() 
    {
        global $driver, $adminer, $connection;
        global $server, $user, $pass, $make_db, $d_name;

        $driver = $_GET['driver_test'];
        
        $d_name = getNameOfDriver($driver); //to get name of driver
        
        $_GET[$d_name] = "driver"; //@todo what is setting to $_GET
        require_once DRIVER_FOLDER . $driver; //to add testing file
        
        $connection = new Min_DB;
        $connection->connect($server[$d_name], $user[$d_name], $pass[$d_name]);
        $connection->query($make_db[$d_name]);
    }

    /*
    * Function after finished test
    */    
    public function tearDown()
    {
        global $driver, $connection, $make_db_name, $d_name;
        
        $db[] = $make_db_name[$d_name];
        drop_databases($db);
        unset($this->_c);
    }
    
    /*
    * Tests of Min_DB
    */    
    
    // connect to server
		public function test_connect()
    {
        global $driver, $connection;
        global $server, $user, $pass, $make_db, $d_name;
        
        switch ($d_name) {
            case "mssql":
                $this->assertFalse($connection->connect("", "", "")); //test of connect with false
                $connection->connect($server[$d_name], $user[$d_name], $pass[$d_name]); //return to normal connect
                break;
            default:
                break;
        }

    }
    
    
    // quote string
    public function test_quote()
    {
        global $driver, $connection, $d_name;
        
        switch ($d_name) {
            case "mssql":
                $this->assertEquals($connection->quote("test'test"), "'test''test'");
                break;
            default:
                break;
        }

    }
    
    
    
    // send query to server
    public function test_query()
    {
        global $driver, $connection, $d_name;
        
        switch ($d_name) {
            case "mssql":
                //$this->assertFalse($connection->query("randomtext"));
                break;
            default:
                break;
        }

    }
    
    /*
    * Tests of another functions
    */ 
    
    // escape string 
    public function test_idf_escape()
    {
        global $driver, $d_name;
        
        switch ($d_name) {
            case "mssql":
                $this->assertEquals(idf_escape("test]test"), "[test]]test]");
                break;
            case "mysql":
                $this->assertEquals(idf_escape("test`test"), "`test``test`");
                break;
            default:
                $this->assertEquals(idf_escape("test\"test"), "\"test\"\"test\"");
                break;
        }

    }
    
    // escape string and add scheme
    public function test_table()
    {
        global $driver, $d_name;

        switch ($d_name) {
            case "mssql":
                $_GET["ns"] = "owner";
                $this->assertEquals(table("test]test"), "[owner].[test]]test]");
                break;
            case "mysql":
                $this->assertEquals(table("test`test"), "`test``test`");
                break;
            default:
               
                break;
        }
        
    }

  

    //@todo problem with query function
    public function test_get_databases()
    {
        global $driver, $connection, $d_name;

        $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, get_databases());
    }

    // show how formulate SQL with limit
    public function test_limit()
    {
        global $driver, $d_name, $d_name;
                                                 
        switch ($d_name) {
            case "mssql":
                $tmp = " TOP (5) querywhere";
                $this->assertEquals(limit("query", "where", "5"), $tmp);
                break;
            case "mysql":
                $tmp = "querywhere LIMIT 5 OFFSET 1";
                $this->assertEquals(limit("query", "where", "5", "1"), $tmp);
                break;
            default:
               
                break;
        }
    }

    // show how formulate SQL with limit with 1
    public function test_limit1()
    {
        global $driver, $d_name;

       switch ($d_name) {
            case "mssql":
                $tmp = " TOP (1) querywhere";
                $this->assertEquals(limit1("query", "where"), $tmp);
                break;
            case "mysql":
                $tmp = "querywhere LIMIT 1";
                $this->assertEquals(limit1("query", "where"), $tmp);
                break;
            default:
               
                break;
        } 
    }
    
        
    
    public function test_engines()
    {
        global $driver, $d_name;

       switch ($d_name) {
            case "mssql":
                $tmp = engines();
                $this->assertTrue(empty($tmp));
                break;
            case "mysql":
                $tmp = engines();
                $this->assertTrue(!empty($tmp));
                break;
            default:
               
                break;
        } 
    }
    
    public function test_is_view()
    {
        global $driver, $d_name;

       switch ($d_name) {
            case "mssql":
                $tmp["Engine"] = "VIEW";
                $this->assertTrue(is_view($tmp));
                break;
            case "mysql":
                $tmp["Rows"] = " ";
                $this->assertTrue(is_view($tmp));
                break;
            default:
               
                break;
        } 
    }

    function test_fk_support()
    {
        global $driver, $d_name;

        switch ($d_name) {
            case "mssql":
                $this->assertEquals(fk_support("status"), true);
                break;
            case "mysql":
                //@todo
                break;
            default:
                break;
        } 
    }
    
    public function test_information_schema()
    {
        global $driver, $d_name;

       switch ($d_name) {
            case "mssql":
                $this->assertFalse(information_schema(""));
                break;
            case "mysql":
                //@todo
                break;
            default:
               
                break;
        } 
    }
    
    public function test_exact_value()
    {
        global $driver, $d_name;

        switch ($d_name) {
            case "mssql":
                $this->assertEquals(exact_value("test'test"), "'test''test'");
                break;
            default:
               
                break;
        }
        
    }
    
    
    public function test_create_database()
    {
        global $driver, $connection, $d_name;

        switch ($d_name) {
            case "mssql":
                $connection->query("CREATE DATABASE testTest2");
                $tmp = create_database("testTest", "Czech_BIN");
                
                $this->assertTrue($tmp);
                break;
            default:
               
                break;
        }
        
    }
    
    public function test_rename_database()
    {
        global $driver, $connection, $d_name;

        switch ($d_name) {
            case "mssql":
                define("DB", "testTest");
                $tmp = rename_database("testTest2", "Czech_CI_AS");
                $this->assertTrue($tmp);
                break;
            default:
                break;
        } 
    }
    
    public function test_drop_databases()
    {
        global $driver, $connection, $d_name;

        switch ($d_name) {
            case "mssql":
                $db[] = "testTest";
                $db[] = "testTest2";
                $tmp = drop_databases($db);
                $this->assertTrue($tmp);
                break;
            default:
               
                break;
        }
        
    }
    
    
    public function test_auto_increment()
    {
        global $driver, $d_name;

       switch ($d_name) {
            case "mssql":
                $tmp = " IDENTITY(5,1) PRIMARY KEY";
                $_POST["Auto_increment"] = "5"; 
                $this->assertEquals($tmp, auto_increment());
                break;
            case "mysql":
                //@todo
                break;
            default:
               
                break;
        } 
    }
    
    
    public function test_trigger_options()
    {
        global $driver, $d_name;

       switch ($d_name) {
            case "mssql":
                $tmp = array(
			                   "Timing" => array("AFTER", "INSTEAD OF"),
			                   "Type" => array("AS"),
		                   );
                $this->assertEquals($tmp, trigger_options());
                break;
            case "mysql":
                //@todo
                break;
            default:
               
                break;
        } 
    }

    
     public function test_get_schema()
    {
        global $driver, $d_name;

       switch ($d_name) {
            case "mssql":
                $this->assertEquals(get_schema(), "dbo");
                break;
            case "mysql":
                //@todo
                break;
            default:
               
                break;
        } 
    }
    
    
    public function test_set_schema()
    {
        global $driver, $d_name;

       switch ($d_name) {
            case "mssql":
                $this->assertTrue(set_schema(""));
                break;
            case "mysql":
                //@todo
                break;
            default:
               
                break;
        } 
    }
    
    public function test_show_variables()
    {
        global $driver, $d_name;

       switch ($d_name) {
            case "mssql":
                $tmp = show_variables();
                $this->assertTrue(empty($tmp));
                break;
            case "mysql":
                //@todo
                break;
            default:
               
                break;
        } 
    }

    public function test_show_status()
    {
        global $driver, $d_name;

       switch ($d_name) {
            case "mssql":
                $tmp = show_status();
                $this->assertTrue(empty($tmp));
                break;
            case "mysql":
                //@todo
                break;
            default:
               
                break;
        } 
    }

    
    public function test_support()
    {
        global $driver, $d_name;

       switch ($d_name) {
            case "mssql":
                $this->assertEquals(support("trigger"), 1);
                $this->assertEquals(support("view"), 1);
                $this->assertEquals(support("drop_col"), 1);
                $this->assertEquals(support("scheme"), 1);
                break;
            case "mysql":
                //@todo
                break;
            default:
               
                break;
        } 
    }

    
} //end of class MssqlTest     
    
?>
