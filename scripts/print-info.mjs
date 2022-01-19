#!/usr/bin/env zx
const ob = {
  id: '9',
  player1_id: '9',
  player2_id: '13',
  turn: 'p2',
  winner: null,
  next_piece: null,
};

const inp = JSON.parse(process.argv[3]);
console.log(
  `id - ${inp.id} | player1_id - ${inp.player1_id} | player2_id - ${inp.player2_id} \nturn - ${inp.turn} | winner - ${inp.winner} | next_piece - ${inp.next_piece_id} `
);
