<?php

class Piece
{
    private $conn;
    private $table = 'PIECES';

    public $id;
    public $attr1;
    public $attr2;
    public $attr3;
    public $attr4;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function read()
    {
        $query = 'SELECT * FROM ' . $this->table;
        $stmt  = $this->conn->prepare($query);

        $stmt->execute();

        $pieces_array = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($pieces_array, $row);
        }
        return $pieces_array;
    }
}
