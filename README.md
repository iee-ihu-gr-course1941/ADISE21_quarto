A simple implementation of the quarto board game written in the standard php/mysql stack.

## Demo
You can test the api by sending requests to `https://users.it.teithe.gr/~it185291/api`

## Running locally
The recommended way to run locally is using via docker. The following command mounts your code in a container running xampp and serves it under the www directory. You can find more info on how to use the container [here](https://hub.docker.com/r/tomsik68/xampp/)
```
docker run --name myXampp -p <SSH-PORT>:22 -p <HTTP-PORT>:80 -d -v <PATH-TO-CODE>:/www tomsik68/xampp:8
```

## API Spec

### Placements 

* POST /api/placements/create.php
  * Query Params: session_id
  * body:
    * id
    * access_token
    * pos_x
    * pos_y

* GET /api/placements/read.php
  * Query Params: session_id
  * body:
    * id
    * access_token

### Sessions

* POST /api/sessions/create.php
  * body:
    * id
    * access_token

* DELETE /api/sessions/end_game.php
  * Query Params: session_id
  * body:
    * id
    * access_token

* PUT /api/sessions/join.php
  * Query Params: session_id
  * body:
    * id
    * access_token

* GET /api/sessions/read.php
  * body:
    * id
    * access_token

* GET /api/sessions/read_one.php
  * Query Params: session_id
  * body:
    * id
    * access_token

* GET /api/sessions/remaining_pieces.php
  * Query Params: session_id
  * body:
    * id
    * access_token

* PUT /api/sessions/set_next.php
  * Query Params: session_id
  * body:
    * id
    * access_token
    * next_piece_id

### Users

* GET /api/users/login.php
  * body:
    * username 
    * password 

* PUT /api/users/logout.php
  * body:
    * id
    * access_token

* POST /api/users/sign_up.php
  * body:
    * username 
    * password 

* GET /api/users/validate_token.php
  * body:
    * id
    * access_token

## Playing the game with curl

### Creating and joining sessions
* Sing up
```
curl -f -d '{"username":"Stef","password":"123123"}' \
  -H "Content-Type: application/json" \
  -X POST https://users.it.teithe.gr/\~it185291/api/users/sign_up.php | jq . 
```

* Sign in and export the credentials to use in following requests. After the request, the variables `id`, `$access_token` will be set to your credentials. You will use these istead of username and password for following requests that require authentication.
```
$(curl -f -d '{"username":"Stef","password":"123123"}' \
  -H "Content-Type: application/json" \
  -X GET https://users.it.teithe.gr/\~it185291/api/users/login.php | jq -r 'keys[] as $k | "export \($k)=\(.[$k])"')
```

* if you want to start a new session, create it and wait for someone to join
```
curl -f -d "{\"id\":\"$id\",\"access_token\":\"$access_token\"}" \
  -H "Content-Type: application/json" \
  -X POST https://users.it.teithe.gr/\~it185291/api/sessions/create.php | jq .
```

* Alternatively, you can browse running sessions.
```
curl -f -d "{\"id\":\"$id\",\"access_token\":\"$access_token\"}" \
  -H "Content-Type: application/json" \
  -X GET https://users.it.teithe.gr/\~it185291/api/sessions/read.php | jq .
```

* You can join sessions where the player2 spot is empty. Here we are joining the session with id 8
```
curl -f -d "{\"id\":\"$id\",\"access_token\":\"$access_token\"}" \
  -H "Content-Type: application/json" \
  -X PUT https://users.it.teithe.gr/\~it185291/api/sessions/join.php\?session_id\=8 | jq .
```

### Playing quarto
Quarto's rules are as follows
1. When 2 players have joined the session, the game can start
2. Player 2 chooses the pawn that player 1 will place
3. Player 1 places that pawn
4. Player 1 chooses the paun that player 2 will place
5. Player 2 places that pawn
6. Repeat steps 2 through 5 until a player wins or theres a deadlock
  * A player wins when they place a pawn that forms a set of 4 that share one of their four, either vertically, horizontally or diagonally
  * A deadlock happens when all pawns are placed but no player has won

* Seeing available pieces
```
curl -f -d "{\"id\":\"$id\",\"access_token\":\"$access_token\"}" \
  -H "Content-Type: application/json" \
  -X GET https://users.it.teithe.gr/\~it185291/api/sessions/remaining_pieces.php\?session_id\=8 | jq .
```

* Choosing a pawn for the other player
```
curl -f -d "{\"id\":\"$id\",\"access_token\":\"$access_token\",\"next_piece_id\":13}" \
  -H "Content-Type: application/json" \
  -X PUT https://users.it.teithe.gr/\~it185291/api/sessions/set_next.php\?session_id\=8 | jq .
```

* Place the pawn that has been chosen for you. You need to specify the x and y axis for the placement
```
curl  -f -d "{\"id\":\"$id\",\"access_token\":\"$access_token\",\"pos_x\":0,\"pos_y\":0}" \
  -H "Content-Type: application/json" \
  -X POST https://users.it.teithe.gr/\~it185291/api/placements/create.php\?session_id\=8 | jq .
```

* At any point, you can view the availabe remaining pieces, the created placements and the session info to have a complete overview of the game's current state
```
curl -f -d "{\"id\":\"$id\",\"access_token\":\"$access_token\"}" \
  -H "Content-Type: application/json" \
  -X GET https://users.it.teithe.gr/\~it185291/api/sessions/remaining_pieces.php\?session_id\=8 | jq .

curl -f -d "{\"id\":\"$id\",\"access_token\":\"$access_token\"}" \
  -H "Content-Type: application/json" \
  -X GET https://users.it.teithe.gr/\~it185291/api/placements/read.php\?session_id\=8 | jq .

curl -f -d "{\"id\":\"$id\",\"access_token\":\"$access_token\"}" \
  -H "Content-Type: application/json" \
  -X GET https://users.it.teithe.gr/\~it185291/api/sessions/read_one.php\?session_id\=8 | jq .
```

* The game is automatically terminated when a player wins or there's a deadlock (it informs you when this has happened), but you can also manually terminate a session before that.
```
curl -f -d "{\"id\":\"$id\",\"access_token\":\"$access_token\"}" \
  -H "Content-Type: application/json" \
  -X DELETE https://users.it.teithe.gr/\~it185291/api/sessions/end_game.php\?session_id\=8 | jq .
```

## Entities

**Piece**
* id: integer
* attr1: 0 | 1
* attr2: 0 | 1
* attr3: 0 | 1
* attr4: 0 | 1

**Placement**
* id: integer
* session_id: integer
* player_id: integer
* piece_id: integer
* pos_x: integer - 0 .. 3
* pos_y: integer - 0 .. 3

**Session**
* id: integer
* player1_id: integer
* player2_id: integer
* turn: 'p1' | 'p2'
* winner: 'p1' | 'p2'
* next_piece_id: integer - 0 .. 15

**User**
* id: integer
* username: string
* password: string
* access_token: string

**Note:** there is no explicit "board" entity. A session's board's state can be obtained via the `/placements/read.php` endpoint with the `session_id` query parameter set to the session id of interest.
