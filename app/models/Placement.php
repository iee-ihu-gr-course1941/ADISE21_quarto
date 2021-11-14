<?php

class Placement
{
    private $conn;
    private $table = 'PLACEMENTS';

    public $id;
    public $session_id;
    public $player_id;
    public $piece_id;
    public $pos_x;
    public $pos_y;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function read()
    {
        $query = 'SELECT * FROM ' . $this->table. '
                          WHERE session_id = ?';
        $stmt  = $this->conn->prepare($query);

        $this->session_id = htmlspecialchars(strip_tags($this->session_id));

        $stmt->bindParam(1, $this->session_id);
        $stmt->execute();

        $placements_array = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($placements_array, $row);
        }
        return $placements_array;
    }

    public function create()
    {
        $query = 'INSERT INTO ' . $this->table . '
                    SET session_id = :session_id,
                        player_id  = :player_id,
                        piece_id   = :piece_id,
                        pos_x      = :pos_x,
                        pos_y      = :pos_y';

        $stmt = $this->conn->prepare($query);

        $this->session_id = htmlspecialchars(strip_tags($this->session_id));
        $this->piece_id   = htmlspecialchars(strip_tags($this->piece_id));
        $this->player_id  = htmlspecialchars(strip_tags($this->player_id));
        $this->pos_x      = htmlspecialchars(strip_tags($this->pos_x));
        $this->pos_y      = htmlspecialchars(strip_tags($this->pos_y));

        $stmt->bindParam(':session_id', $this->session_id);
        $stmt->bindParam(':piece_id', $this->piece_id);
        $stmt->bindParam(':player_id', $this->player_id);
        $stmt->bindParam(':pos_x', $this->pos_x);
        $stmt->bindParam(':pos_y', $this->pos_y);

        $stmt->execute();

        $affected_rows = $stmt->rowCount();
        return $affected_rows > 0;
    }

    public function is_valid()
    {
        $query = 'SELECT * FROM ' . $this->table. '
                          WHERE session_id = :session_id
                          AND   pos_x      = :pos_x
                          AND   pos_y      = :pos_y';

        $stmt  = $this->conn->prepare($query);

        $this->session_id = htmlspecialchars(strip_tags($this->session_id));
        $this->pos_x      = htmlspecialchars(strip_tags($this->pos_x));
        $this->pos_y      = htmlspecialchars(strip_tags($this->pos_y));

        $stmt->bindParam(':session_id', $this->session_id);
        $stmt->bindParam(':pos_x', $this->pos_x);
        $stmt->bindParam(':pos_y', $this->pos_y);
        $stmt->execute();

        $affected_rows = $stmt->rowCount();
        return !($affected_rows > 0);
    }
}
