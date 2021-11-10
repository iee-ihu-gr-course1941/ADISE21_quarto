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
		$query = 'SELECT u.id, u.username FROM ' . $this->table . ' u 
				WHERE u.id = ?';

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->id);

		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		$this->id 	= $row['id'];
		$this->username = $row['username'];
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
			password_hash($this->password, PASSWORD_DEFAULT));
		
		try{
			$stmt->execute();
			return true;
		}catch(PDOException $e){
			return false;
		}

	}

	public function set_token()
	{
		$query = 'UPDATE ' . $this->table . '
				SET	access_token = :access_token
				WHERE	id = :id';

		$stmt = $this->conn->prepare($query);

		$chars 		    = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$this->access_token = substr(str_shuffle(str_repeat($chars, ceil(30/strlen($chars)) )),1,30);

		$this->id = htmlspecialchars(strip_tags($this->id));

		$stmt->bindParam(':access_token', $this->access_token);
		$stmt->bindParam(':id', $this->id);

		try{
			$stmt->execute();
			return true; 
		}catch(PDOException $e){
			return false;
		}

	}

	public function authenticate()
	{
		$query = 'SELECT id, password_hash FROM ' . $this->table . '
				WHERE 	username = :username';

		$stmt = $this->conn->prepare($query);

		$this->username = htmlspecialchars(strip_tags($this->username));
		$this->password = htmlspecialchars(strip_tags($this->password));
		
		$stmt->bindParam(':username', $this->username);
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		$fetched_pass = $row['password_hash'];	
		$this->id = $row['id'];

		return password_verify($this->password, $fetched_pass);
	}

	public function validate_token()
	{
		$query = 'SELECT access_token FROM ' . $this->table . '
				WHERE id = :id';
			
		$stmt = $this->conn->prepare($query);

		$this->access_token = htmlspecialchars(strip_tags($this->access_token));
		$this->id 	    = htmlspecialchars(strip_tags($this->id));

		$stmt->bindParam(':id', $this->id);
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		$fetched_token = $row['access_token'];

		return $this->access_token === $fetched_token;
	}
}
