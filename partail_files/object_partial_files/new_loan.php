<?php
include_once '../../models/BaseClass.php';
include_once '../../models/Loan.php';
include_once '../../config/Database.php';

$database = new Database();
$db = $database->connect();

$loan = new Loan($db);