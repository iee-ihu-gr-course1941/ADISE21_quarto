CREATE TABLE USER(
  id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(30) NOT NULL,
  password_hash VARCHAR(30) NOT NULL,
  access_token VARCHAR(30)
)

CREATE TABLE SESSION(
  id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  player1_id INT(6) UNSIGNED NOT NULL,
  player2_id INT(6) UNSIGNED,
  turn ENUM('p1', 'p2') NOT NULL,
  winner ENUM('p1', 'p2', 'none') NOT NULL
)