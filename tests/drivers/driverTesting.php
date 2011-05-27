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
             
    }
    
    /*
    * Tests of Min_DB
    */    
    
    // connect to server
    public function test_connect()
    {
        global $connection;
       
        $this->assertFalse($connection->connect("", "", "")); //test of connect with false
        $connection->connect($this->server, $this->user, $this->pass); //return to normal connect
    }
    
    
    // quote string
    public function test_quote_result()
    {
        global $connection;
        
        switch ($this->d_name) {
            case "mssql":
                create_db($this);
                $connection->query("INSERT INTO TEST
                    (TEST_NAME, TEST_ID, TEST_DATE)
                    VALUES (" . $connection->quote("it's") . ",
                        " . $connection->quote("20") . ",
                        " . $connection->quote("") . ")");
                $res = $connection->result("select * from [mytest].[dbo].[TEST] where TEST_ID = '20'");
                $this->assertEquals($res, "it's");
                $this->assertEquals($connection->quote("test'test"), "'test''test'");
                drop_db($this);
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

    // send query to server and get row of result
    public function test_result()
    {
        global $connection;

        switch ($this->d_name) {
            case "mssql":
                create_db($this);
                $res = $connection->result("select * from [mytest].[dbo].[TEST]");
                $this->assertEquals($res, "jedna");
                drop_db($this);
                break;
            default:
                break;
        }

    }

    
    public function test_fetch_assoc()
    {
       global $connection;
        switch ($this->d_name) {
            case "mssql":
                create_db($this);
                $result = $connection->query("select * from [TEST] where TEST_ID = '10'");
                $array = $result->fetch_assoc();
                $this->assertEquals($array["TEST_NAME"], "jedna");
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
                
                create_db($this);
                $result = $connection->query("select * from " . table("TEST"));
                $row = $result->fetch_row();
                $this->assertEquals($row[0], "jedna");
                $_GET["ns"] = "dbo";
                $this->assertEquals(table("test]test"), "[dbo].[test]]test]");
                drop_db($this);
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
                $this->assertTrue(is_array_string(get_databases()));
                $this->assertTrue($mydb);
                drop_db($this);
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
                create_db($this);
                $rows = get_rows("select" . limit(" * from TEST", " where TEST_ID != '500'", "2"));
                $this->assertEquals(count($rows), "2");
                drop_db($this);
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
                create_db($this);
                $rows = get_rows("select" . limit1(" * from TEST", " where TEST_ID != '500'"));
                $this->assertEquals(count($rows), "1");
                drop_db($this);
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
                drop_db($this);
                break;
            default:

                break;
        }

    }
    
        
    
    public function test_engines()
    {
       $this->assertTrue(is_array_string(engines()));

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
      
        $this->assertEquals(logged_user(), $this->user);
    }


    public function test_tables_list()
    {
        global $connection;

        $tmp = tables_list();
        $this->assertTrue(is_array_key_string($tmp));
        $this->assertTrue(is_array_val_string($tmp));

        switch ($this->d_name) {
            case "mssql":
                create_db($this);
                $tables = array ( // from make_db
                    "TEST" => "USER_TABLE",
                    "TEST2" => "USER_TABLE",
                    "NUMBERS" => "USER_TABLE",
                    "NEWTABLE" => "USER_TABLE"
                );
                $this->assertEquals(tables_list(), $tables);
                drop_db($this);
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
                create_db($this);
                $tables = "4";
                $db[] = $this->make_db_name;
                $res = count_tables($db);
                $this->assertEquals($res['mytest'], $tables);
                drop_db($this);
                break;
            default:
                break;
        }

    }

    public function test_table_status()
    {
        global $connection;

        create_db($this);
        $tmp = table_status();
        $this->assertTrue(is_array_key_string($tmp));
        $this->assertTrue(is_array_val_array($tmp));

        switch ($this->d_name) {
            case "mssql":
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

                break;
            default:
                break;
        }
        drop_db($this);
    }


    public function test_is_view()
    {
       global $connection;

       create_db($this, "view");
       $this->assertTrue(is_bool(is_view(table_status("v"))));
       $this->assertTrue(is_bool(is_view(table_status("TEST"))));

       switch ($this->d_name) {
            case "mssql":
                $this->assertTrue(is_view(table_status("v")));
                $this->assertFalse(is_view(table_status("TEST")));

                break;
            case "mysql":

                break;
            default:               
                break;
        }
        drop_db($this);
    }

    function test_fk_support()
    {
        $this->assertTrue(is_bool(fk_support("status")));

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

        create_db($this);
        $this->assertTrue(is_array_key_string(fields("TEST")));

        switch ($this->d_name) {
            case "mssql":
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
                break;
            default:
                break;
        }
        drop_db($this);
    }


    public function test_indexes()
    {
       global $connection;

       create_db($this);
       $this->assertTrue(is_array_key_string(indexes("NEWTABLE")));
       $this->assertTrue(is_array_val_array(indexes("NEWTABLE")));

       switch ($this->d_name) {
            case "mssql":
                $tables['PKINDEX_IDX']['type'] = "PRIMARY";
                $tables['PKINDEX_IDX']['lengths'] = array();
                $tables['PKINDEX_IDX']['columns'][1] = "ID";
                $this->assertEquals(indexes("NEWTABLE"), $tables);
                break;
            default:
                break;
        }
        drop_db($this);
    }


     public function test_view()
    {
        global $connection;

        create_db($this, "view");
        $tmp = view("v");
        $this->assertTrue(is_array_key_string($tmp));
        $this->assertTrue(is_array_val_string($tmp));

        switch ($this->d_name) {
            case "mssql":
                $tables = array("select" => "SELECT * FROM TEST");
                $this->assertEquals(view("v"), $tables);
                break;
            default:
                break;
        }
        drop_db($this);
    }


    public function test_information_schema()
    {
       $this->assertTrue(is_bool(information_schema("")));

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

        $this->assertTrue(is_string(error()));

        switch ($this->d_name) {
            case "mssql":
                create_database($this->make_db_name, "Czech_BIN");
                $connection->select_db($this->make_db_name);
                $connection->query("random");
                $this->assertEquals(error(), "Could not find stored procedure &#039;random&#039;.");
                drop_db($this);
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
                create_db($this);
                $result = $connection->result("select * from [dbo].[TEST] where TEST_NAME like " . exact_value("j%"));
                $this->assertEquals($result, 'jedna');
                $result = $connection->result("select * from [dbo].[TEST] where TEST_ID like " . exact_value("1%"));
                $this->assertEquals($result, 'jedna');
                $result = $connection->result("select * from [dbo].[TEST] where TEST_NAME like " . exact_value("%nA"));
                $this->assertEquals($result, null);
                $this->assertEquals(exact_value("test'test"), "'test''test'");
                drop_db($this);
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
                $tmp = create_database("testTest", "Czech_BIN");
                $this->assertTrue($tmp);

                $mydb = false;
                foreach(get_databases() as $val){
                    if($val == "testTest"){
                        $mydb = true;
                    }
                }
                $this->assertTrue($mydb);
                break;
            default:
               
                break;
        }
        
    }
    
    public function test_rename_database()
    {
        global $connection;
        switch ($this->d_name) {
            case "mssql":
                define("DB", "testTest");
                $connection->query("CREATE DATABASE testTest2");
                $tmp = rename_database("testTest2", "Czech_CI_AS");
                $this->assertTrue($tmp);
                break;
            default:
                break;
        } 
    }
    
    public function test_drop_databases()
    {
        global $connection;
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

                create_db($this);
                $connection->query("CREATE TABLE test_ai (
                      id INTEGER ". auto_increment() .",
                      name varchar(16) NOT NULL
                    )");
                $connection->query("insert into test_ai (name) values ('test')");
                $this->assertEquals($connection->result("select id from test_ai"), "5");
                drop_tables(array("test_ai"));
                drop_db($this);
                break;
            case "mysql":
                //@todo
                break;
            default:
               
                break;
        } 
    }

    public function test_alter_table()
    {
       global $connection;

       switch ($this->d_name) {
            case "mssql":
                create_db($this);
                $col = array(
                    " TEST_NAME ",
                    " varchar(5) ",
                    " NOT NULL ", "", "", "", ""
                );
                // to create table with one column
                $this->assertTrue(alter_table("", "tabulka", array(array("", $col, "")), array(), "", "", "", "", ""));
                $is_added = false;
                foreach(tables_list() as $key => $val){
                    if($key == "tabulka") $is_added = true;
                }
                $this->assertTrue($is_added);

                // to rename table, alter column, rename column
                $col2 = array(
                    " TEST_NAME2 ",
                    " varchar(10) ",
                    " NOT NULL ", "", "", "", ""
                );
                $this->assertTrue(alter_table("tabulka", "tabulka2", array(array("TEST_NAME", $col2, ""), array("", $col, "")), array(), "", "", "", "", ""));
                $is_added = false;
                foreach(tables_list() as $key => $val){
                    if($key == "tabulka2") $is_added = true;
                }
                $this->assertTrue($is_added);
               

                // to drop column
                $this->assertTrue(alter_table("tabulka2", "tabulka2", array(array("TEST_NAME2", array(), "")), array(), "", "", "", "", ""));

                drop_tables(array("tabulka", "tabulka2"));
                drop_db($this);
                break;
            case "mysql":
                //@todo
                break;
            default:

                break;
        }
    }

    public function test_alter_indexes()
    {
       global $connection;

       switch ($this->d_name) {
            case "mssql":
                create_db($this);
                //to create index
                $this->assertTrue(alter_indexes("NUMBERS", array(array("INDEX", "(EN)"))));
                $tables = array(
                    'type' => "INDEX",
                    'lengths' => array(),
                    'columns' => array("EN")
                );
                $is_index = false;
                foreach(indexes("NUMBERS") as $key => $value){                 
                    if($value['type'] == "INDEX" || $value['columns'][1] == "EN"){
                        $is_index = true;
                        $name = $key;
                    }
                }
                $this->assertTrue($is_index);

                //to drop index
                $this->assertTrue(alter_indexes("NUMBERS", array(array("INDEX", $name, DROP))));
                $array = indexes("NUMBERS");
                $this->assertTrue(empty($array));

                drop_db($this);
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
                create_db($this);
                $connection->query("insert into NUMBERS (EN, FR) VALUES ('', '')");
                $connection->query("insert into NUMBERS (EN, FR) VALUES ('', '')");
                $this->assertEquals(last_id(), "2");
                
                drop_db($this);
                break;
            default:

                break;
        }
    }

    
    public function test_foreign_keys()
    {
        global $connection;

        create_db($this);
        $tmp = foreign_keys("TEST2");
        $this->assertTrue(is_array_key_string($tmp));
        $this->assertTrue(is_array_val_array($tmp));

        switch ($this->d_name) {
            case "mssql":
                $tables['FK_ID2'] = array (
                   "table" => "TEST",
                   "source" => array("ID2"),
                   "target" => array("TEST_ID")
                );
                $this->assertEquals(foreign_keys("TEST2"), $tables);
                break;
            default:
                break;
        }
        drop_db($this);
    }
    
    
    public function test_insert_into()
    {
        global $connection;

        switch ($this->d_name) {
            case "mssql":
                create_db($this);
                $tables = array (
                   "TEST_NAME" => "'dva'",
                   "TEST_ID" => "'2'",
                   "TEST_DATE" => "'M'"
                );
                $this->assertTrue(insert_into("TEST", $tables));
                
                $this->assertEquals($connection->result("select TEST_NAME from TEST where TEST_ID=2"), "dva");
                drop_db($this);
                break;
            default:
                break;
        }

    }

    public function test_insert_update()
    {
        global $connection;

        switch ($this->d_name) {
            case "mssql":
                create_db($this);
                $tables = array (
                   "[TEST_NAME]" => "'dva'",
                   "[TEST_ID]" => "'10'",
                   "[TEST_DATE]" => "'M'"
                );
                $primary["TEST_ID"] = "'10'";
                $this->assertTrue(insert_update("TEST", $tables, $primary));
                $this->assertEquals($connection->result("select TEST_NAME from TEST where TEST_ID=10"), "dva");
                drop_db($this);
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
                create_db($this);
                $tables = array (
                   "TEST2"
                );
                $this->assertTrue(truncate_tables($tables));

                $this->assertEquals($connection->result("select * from TEST2"), NULL);
                drop_db($this);
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
                create_db($this, "view");
                $views = array (
                        "v"
                         );
                $this->assertTrue(drop_views($views));
                $tables = array ( "select" =>"");
                $this->assertEquals(view("v"), $tables);
                drop_db($this);
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
                create_db($this);
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
                drop_db($this);
                break;
            default:
                break;
        }

    }

    
    public function test_move_tables()
    {
        global $connection;

        switch ($this->d_name) {
            case "mssql":
                create_db($this, "view");
                $this->assertTrue(move_tables(array("NUMBERS", "TEST"), array("v"), "guest"));
                
                drop_db($this);
                break;
            default:
                break;
        }

    }
    

    
    public function test_trigger()
    {
        global $connection;

        switch ($this->d_name) {
            case "mssql":
                create_db($this, "trigger");
                $tmp = trigger("potvrzeni");
                $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $tmp);
                $this->assertEquals($tmp, array(
                    array(
                        "Trigger" => "potvrzeni",
                        "Event" => "INSERT",
                        "Timing" => "AFTER",
                        "text" => "CREATE TRIGGER potvrzeni ON TEST AFTER INSERT AS BEGIN print 'ok' END"
                    ),
                    "Statement" => ""
                ));
                drop_db($this);
                break;
            default:
                break;
        }

    }

    public function test_triggers()
    {
        global $connection;

        switch ($this->d_name) {
            case "mssql":
                create_db($this, "trigger");
                $tmp = triggers("TEST");
                $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $tmp);
                $this->assertEquals($tmp, array(
                    "potvrzeni" => array(
                        "AFTER", "INSERT"
                    )
                ));
                drop_db($this);
                break;
            default:
                break;
        }

    }
    

    public function test_trigger_options()
    {
       $tmp = trigger_options();
       $this->assertTrue(is_array_val_array($tmp));

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
       $tmp = schemas();
       $this->assertTrue(is_array_val_string($tmp));
       
    }


    
    public function test_schema()
    {
       
       switch ($this->d_name) {
            case "mssql":
                $this->assertTrue(set_schema("dbo")); //set_schema return only true value with no action
                $this->assertEquals(get_schema(), "dbo");
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
       $tmp = show_variables();
       $this->assertTrue(is_array_key_string($tmp));
       $this->assertTrue(is_array_val_numeric($tmp));
    }

    public function test_show_status()
    {
       $tmp = show_status();
       $this->assertTrue(is_array_key_string($tmp));
       $this->assertTrue(is_array_val_numeric($tmp));
    }

    
    public function test_support()
    {
       switch ($this->d_name) {
            case "mssql":
                $this->assertEquals(support("trigger"), 1);
                $this->assertEquals(support("view"), 1);
                $this->assertEquals(support("drop_col"), 1);
                $this->assertEquals(support("scheme"), 1);
                $this->assertFalse(support("sequence"));
                $this->assertFalse(support("type"));
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
