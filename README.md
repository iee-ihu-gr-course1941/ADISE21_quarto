A simple implementation of the quarto board game written in the standard php/mysql stack.

## API Spec

### Placements 

* POST /api/placements/create.php
** Query Params: session_id
** body:
*** id
*** access_token
*** pos_x
*** pos_y

* GET /api/placements/read.php
** Query Params: session_id
** body:
*** id
*** access_token

### Sessions

* POST /api/sessions/create.php
** body:
*** id
*** access_token

* DELETE /api/sessions/read.php
** Query Params: id
** body:
*** id
*** access_token

* PUT /api/sessions/join.php
** Query Params: id
** body:
*** id
*** access_token

* GET /api/sessions/read.php
** body:
*** id
*** access_token

* GET /api/sessions/read_one.php
** Query Params: id
** body:
*** id
*** access_token

* GET /api/sessions/remaining_pieces.php
** Query Params: id
** body:
*** id
*** access_token

* PUT /api/sessions/set_next.php
** Query Params: id
** body:
*** id
*** access_token
*** next_piece_id

### Users

* GET /api/users/login.php
** body:
*** username 
*** password 

* PUT /api/users/logout.php
** body:
*** id
*** access_token

* POST /api/users/sign_up.php
** body:
*** username 
*** password 

* GET /api/users/validate_token.php
** body:
*** id
*** access_token
