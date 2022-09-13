<?php
class Community extends BaseClass {
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function get_all() {
        $this->stmt = $this->prepare_stmt("CALL selCommunity('{$this->index}');");
        $this->execute();

        return $this->stmt;
    }
}