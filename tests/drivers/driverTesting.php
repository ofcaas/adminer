<?php
/*
* Testing PHP drivers
*
* @author Jakub Cernohuby
*/
require_once 'SetTesting.php'; //setting of test
require_once 'function.php'; //functions required to testing
require_once '../../adminer/include/adminer.inc.php';
require_once '../../adminer/include/functions.inc.php';


/*
* Main test class
*/ 
class driverTesting extends PHPUnit_Framework_TestCase
{
    protected $_c = null;
    protected $array_minDb = array();
    protected $driver = "";
    public $connection = "";
    /*
    * Function before start test
    */    
    public function setUp() 
    {
        global $driver, $adminer, $connection;
        global $server, $user, $pass, $make_db;

        $driver = $_GET['driver_test'];
        
        $d_name = getNameOfDriver($driver); //to get name of driver
        
        $_GET[$d_name] = "driver"; //@todo what is setting to $_GET
        require_once DRIVER_FOLDER . $driver; //to add testing file
        
        /* set up for connect()
        define("SERVER", $server[$d_name]);
        $_GET["username"] = $user[$d_name];
        define("DRIVER", $d_name);
        $_SESSION["pwds"][DRIVER][SERVER][$_GET["username"]] = $pass[$d_name];
        */
        
        $connection = new Min_DB;
        $connection->connect($server[$d_name], $user[$d_name], $pass[$d_name]);
        //$connection->query($make_db);
        
        //$connection = connect();
    }

    /*
    * Function after finished test
    */    
    public function tearDown()
    {
        unset($this->_c);
    }
    
    /*
    * Tests of Min_DB
    */    
    
    public function test_connect()
    {
        global $driver, $connection;
        global $server, $user, $pass, $make_db;
        
        switch (getNameOfDriver($driver)) {
            case "mssql":
                $this->assertFalse($connection->connect("", "", "")); //test of connect with false
                $connection->connect($server[$d_name], $user[$d_name], $pass[$d_name]); //return to normal connect
                break;
            default:
                break;
        }

    }
    
    public function test_quote()
    {
        global $driver, $connection;
        
        switch (getNameOfDriver($driver)) {
            case "mssql":
                $this->assertEquals($connection->quote("test'test"), "'test''test'");
                break;
            default:
                break;
        }

    }
    
    public function test_query()
    {
        global $driver, $connection;
        
        switch (getNameOfDriver($driver)) {
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
    
    public function test_idf_escape()
    {
        global $driver;
        
        switch (getNameOfDriver($driver)) {
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
    
    //@todo within mysql, mssql
    public function test_table()
    {
        global $driver;

        switch (getNameOfDriver($driver)) {
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

  

    //@todo
    public function test_get_databases()
    {
        global $driver;

        //$this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, get_databases());
    }

    //@todo
    public function test_limit()
    {
        global $driver;
                                                 
        switch (getNameOfDriver($driver)) {
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

    
    public function test_limit1()
    {
        global $driver;

       switch (getNameOfDriver($driver)) {
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
    
     //@todo
    public function test_db_collation()
    {
        global $driver;

        
    }
    
    
    public function test_engines()
    {
        global $driver;

       switch (getNameOfDriver($driver)) {
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
        global $driver;

       switch (getNameOfDriver($driver)) {
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
        global $driver;

        if(getNameOfDriver($driver)=="mysql"){

           $this->assertEquals(fk_support("status"), true);
        }
        if(getNameOfDriver($driver)=="mssql"){
           $this->assertEquals(fk_support("status"), true);
        }
    	if(getNameOfDriver($driver)=="oracle"){
           $this->assertEquals(fk_support("status"), true);
        }
    }
    
    public function test_information_schema()
    {
        global $driver;

       switch (getNameOfDriver($driver)) {
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
        global $driver;

        switch (getNameOfDriver($driver)) {
            case "mssql":
                $this->assertEquals(exact_value("test'test"), "'test''test'");
                break;
            default:
               
                break;
        }
        
    }
    
    /*
    public function test_create_database()
    {
        global $driver, $connection;

        switch (getNameOfDriver($driver)) {
            case "mssql":
                $connection->query("CREATE DATABASE testTest2");
                $tmp = create_database("testTest", "Czech_BIN");
                print $tmp;
                $this->assertFalse($tmp);
                break;
            default:
               
                break;
        }
        
    }
    */
    
    public function test_auto_increment()
    {
        global $driver;

       switch (getNameOfDriver($driver)) {
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
        global $driver;

       switch (getNameOfDriver($driver)) {
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

    /*
     public function test_get_schema()
    {
        global $driver;

       switch (getNameOfDriver($driver)) {
            case "mssql":
                $this->assertTrue(get_schema());
                break;
            case "mysql":
                //@todo
                break;
            default:
               
                break;
        } 
    }
    */
    
    public function test_set_schema()
    {
        global $driver;

       switch (getNameOfDriver($driver)) {
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
        global $driver;

       switch (getNameOfDriver($driver)) {
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
        global $driver;

       switch (getNameOfDriver($driver)) {
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
        global $driver;

       switch (getNameOfDriver($driver)) {
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
