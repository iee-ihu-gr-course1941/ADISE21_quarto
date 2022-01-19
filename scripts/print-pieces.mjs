#!/usr/bin/env zx

const asString = num => {
  const numStr = num.toString(2);
  switch (true) {
    case num < 2:
      return '000' + numStr;
    case num < 4:
      return '00' + numStr;
    case num < 9:
      return '0' + numStr;
    default:
      return numStr;
  }
};

console.log(chalk.blue('Remaining Pieces: \n'));
const inp = JSON.parse(process.argv[3])
  .map(
    piece =>
      `${piece.id} - ${piece.attr1}${piece.attr2}${piece.attr3}${piece.attr4}`
  )
  .forEach(piecestr => {
    console.log(piecestr);
  });
