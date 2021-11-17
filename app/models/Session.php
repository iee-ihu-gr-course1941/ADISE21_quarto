<?php

class Session
{
    private $conn;
    private $table = 'SESSIONS';

    public $id;
    public $player1_id;
    public $player2_id;
    public $turn;
    public $winner;
    public $next_piece_id;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public static function is_playing($player_id, $session)
    {
        $query = 'SELECT count(id) as num FROM '. $session->table . '
                            WHERE (player1_id = :player_id
                              OR    player2_id = :player_id)
                            AND id = :session_id';

        $stmt = $session->conn->prepare($query);

        $player_id = htmlspecialchars(strip_tags($player_id));
        $session_id = htmlspecialchars(strip_tags($session->id));

        $stmt->bindParam(':player_id', $player_id);
        $stmt->bindParam(':session_id', $session_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $num = $row['num'];

        return $num > 0;
    }

    public function is_turn($player_id)
    {
        $query = 'SELECT count(id) as num FROM '. $this->table . '
                          WHERE ((turn = \'p1\' AND player1_id = :player_id)
                          OR    (turn = \'p2\'  AND player2_id = :player_id))
                          AND   id = :id';

        $stmt = $this->conn->prepare($query);

        $player_id = htmlspecialchars(strip_tags($player_id));
        $this->id  = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':player_id', $player_id);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $num = $row['num'];

        return $num > 0;
    }

    public function read()
    {
        $query = 'SELECT * FROM ' . $this->table;
        $stmt  = $this->conn->prepare($query);

        $stmt->execute();

        $placements_array = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($placements_array, $row);
        }
        return $placements_array;
    }

    public function read_one()
    {
        $query = 'SELECT * FROM ' . $this->table . ' u 
				WHERE u.id = ?';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        $result = $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->id 	     = $row['id'];
        $this->player1_id    = $row['player1_id'];
        $this->player2_id    = $row['player2_id'];
        $this->turn          = $row['turn'];
        $this->winner        = $row['winner'];
        $this->next_piece_id = $row['next_piece'];

        return $result;
    }


    public function create()
    {
        $query = 'INSERT INTO ' . $this->table . '
				SET
				player1_id = :player1_id';

        $stmt = $this->conn->prepare($query);

        $this->player1_id = htmlspecialchars(strip_tags($this->player1_id));

        $stmt->bindParam(':player1_id', $this->player1_id);
        $stmt->execute();

        $affected_rows = $stmt->rowCount();
        return $affected_rows > 0;
    }

    public function set_turn()
    {
        $query = 'UPDATE ' . $this->table . '
                                SET turn = CASE 
                                                WHEN turn = \'p1\' THEN \'p2\'
                                                WHEN turn = \'p2\' THEN \'p1\'
                                           END
                                WHERE id = ?';

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(1, $this->id);

        $result = $stmt->execute();
        return $result;
    }

    public function join()
    {
        $query = 'UPDATE ' . $this->table . ' s1
                                SET   s1.player2_id = :player2_id  
                                WHERE s1.id         = :id 
                                AND   s1.player2_id IS NULL';

        $stmt = $this->conn->prepare($query);

        $this->id         = htmlspecialchars(strip_tags($this->id));
        $this->player2_id = htmlspecialchars(strip_tags($this->player2_id));

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':player2_id', $this->player2_id);

        $result        = $stmt->execute();
        $affected_rows = $stmt->rowCount();

        $result = $affected_rows > 0;

        return $result;
    }

    public function end_game()
    {
        $query = 'DELETE FROM ' . $this->table . '
                                    WHERE id = ?';

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(1, $this->id);

        $result = $stmt->execute();
        return $result;
    }

    public function set_next()
    {
        $query = 'UPDATE ' . $this->table . '
                                SET   next_piece    = :next_piece 
                                WHERE id            = :id
                                AND   next_piece    is NULL';

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->next_piece = htmlspecialchars(strip_tags($this->next_piece_id));

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':next_piece', $this->next_piece_id);

        $result        = $stmt->execute();
        $affected_rows = $stmt->rowCount();

        return $result && $affected_rows > 0;
    }

    public function set_next_null()
    {
        $query = 'UPDATE ' . $this->table . '
                                SET   next_piece = NULL 
                                WHERE id         = :id';

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':id', $this->id);

        $result        = $stmt->execute();
        $affected_rows = $stmt->rowCount();

        return $result && $affected_rows > 0;
    }

    public function is_next_null()
    {
        $query = 'SELECT count(*) as num FROM ' . $this->table . '
                                WHERE id          = :id
                                AND   next_piece is NULL';

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':id', $this->id);

        $result        = $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $num = $row['num'];

        return $result && $num > 0;
    }

    public function has_won()
    {
        $query = '
        SELECT count(*) as num 
        FROM PLACEMENTS as p1 
        JOIN PLACEMENTS as p2 
        JOIN PLACEMENTS as p3 
        JOIN PLACEMENTS as p4 
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
    	      AND ((pc1.attr1 = pc2.attr1 = pc3.attr1 = pc4.attr1) 
                OR (pc1.attr2 = pc2.attr2 = pc3.attr2 = pc4.attr2)
                OR (pc1.attr3 = pc2.attr3 = pc3.attr3 = pc4.attr3) 
                OR (pc1.attr4 = pc2.attr4 = pc3.attr4 = pc4.attr4))';

        $stmt  = $this->conn->prepare($query);

        $this->session_id = htmlspecialchars(strip_tags($this->session_id));

        $stmt->bindParam(':session_id', $this->session_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $num = $row['num'];

        return $num > 0;
    }

    public function set_winner($winner_id)
    {
        $query = 'UPDATE ' . $this->table . ' s1
                                SET   s1.winner = :winner
                                WHERE s1.id     = :id';

        $stmt = $this->conn->prepare($query);

        $this->id  = htmlspecialchars(strip_tags($this->id));
        $winner_id = htmlspecialchars(strip_tags($winner_id));

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':winner', $winner_id);

        $stmt->execute();
        return $stmt;
    }

    public function remaining_pieces()
    {
        $query = 'SELECT * FROM PIECES as pc 
                  WHERE pc.id NOT IN (
	              SELECT pl.piece_id FROM PLACEMENTS as pl
                      WHERE  pl.session_id = :session_id)';


        $stmt = $this->conn->prepare($query);

        $this->id  = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':session_id', $this->id);

        $stmt->execute();
        $remaining_pieces_arr = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($remaining_pieces_arr, $row);
        }
        return $remaining_pieces_arr;
    }

    public function is_piece_available()
    {
        $query = 'SELECT count(*) as num 
                  FROM PIECES as pc 
                  WHERE pc.id = :piece_id
                  AND   pc.id NOT IN (
	            SELECT pl.piece_id FROM PLACEMENTS as pl
                    WHERE  pl.session_id = :session_id )';


        $stmt = $this->conn->prepare($query);

        $this->id            = htmlspecialchars(strip_tags($this->id));
        $this->next_piece_id = htmlspecialchars(strip_tags($this->next_piece_id));

        $stmt->bindParam(':session_id', $this->id);
        $stmt->bindParam(':piece_id', $this->next_piece_id);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $num = $row['num'];

        return $num > 0;
    }
}
