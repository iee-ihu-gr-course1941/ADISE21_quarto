# Entities

## Session
* id: Int - pk - Autoinc
* player1-id: User
* player2-id: User
* player1-turn: User 
* winner: User

## Piece
* id: Int - pk - Autoinc
* attr1: Boolean
* attr2: Boolean
* attr3: Boolean
* attr4: Boolean

## Placement
* id: Int - pk - Autoinc
* session-id: Session 
* player-id: User 
* piece-id: Piece
* pos-x: Integer
* pos-y: Integer

## User
* id: Int - pk - Autoinc
* username: String
* password-hash: String
* access-token: String

# Api spec

## Session
* create: create session.
* read: return all active and waiting sessions.
* read-one: return info of session (used along with placements for the games state)
* join: validate game isnt full, player2 joins game, game is now considered full and starts
* remaining-pieces: returns remaing pieces by comparing the placements of the session to the absolute pieces

## Placement
* create: create placement
* read: get all placements of current session (used to determine game state along with session)

## User
* create: standard creation (password is hashed)
* read: standard read
* login: compare username and hashed password to stored ones, returns access token valid for 2 hours

# Service Spec

## Session
* create: create session.
* read: return all active and waiting sessions.
* read-one: return info of session (used along with placements for the games state)
* join: validate game isnt full ,player2 joins game, game is now considered full and starts
* validate-placement: validate that there isnt another placement of the same session in the same pos-x pos-y and that its the players turn to play
* set-turn: set the turn of other player
* detect-win: validate board for winning state
* remaining-pieces: returns remaing pieces by comparing the placements of the session to the absolute pieces

## Piece
* read: standard read

## Placement
* read: get all placements of current session (used to determine game state along with session)
* create: create triggers multiple actions:
** validate that there isnt another placement of the same session in the same pos-x pos-y
** detect-win: after a placement, the board is validated for a winning state. If detected, 
	the player who made the placement is declared winner is set in the session entity
** set-turn: when a placement has been made, the other player gets their turn

## User
* create: standard creation (password is hashed)
* read: standard read
* login: compare username and hashed password to stored ones, returns access token valid for 2 hours
* validate-request: compare request access token with valid persisted token

# Technical Description

## User management

### User creation
* username is stored as is
* password is hashed and stored

### User login
* inputed username is compared to stored username
* if username is found then input password is hashed and compared to stored hashed

### Subsequent Requests
* for a request to be considered authorized, it needs the access token to be present and valid
* an access token is validated by comparing it to the stored one, username, id, timestamp isnt over 2 hours old.

## Session
* player1 initiates session, session is frozen until player2 is present.
* player2 browses available games (a game is available when player2 isnt present).
* player2 joins session, session starts.
* player1 makes a move, a move is a placement on the board, the board is then validated for a winning state.
* when winning state is detected, the user who made the last placement within a session is declared as winner of the session.
* Session is considered fulfilled when winner is present.
* session entities are preserved and displayed as history

## Terms
* Winning state: when all pieces in the same x pos, or y pos, or in the diagonal share an attr. 
* Diagonal positions: x = y or x + y = max x = max y.


