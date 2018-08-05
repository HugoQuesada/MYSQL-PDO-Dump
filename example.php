<?php
$dump = new MysqlPdoDump();
$dump->setBdd(new PDO('mysql:host=<host>;dbname=<db_name>;charset=<charset>', '<user>', '<password>'));
//1 for struct, 2 for data, 3 for all
$dump->dumpBdd(3);
