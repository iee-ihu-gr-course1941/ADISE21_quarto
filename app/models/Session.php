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
                            WHERE player1_id = :player_id
                            OR    player2_id = :player_id';

        $stmt = $session->conn->prepare($query);

        $player_id = htmlspecialchars(strip_tags($player_id));

        $stmt->bindParam(':player_id', $player_id);
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
        $affected_rows = $stmt->rowCount();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $num = $row['num'];

        return $result && $num > 0;
    }
}
