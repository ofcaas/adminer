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

$server['mssql'] = "localhost"; //adrress of server
$user['mssql'] = "cvut";        //user name
$pass['mssql'] = "ovecka";      //user password

$server['mysql'] = "localhost"; //adrress of server
$user['mysql'] = "root";        //user name
$pass['mysql'] = "";            //user password
 
 
 
 /*
 * setup params of MSSQL
 *
 */

 $server_info['mssql'] = "10.00.1600"; // version of MSSQL
 
  
 
/*
 * setup testing db
 *
 */
 
 $make_db['mssql'] = "
CREATE DATABASE mytest;

USE mytest;
 
-- TABLE TEST
CREATE TABLE TEST (
  TEST_NAME CHAR(30) NOT NULL,
  TEST_ID INTEGER DEFAULT '0' NOT NULL,
  TEST_DATE TIMESTAMP NOT NULL
);
ALTER TABLE TEST ADD CONSTRAINT PK_TEST PRIMARY KEY (TEST_ID);
 
-- TABLE TEST2 with some CONSTRAINTs and an INDEX
CREATE TABLE TEST2 (
  ID INTEGER NOT NULL,
  FIELD1 INTEGER,
  FIELD2 CHAR(15),
  FIELD3 VARCHAR(50),
  FIELD4 INTEGER,
  FIELD5 INTEGER,
  ID2 INTEGER NOT NULL
);
ALTER TABLE TEST2 ADD CONSTRAINT PK_TEST2 PRIMARY KEY (ID2);
ALTER TABLE TEST2 ADD CONSTRAINT TEST2_FIELD1ID_IDX UNIQUE (ID, FIELD1);
ALTER TABLE TEST2 ADD CONSTRAINT TEST2_FIELD4_IDX UNIQUE (FIELD4);
CREATE INDEX TEST2_FIELD5_IDX ON TEST2(FIELD5);
 
-- TABLE NUMBERS
CREATE TABLE NUMBERS (
  NUMBER INTEGER DEFAULT '0' NOT NULL,
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
 
-- VIEW on TEST
CREATE VIEW \"testview\"(
  TEST_NAME,
  TEST_ID,
  TEST_DATE
) AS
SELECT *
FROM TEST
WHERE TEST_NAME LIKE 't%';
 
-- VIEW on NUMBERS
CREATE VIEW \"numbersview\"(
  NUMBER,
  TRANS_EN,
  TRANS_FR
) AS
SELECT *
FROM NUMBERS
WHERE NUMBER > 100;
 
-- TRIGGER on NEWTABLE
CREATE TRIGGER TEST_TRIGGER ON NEWTABLE
FOR UPDATE AS
DECLARE @oldName VARCHAR(100)
DECLARE @newId INTEGER
SELECT @oldName = (SELECT somename FROM Deleted)
SELECT @newId = (SELECT id FROM Inserted)
BEGIN
  UPDATE NEWTABLE SET somedescription = @oldName WHERE id = @newId;
END;
 
-- FUNCTION
CREATE FUNCTION sum_decimal(@Number1 Decimal(6,2), @Number2 Decimal(6,2))
RETURNS Decimal(6,2)
BEGIN
    DECLARE @Result Decimal(6,2)
    SET @Result = @Number1 + @Number2
    RETURN @Result
END;
 
-- STORED PROCEDURE
CREATE PROC getname
AS
 SELECT SOMENAME FROM newtable;
 
-- TABLEs for testing CONSTRAINTs
CREATE TABLE testconstraints (
  someid integer NOT NULL,
  somename character varying(10) NOT NULL,
  CONSTRAINT testconstraints_id_pk PRIMARY KEY (someid)
);
CREATE TABLE testconstraints2 (
  ext_id INT NOT NULL,
  modified DATETIME,
  uniquefield VARCHAR(10) NOT NULL,
  usraction INT NOT NULL,
  CONSTRAINT testconstraints_id_fk FOREIGN KEY (ext_id)
      REFERENCES testconstraints (someid)
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT unique_2_fields_idx UNIQUE (modified, usraction),
  CONSTRAINT uniquefld_idx UNIQUE (uniquefield)
)";

?>
