<?php
class User
{
	private $conn;
	private $table = 'USERS';

	public $id;
	public $username;
	public $password;
	public $access_token;

	public function __construct($db)
	{
		$this->conn = $db;
	}

	public function read_one()
	{
		$query = 'SELECT * FROM ' . $this->table . ' u 
				WHERE u.id = ?';

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->id);

		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		$this->id = $row['id'];
		$this->username = $row['username'];


		return $stmt;
	}

	public function sign_up()
	{
		$query = 'INSERT INTO ' . $this->table . '
			SET
			username = :username,
			password_hash = :password_hash';

		$stmt = $this->conn->prepare($query);

		$this->username = htmlspecialchars(strip_tags($this->username));
		$this->password = htmlspecialchars(strip_tags($this->password));

		$stmt->bindParam(':username', $this->username);
		$stmt->bindParam(':password_hash', 
			password_hash($this->password, PASSWORD_BCRYPT));

		if ($stmt->execute()) {
			return true;
		}

		printf("Error: %s.\n", $stmt->error);
		return false;
	}


}
