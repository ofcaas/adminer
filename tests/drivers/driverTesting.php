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
                $this->assertFalse($connection->query("randomtext"));
                break;
            default:
                break;
        }

    }

    // send query to server and get row of result, testuje i query
    public function test_result()
    {
        global $driver, $connection, $d_name, $make_db, $make_db_name;

        switch ($d_name) {
            case "mssql":
                create_database("mytest", "Czech_BIN");
                $connection->select_db("mytest");
                $connection->query($make_db[$d_name]);
                $this->assertEquals($connection->result("select * from [mytest].[dbo].[TEST]"), "jedna");
                $db[] = $make_db_name[$d_name];
                drop_databases($db);
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

  

    // get names of dbs
    public function test_get_databases()
    {
        global $driver, $d_name, $make_db_name;

        switch ($d_name) {
            case "mssql":
                $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, get_databases());
                create_database("mytest", "Czech_BIN");
                $mydb = false;
                foreach(get_databases() as $val){
                    if($val == "mytest"){
                        $mydb = true;
                    }
                }
                $this->assertTrue($mydb);
                $db[] = $make_db_name[$d_name];
                drop_databases($db);
                break;
            default:

                break;
        }
        
    }

    // show how formulate SQL with limit
    public function test_limit()
    {
        global $driver, $d_name;
                                                 
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

    public function test_db_collation()
    {
        global $driver, $d_name, $make_db_name;

        switch ($d_name) {
            case "mssql":
                create_database("mytest", "Czech_BIN");
                $this->assertEquals("Czech_BIN", db_collation("mytest", ""));
                $db[] = $make_db_name[$d_name];
                drop_databases($db);
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
    
    public function test_logged_user()
    {
        global $driver, $d_name, $user;

       switch ($d_name) {
            case "mssql":
                $this->assertEquals(logged_user(), $user[$d_name]);
                break;
            default:
               
                break;
        } 
    }


    public function test_tables_list()
    {
        global $driver, $connection, $d_name, $make_db, $make_db_name;

        switch ($d_name) {
            case "mssql":
                create_database("mytest", "Czech_BIN");
                $connection->select_db("mytest");
                $connection->query($make_db[$d_name]);
                $tables = array ( // from make_db
                    "TEST" => "USER_TABLE",
                    "TEST2" => "USER_TABLE",
                    "NUMBERS" => "USER_TABLE",
                    "NEWTABLE" => "USER_TABLE"
                );
                $this->assertEquals(tables_list(), $tables);
                $db[] = $make_db_name[$d_name];
                drop_databases($db);
                break;
            default:
                break;
        }

    }

    public function test_count_tables()
    {
        global $driver, $connection, $d_name, $make_db, $make_db_name;

        switch ($d_name) {
            case "mssql":
                create_database("mytest", "Czech_BIN");
                $connection->select_db("mytest");
                $connection->query($make_db[$d_name]);
                $tables = "4";
                $db[] = $make_db_name[$d_name];
                $res = count_tables($db);
                $this->assertEquals($res['mytest'], $tables);
                drop_databases($db);
                break;
            default:
                break;
        }

    }

    public function test_table_status()
    {
        global $driver, $connection, $d_name, $make_db, $make_db_name;

        switch ($d_name) {
            case "mssql":
                create_database("mytest", "Czech_BIN");
                $connection->select_db("mytest");
                $connection->query($make_db[$d_name]);
                $tables = array (
                    "Name" => "TEST",
                    "Engine" => "USER_TABLE"
                );
                $db[] = $make_db_name[$d_name];
                $res = table_status("TEST");
                $this->assertEquals($res, $tables);

                $tables2["TEST"] = array (
                    "Name" => "TEST",
                    "Engine" => "USER_TABLE"
                );
                $tables2["TEST2"] = array (
                    "Name" => "TEST2",
                    "Engine" => "USER_TABLE"
                );
                $tables2["NUMBERS"] = array (
                    "Name" => "NUMBERS",
                    "Engine" => "USER_TABLE"
                );
                $tables2["NEWTABLE"] = array (
                    "Name" => "NEWTABLE",
                    "Engine" => "USER_TABLE"
                );
                $res = table_status();
                $this->assertEquals($res, $tables2);
                drop_databases($db);
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


     public function test_view()
    {
        global $driver, $connection, $d_name, $make_db, $make_db_name, $make_view;

        switch ($d_name) {
            case "mssql":
                create_database("mytest", "Czech_BIN");
                $connection->select_db("mytest");
                $connection->query($make_db[$d_name]);
                $connection->query($make_view[$d_name]); // view must be the first query
                $tables = array ( "select" =>"SELECT * FROM TEST WHERE TEST_NAME = 1");
                $this->assertEquals(view("v"), $tables);
                $db[] = $make_db_name[$d_name];
                drop_databases($db);
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

    public function test_last_id()
    {
        global $driver, $d_name;

       switch ($d_name) {
            case "mssql":
                $this->assertEquals(last_id(), NULL);
                break;
            default:

                break;
        }
    }
    
    
    public function test_insert_into()
    {
        global $driver, $connection, $d_name, $make_db, $make_db_name;

        switch ($d_name) {
            case "mssql":
                create_database("mytest", "Czech_BIN");
                $connection->select_db("mytest");
                $connection->query($make_db[$d_name]);
                $tables = array (
                   "TEST_NAME" => "'dva'",
                   "TEST_ID" => "'2'",
                   "TEST_DATE" => "'M'"
                );
                $this->assertTrue(insert_into("TEST", $tables));
                
                $this->assertEquals($connection->result("select TEST_NAME from TEST where TEST_ID=2"), "dva");
                $db[] = $make_db_name[$d_name];
                drop_databases($db);
                break;
            default:
                break;
        }

    }

    public function test_truncate_tables()
    {
        global $driver, $connection, $d_name, $make_db, $make_db_name;

        switch ($d_name) {
            case "mssql":
                create_database("mytest", "Czech_BIN");
                $connection->select_db("mytest");
                $connection->query($make_db[$d_name]);
                $tables = array (
                   "TEST"
                );
                $this->assertTrue(truncate_tables($tables));

                $this->assertEquals($connection->result("select * from TEST"), NULL);
                $db[] = $make_db_name[$d_name];
                drop_databases($db);
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
