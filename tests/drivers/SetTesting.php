<?php

/*
 * Set parametrs to run tests - connect etc.
 *  
 * @author Jakub Cernohuby
 */

/*
* Here set the connection params to each server, name of server must be 
* the same as the name of server in adminer/driver folder.
* 
* For example:
* $server['mssql'] = "localhost";
* $server['mysql'] = "192.168.1.2";
*
*/

$test_server['mssql'] = "localhost"; //adrress of server
$test_user['mssql'] = "kuba";        //user name
$test_pass['mssql'] = "cvut";      //user password

$test_server['mysql'] = "localhost"; //adrress of server
$test_user['mysql'] = "root";        //user name
$test_pass['mysql'] = "";            //user password
 
 
 
/*
 * set up testing db
 *
 */
 $test_make_db_name['mssql'] = "mytest";
 $test_make_db['mssql'] = "

-- TABLE TEST
CREATE TABLE TEST (
  TEST_NAME VARCHAR(5) NOT NULL,
  TEST_ID INTEGER DEFAULT '0' NOT NULL,
  TEST_DATE VARCHAR(1) NOT NULL
);
ALTER TABLE TEST ADD CONSTRAINT PK_TEST PRIMARY KEY (TEST_ID);

INSERT INTO TEST (TEST_NAME, TEST_ID, TEST_DATE) VALUES ('jedna', '10', ' ');
INSERT INTO TEST (TEST_NAME, TEST_ID, TEST_DATE) VALUES ('tri', '100', ' ');
INSERT INTO TEST (TEST_NAME, TEST_ID, TEST_DATE) VALUES ('sest', '5000', ' ');



-- TABLE TEST2 with some CONSTRAINTs and an INDEX
CREATE TABLE TEST2 (
  ID INTEGER NOT NULL,
  FIELD1 INTEGER,
  FIELD2 CHAR(15),
  FIELD3 VARCHAR(50),
  FIELD4 INTEGER,
  FIELD5 INTEGER,
  ID2 INTEGER NOT NULL,
  CONSTRAINT FK_ID2 FOREIGN KEY (ID2) REFERENCES TEST (TEST_ID)
);
ALTER TABLE TEST2 ADD CONSTRAINT PK_TEST2 PRIMARY KEY (ID2);
ALTER TABLE TEST2 ADD CONSTRAINT TEST2_FIELD1ID_IDX UNIQUE (ID, FIELD1);
ALTER TABLE TEST2 ADD CONSTRAINT TEST2_FIELD4_IDX UNIQUE (FIELD4);
CREATE INDEX TEST2_FIELD5_IDX ON TEST2(FIELD5);

-- TABLE NUMBERS
CREATE TABLE NUMBERS (
  NUMBER INTEGER IDENTITY(1,1),
  EN CHAR(100) NOT NULL,
  FR CHAR(100) NOT NULL
);


-- TABLE NEWTABLE
CREATE TABLE NEWTABLE (
  ID INT DEFAULT 0 NOT NULL,
  SOMENAME VARCHAR (12),
  SOMEDATE TIMESTAMP NOT NULL,
  SOMEDESCRIPTION VARCHAR(12) NULL
);
ALTER TABLE NEWTABLE ADD CONSTRAINT PKINDEX_IDX PRIMARY KEY (ID);

";

$test_make_view['mssql'] = "CREATE VIEW v AS SELECT * FROM TEST WHERE TEST_NAME = 1";
$test_make_trigger['mssql'] = "CREATE TRIGGER potvrzeni ON test AFTER INSERT AS BEGIN print 'ok' END";

?>
