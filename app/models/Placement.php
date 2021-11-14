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

    public function check_win()
    {
        $query = '
        SELECT count(*) as num FROM '. $this->table .'as p1 
        JOIN '. $this->table .' as p2 
        JOIN '. $this->table .' as p3 
        JOIN '. $this->table .' as p4 
        INNER JOIN PIECES as pc1 on pc1.id = p1.piece_id 
        INNER JOIN PIECES as pc2 on pc2.id = p2.piece_id 
        INNER JOIN PIECES as pc3 on pc3.id = p3.piece_id 
        INNER JOIN PIECES as pc4 on pc4.id = p4.piece_id
	  WHERE p1.piece_id <> p2.piece_id 
    	  AND   p2.piece_id <> p3.piece_id 
    	  AND   p3.piece_id <> p4.piece_id
    	  AND   p1.piece_id <> p3.piece_id
    	  AND   p2.piece_id <> p4.piece_id
          AND   p1.piece_id <> p4.piece_id
          AND   p1.session_id = :session_id
          AND   p2.session_id = :session_id
          AND   p3.session_id = :session_id
          AND   p4.session_id = :session_id
	  AND ((p1.pos_x = p2.pos_x AND p2.pos_x = p3.pos_x AND p3.pos_x = p4.pos_x) 
            OR(p1.pos_y = p2.pos_y AND p2.pos_y = p3.pos_y AND p3.pos_y = p4.pos_y)
            OR((p1.pos_x = p1.pos_y AND p1.pos_y + 1 = p2.pos_y)
              AND (p2.pos_x = p2.pos_y AND p2.pos_y + 1 = p3.pos_y)
              AND (p3.pos_x = p3.pos_y AND p3.pos_y + 1 = p4.pos_y)
              AND p4.pos_x = p4.pos_y)
            OR ((p1.pos_x + p1.pos_y = 3 AND p1.pos_y + 1 = p2.pos_y AND p1.pos_x - 1 = p2.pos_x) 
              AND (p2.pos_x + p2.pos_y = 3 AND p2.pos_y + 1 = p3.pos_y AND p2.pos_x - 1 = p3.pos_x)
              AND (p3.pos_x + p3.pos_y = 3 AND p3.pos_y + 1 = p4.pos_y AND p3.pos_x - 1 = p4.pos_x)
              AND (p4.pos_x + p4.pos_y = 3)))
    	      AND ((pc1.attr1 = pc2.attr1 AND pc2.attr1 = pc3.attr1 AND pc3.attr1 = pc4.attr1) 
                OR (pc1.attr2 = pc2.attr2 AND pc2.attr2 = pc3.attr2 AND pc3.attr2 = pc4.attr2)
                OR (pc1.attr3 = pc2.attr3 AND pc2.attr3 = pc3.attr3 AND pc3.attr3 = pc4.attr3) 
                OR (pc1.attr4 = pc2.attr4 AND pc2.attr4 = pc3.attr4 AND pc3.attr4 = pc4.attr4))';

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
