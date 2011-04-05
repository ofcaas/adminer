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
    public $driver = "";
    public $d_name = "";
    public $connection = "";
    public $server = "", $user = "", $pass = "";
    public $make_db = "", $make_db_name = "", $make_db_trigger = "", $make_db_view = "";

    /*
    * Function before start test
    */    
    public function setUp() 
    {
        global $connection, $test_server, $test_user, $test_pass, $test_make_db, $test_make_db_name, $test_make_trigger, $test_make_view;

        $this->driver = $_GET['driver_test'];
        $this->d_name = getNameOfDriver($this->driver); //to get name of driver

        $this->server = $test_server[$this->d_name];
        $this->user = $test_user[$this->d_name];
        $this->pass = $test_pass[$this->d_name];
        $this->make_db = $test_make_db[$this->d_name];
        $this->make_db_name = $test_make_db_name[$this->d_name];
        $this->make_db_trigger = $test_make_trigger[$this->d_name];
        $this->make_db_view = $test_make_view[$this->d_name];
       
        $_GET[$this->d_name] = "driver"; //@todo what is setting to $_GET
        require_once DRIVER_FOLDER . $this->driver; //to add testing file
        
        $connection = new Min_DB;
        $connection->connect($this->server, $this->user, $this->pass);
        
    }

    /*
    * Function after finished test
    */    
    public function tearDown()
    {
        global $connection;
        $db[] = $this->make_db_name;
        drop_databases($db);
       
        unset($this->_c);
    }
    
    /*
    * Tests of Min_DB
    */    
    
    // connect to server
    public function test_connect()
    {
        global $connection;
        
        switch ($this->d_name) {
            case "mssql":
                $this->assertFalse($connection->connect("", "", "")); //test of connect with false
                $connection->connect($this->server, $this->user, $this->pass); //return to normal connect
                break;
            default:
                break;
        }

    }
    
    
    // quote string
    public function test_quote()
    {
        global $connection;
        switch ($this->d_name) {
            case "mssql":
                create_database($this->make_db_name, "Czech_BIN");
                $connection->select_db($this->make_db_name);
                $connection->query($this->make_db);
                $connection->query("INSERT INTO TEST
                    (TEST_NAME, TEST_ID, TEST_DATE)
                    VALUES (" . $connection->quote("it's") . ",
                        " . $connection->quote("20") . ",
                        " . $connection->quote("") . ")");
                $res = $connection->result("select * from [mytest].[dbo].[TEST] where TEST_ID = '20'");
                $this->assertEquals($res, "it's");
                $this->assertEquals($connection->quote("test'test"), "'test''test'");
                $db[] = $this->make_db_name;
                drop_databases($db);
                break;
            default:
                break;
        }

    }
    
    
    
    // send query to server
    public function test_query_row()
    {
        global $connection;
        switch ($this->d_name) {
            case "mssql":
                $this->assertFalse($connection->query("randomtext"));
                $result = $connection->query("select suser_name()");
                $array = $result->fetch_row();
                $this->assertEquals($array[0], $this->user);
                break;
            default:
                break;
        }

    }

    // send query to server and get row of result, testuje i query
    public function test_result()
    {
        global $connection;

        switch ($this->d_name) {
            case "mssql":
                create_database($this->make_db_name, "Czech_BIN");
                $connection->select_db($this->make_db_name);
                $connection->query($this->make_db);
                $res = $connection->result("select * from [mytest].[dbo].[TEST]");
                $this->assertEquals($res, "jedna");
                $db[] = $this->make_db_name;
                drop_databases($db);
                break;
            default:
                break;
        }

    }

    /*
    public function test_fetch_assoc()
    {
        global $connection;

        switch ($d_name) {
            case "mssql":
                create_database("mytest", "Czech_BIN");
                $connection->select_db("mytest");
                $connection->query($make_db[$d_name]);
                $res = $connection->query("select * from [mytest].[dbo].[TEST]");
                $res2 = $res->fetch_assoc();
                $this->assertEquals($res2[1], "10");
                $db[] = $make_db_name[$d_name];
                drop_databases($db);
                break;
            default:
                break;
        }

    }
     */
    
    /*
     public function test_store_result()
    {
        global $connection,  $driver, $connection, $d_name, $make_db, $make_db_name;

        switch ($d_name) {
            case "mssql":
                create_database("mytest", "Czech_BIN");
                $connection->select_db("mytest");
                $connection->query($make_db[$d_name]);
                do {
			$result = $connection->store_result("select * from [mytest].[dbo].[TEST]");
			$arrayRes = $result->fetch_assoc();
                } while ($connection->next_result());
                $db[] = $make_db_name[$d_name];
                drop_databases($db);
                break;
            default:
                break;
        }

    }
    */

    /*
    * Tests of another functions
    */ 
    
    // escape string 
    public function test_idf_escape()
    {
        global $connection;

        switch ($this->d_name) {
            case "mssql":
                $this->assertTrue($connection->query("create database " . idf_escape("[TEST]")));
                $this->assertEquals(idf_escape("test]test"), "[test]]test]");
                $db[] = "[TEST]";
                $this->assertTrue(drop_databases($db));
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
        global $connection;
        
        switch ($this->d_name) {
            case "mssql":
                
                create_database($this->make_db_name, "Czech_BIN");
                $connection->select_db($this->make_db_name);
                $connection->query($this->make_db);
                $result = $connection->query("select * from " . table("TEST"));
                $row = $result->fetch_row();
                $this->assertEquals($row[0], "jedna");
                $_GET["ns"] = "dbo";
                $this->assertEquals(table("test]test"), "[dbo].[test]]test]");
                $db[] = $this->make_db_name;
                drop_databases($db);
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
        global $connection;

        switch ($this->d_name) {
            case "mssql":
                $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, get_databases());
                create_database($this->make_db_name, "Czech_BIN");
                $mydb = false;
                foreach(get_databases() as $val){
                    if($val == $this->make_db_name){
                        $mydb = true;
                    }
                }
                $this->assertTrue($mydb);
                $db[] = $this->make_db_name;
                drop_databases($db);
                break;
            default:

                break;
        }
        
    }

    // show how formulate SQL with limit
    public function test_limit()
    {
        global $connection;

        switch ($this->d_name) {
            case "mssql":
                create_database($this->make_db_name, "Czech_BIN");
                $connection->select_db($this->make_db_name);
                $connection->query($this->make_db);
                $rows = get_rows("select" . limit(" * from TEST", " where TEST_ID != '500'", "2"));
                $this->assertEquals(count($rows), "2");
                $db[] = $this->make_db_name;
                drop_databases($db);
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
       global $connection;

        switch ($this->d_name) {
            case "mssql":
                create_database($this->make_db_name, "Czech_BIN");
                $connection->select_db($this->make_db_name);
                $connection->query($this->make_db);
                $rows = get_rows("select" . limit1(" * from TEST", " where TEST_ID != '500'"));
                $this->assertEquals(count($rows), "1");
                $db[] = $this->make_db_name;
                drop_databases($db);
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
        global $connection,  $make_db_name;

        switch ($this->d_name) {
            case "mssql":
                create_database($this->make_db_name, "Czech_BIN");
                $this->assertEquals("Czech_BIN", db_collation($this->make_db_name, ""));
                $db[] = $this->make_db_name;
                drop_databases($db);
                break;
            default:

                break;
        }

    }
    
        
    
    public function test_engines()
    {

       switch ($this->d_name) {
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
        global $connection;

       switch ($this->d_name) {
            case "mssql":
                $this->assertEquals(logged_user(), $this->user);
                break;
            default:
               
                break;
        } 
    }


    public function test_tables_list()
    {
        global $connection;

        switch ($this->d_name) {
            case "mssql":
                create_database($this->make_db_name, "Czech_BIN");
                $connection->select_db($this->make_db_name);
                $connection->query($this->make_db);
                $tables = array ( // from make_db
                    "TEST" => "USER_TABLE",
                    "TEST2" => "USER_TABLE",
                    "NUMBERS" => "USER_TABLE",
                    "NEWTABLE" => "USER_TABLE"
                );
                $this->assertEquals(tables_list(), $tables);
                $db[] = $this->make_db_name;
                drop_databases($db);
                break;
            default:
                break;
        }

    }

    public function test_count_tables()
    {
        global $connection;

        switch ($this->d_name) {
            case "mssql":
                create_database($this->make_db_name, "Czech_BIN");
                $connection->select_db($this->make_db_name);
                $connection->query($this->make_db);
                $tables = "4";
                $db[] = $this->make_db_name;
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
        global $connection;

        switch ($this->d_name) {
            case "mssql":
                create_database($this->make_db_name, "Czech_BIN");
                $connection->select_db($this->make_db_name);
                $connection->query($this->make_db);
                $tables = array (
                    "Name" => "TEST",
                    "Engine" => "USER_TABLE"
                );
                $db[] = $this->make_db_name;
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
       global $connection;
       
       switch ($this->d_name) {
            case "mssql":
                create_database($this->make_db_name, "Czech_BIN");
                $connection->select_db($this->make_db_name);
                $connection->query($this->make_db);
                $connection->query($this->make_db_view); // view must be the first query
                $this->assertTrue(is_view(table_status("v")));
                $this->assertFalse(is_view(table_status("TEST")));
                $db[] = $this->make_db_name;
                drop_databases($db);
                break;
            case "mysql":

                break;
            default:
               
                break;
        } 
    }

    function test_fk_support()
    {
        switch ($this->d_name) {
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

    public function test_fields()
    {
        global $connection;

        switch ($this->d_name) {
            case "mssql":
                create_database($this->make_db_name, "Czech_BIN");
                $connection->select_db($this->make_db_name);
                $connection->query($this->make_db);
                $tables['TEST_NAME'] = array (
                    "field" =>"TEST_NAME",
                    "full_type" => "varchar(5)",
                    "type" => "varchar",
                    "length" => "5",
                    "default" => NULL,
                    "null" => "0",
                    "auto_increment" => "0",
		    "collation" => "Czech_BIN",
                    "privileges" => array("insert" => 1, "select" => 1, "update" => 1),
                    "primary" => "0"
                    );
                $tables['TEST_ID'] = array (
                    "field" =>"TEST_ID",
                    "full_type" => "int",
                    "type" => "int",
                    "length" => "",
                    "default" => NULL,
                    "null" => "0",
                    "auto_increment" => "0",
		    "collation" => NULL,
                    "privileges" => array("insert" => 1, "select" => 1, "update" => 1),
                    "primary" => "0"
                    );
                $tables['TEST_DATE'] = array (
                    "field" =>"TEST_DATE",
                    "full_type" => "varchar(1)",
                    "type" => "varchar",
                    "length" => "1",
                    "default" => NULL,
                    "null" => "0",
                    "auto_increment" => "0",
		    "collation" => "Czech_BIN",
                    "privileges" => array("insert" => 1, "select" => 1, "update" => 1),
                    "primary" => "0"
                    );
                $this->assertEquals(fields("TEST"), $tables);
                $db[] = $this->make_db_name;
                drop_databases($db);
                break;
            default:
                break;
        }

    }


        public function test_indexes()
    {
        global $connection;

       switch ($this->d_name) {
            case "mssql":
                create_database($this->make_db_name, "Czech_BIN");
                $connection->select_db($this->make_db_name);
                $connection->query($this->make_db);
                $tables['PKINDEX_IDX']['type'] = "PRIMARY";
                $tables['PKINDEX_IDX']['lengths'] = array();
                $tables['PKINDEX_IDX']['columns'][1] = "ID";
                $this->assertEquals(indexes("NEWTABLE"), $tables);
                $db[] = $this->make_db_name;
                drop_databases($db);
                break;
            default:
                break;
        }

    }


     public function test_view()
    {
        global $connection;

        switch ($this->d_name) {
            case "mssql":
                create_database($this->make_db_name, "Czech_BIN");
                $connection->select_db($this->make_db_name);
                $connection->query($this->make_db);
                $connection->query($this->make_db_view); // view must be the first query
                $tables = array ( "select" =>"SELECT * FROM TEST WHERE TEST_NAME = 1");
                $this->assertEquals(view("v"), $tables);
                $db[] = $this->make_db_name;
                drop_databases($db);
                break;
            default:
                break;
        }

    }


    public function test_information_schema()
    {
       switch ($this->d_name) {
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

    public function test_error()
    {
        global $connection;
        switch ($this->d_name) {
            case "mssql":
                create_database($this->make_db_name, "Czech_BIN");
                $connection->select_db($this->make_db_name);
                $connection->query("random");
                $this->assertEquals(error(), "Could not find stored procedure &#039;random&#039;.");
                $db[] = $this->make_db_name;
                drop_databases($db);
                break;
            default:

                break;
        }

    }
    
    public function test_exact_value()
    {
        global $connection;
        
        switch ($this->d_name) {
            case "mssql":
                create_database($this->make_db_name, "Czech_BIN");
                $connection->select_db($this->make_db_name);
                $connection->query($this->make_db);
                $result = $connection->result("select * from [dbo].[TEST] where TEST_NAME like " . exact_value("j%"));
                $this->assertEquals($result, 'jedna');
                $result = $connection->result("select * from [dbo].[TEST] where TEST_ID like " . exact_value("1%"));
                $this->assertEquals($result, 'jedna');
                $result = $connection->result("select * from [dbo].[TEST] where TEST_NAME like " . exact_value("%nA"));
                $this->assertEquals($result, null);
                $this->assertEquals(exact_value("test'test"), "'test''test'");

                $db[] = $this->make_db_name;
                drop_databases($db);
                break;
            default:
               
                break;
        }
        
    }
    
    
    public function test_create_database()
    {
        global $connection;
        switch ($this->d_name) {
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
        switch ($this->d_name) {
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
        switch ($this->d_name) {
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
       global $connection;
       
       switch ($this->d_name) {
            case "mssql":
                $_POST["Auto_increment"] = "5"; //to set auto_increment

                create_database($this->make_db_name, "Czech_BIN");
                $connection->select_db($this->make_db_name);
                $connection->query($this->make_db);
                $connection->query("CREATE TABLE test_ai (
                      id INTEGER ". auto_increment() .",
                      name varchar(16) NOT NULL
                    )");
                $connection->query("insert into test_ai (name) values ('test')");
                $this->assertEquals($connection->result("select id from test_ai"), "5");
                drop_tables(array("test_ai"));
                $db[] = $this->make_db_name;
                drop_databases($db);
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
        global $connection;

        switch ($this->d_name) {
            case "mssql":
                $this->assertEquals(last_id(), NULL);
                create_database($this->make_db_name, "Czech_BIN");
                $connection->select_db($this->make_db_name);
                $connection->query($this->make_db);
                $connection->query("insert into NUMBERS (EN, FR) VALUES ('', '')");
                $connection->query("insert into NUMBERS (EN, FR) VALUES ('', '')");
                $this->assertEquals(last_id(), "2");
                
                $db[] = $this->make_db_name;
                drop_databases($db);
                break;
            default:

                break;
        }
    }

    
    public function test_foreign_keys()
    {
        global $connection;

        switch ($this->d_name) {
            case "mssql":
                create_database($this->make_db_name, "Czech_BIN");
                $connection->select_db($this->make_db_name);
                $connection->query($this->make_db);
                $tables['FK_ID2'] = array (
                   "table" => "TEST",
                   "source" => array("ID2"),
                   "target" => array("TEST_ID")
                );
                $this->assertEquals(foreign_keys("TEST2"), $tables);
                $db[] = $this->make_db_name;
                drop_databases($db);
                break;
            default:
                break;
        }

    }
    
    
    public function test_insert_into()
    {
        global $connection;

        switch ($this->d_name) {
            case "mssql":
                create_database($this->make_db_name, "Czech_BIN");
                $connection->select_db($this->make_db_name);
                $connection->query($this->make_db);
                $tables = array (
                   "TEST_NAME" => "'dva'",
                   "TEST_ID" => "'2'",
                   "TEST_DATE" => "'M'"
                );
                $this->assertTrue(insert_into("TEST", $tables));
                
                $this->assertEquals($connection->result("select TEST_NAME from TEST where TEST_ID=2"), "dva");
                $db[] = $this->make_db_name;
                drop_databases($db);
                break;
            default:
                break;
        }

    }

    public function test_truncate_tables()
    {
        global $connection;

        switch ($this->d_name) {
            case "mssql":
                create_database($this->make_db_name, "Czech_BIN");
                $connection->select_db($this->make_db_name);
                $connection->query($this->make_db);
                $tables = array (
                   "TEST2"
                );
                $this->assertTrue(truncate_tables($tables));

                $this->assertEquals($connection->result("select * from TEST2"), NULL);
                $db[] = $this->make_db_name;
                drop_databases($db);
                break;
            default:
                break;
        }

    }


    public function test_drop_view()
    {
        global $connection;

        switch ($this->d_name) {
            case "mssql":
                create_database($this->make_db_name, "Czech_BIN");
                $connection->select_db($this->make_db_name);
                $connection->query($this->make_db);
                $connection->query($this->make_db_view); // view must be the first query
                $views = array (
                        "v"
                         );
                $this->assertTrue(drop_views($views));
                $tables = array ( "select" =>"");
                $this->assertEquals(view("v"), $tables);
                $db[] = $this->make_db_name;
                drop_databases($db);
                break;
            default:
                break;
        }

    }

    public function test_drop_tables()
    {
        global $connection;

        switch ($this->d_name) {
            case "mssql":
                create_database($this->make_db_name, "Czech_BIN");
                $connection->select_db($this->make_db_name);
                $connection->query($this->make_db);
                $tables = array ( // from make_db
                    "TEST" => "USER_TABLE",
                    "TEST2" => "USER_TABLE",
                    "NUMBERS" => "USER_TABLE",
                    "NEWTABLE" => "USER_TABLE"
                );
                $this->assertEquals(tables_list(), $tables);
                $this->assertTrue(drop_tables(array("TEST2")));
                $tables2 = array ( // from make_db
                    "TEST" => "USER_TABLE",
                    "NUMBERS" => "USER_TABLE",
                    "NEWTABLE" => "USER_TABLE"
                );
                $this->assertEquals(tables_list(), $tables2);
                $db[] = $this->make_db_name;
                drop_databases($db);
                break;
            default:
                break;
        }

    }

    /*
    public function test_move_tables()
    {
        global $connection,  $driver, $connection, $d_name, $make_db, $make_db_name;

        switch ($d_name) {
            case "mssql":
                create_database("mytest", "Czech_BIN");
                $connection->select_db("mytest");
                $connection->query($make_db[$d_name]);
                $connection->query($make_view[$d_name]);
                $this->assertTrue(move_tables(array("newtable"), array("v"), "guest"));
                
                $db[] = $make_db_name[$d_name];
                drop_databases($db);
                break;
            default:
                break;
        }

    }
     */

    /*
    public function test_triggers()
    {
        global $connection,  $driver, $connection, $d_name, $make_db, $make_db_name, $make_trigger;

        switch ($d_name) {
            case "mssql":
                create_database("mytest", "Czech_BIN");
                $connection->select_db("mytest");
                $connection->query($make_db[$d_name]);
                $connection->query($make_trigger[$d_name]);
                
                $tables = array();
                $this->assertEquals(triggers("test"), $tables);
                $db[] = $make_db_name[$d_name];
                drop_databases($db);
                break;
            default:
                break;
        }

    }
    */

    public function test_trigger_options()
    {
       switch ($this->d_name) {
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

    public function test_schemas()
    {
         switch ($this->d_name) {
            case "mssql":
                $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, schemas());
                break;
            default:

                break;
        }

    }

    
     public function test_get_schema()
    {
       switch ($this->d_name) {
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
       switch ($this->d_name) {
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
       switch ($this->d_name) {
            case "mssql":
                $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, show_variables());
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
       switch ($this->d_name) {
            case "mssql":
                $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, show_status());
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
       switch ($this->d_name) {
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
