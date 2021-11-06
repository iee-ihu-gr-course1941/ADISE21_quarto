# Entities

## Session
* id: Int - pk - Autoinc
* player1-id: User
* player2-id: User
* player1-turn: Boolean
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
* CRUD: standard crud operations
* join: player2 joins game, game is now considered full and starts

## Piece
* Read: standard read

## Placement
* create: create triggers multiple actions:
** validate that there isnt another placement of the same session in the same pos-x pos-y
** set-turn: when a placement has been made, the other player gets their turn
** detect-win: after a placement, the board is validated for a winning state. If detected, 
	the player who made the placement is declared winner is set in the session entity

## User
* create: standard creation (password is hashed)
* read: standard read
* login: compare username and hashed password to stored ones


