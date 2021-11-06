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


