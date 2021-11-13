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

    public function is_available($session_id)
    {
        $query = 'SELECT pc1.id FROM '.$this->table.' pc1
                  WHERE pc1.id = :piece_id
                  AND pc1.id IN
                          (SELECT pc.id as pieces FROM PIECES as pc
                            LEFT JOIN PLACEMENTS as pl
                            ON    pl.session_id = :session_id
                            AND   pc.id         = pl.piece_id
                            WHERE pl.piece_id   IS NULL)';

        $stmt = $this->conn->prepare($query);

        $session_id = htmlspecialchars(strip_tags($session_id));
        $this->id   = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':session_id', $session_id);
        $stmt->bindParam(':piece_id', $this->id);
        $stmt->execute();

        $row_count = $stmt->rowCount();

        return $row_count === 1;
    }
}
