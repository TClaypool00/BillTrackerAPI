<?php
class Suggestion extends BaseClass {
    public function __construct($db)
    {
        $this->conn = $db;
    }
}