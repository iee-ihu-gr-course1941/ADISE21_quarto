#!/usr/bin/env zx
const ar = [
  ['----', '----', '----', '----'],
  ['----', '----', '----', '----'],
  ['----', '----', '----', '----'],
  ['----', '----', '----', '----'],
];
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

process.stdout.write('\n\n');
JSON.parse(process.argv[3])
  .reduce((acc, current) => {
    acc[current['pos_x']][current['pos_y']] = parseInt(current.piece_id);
    return [...acc];
  }, ar)
  .map(pieces => pieces.map(asString))
  .forEach((pieces_ids, index) => {
    process.stdout.write(chalk.blue(index + ' - '));
    pieces_ids.forEach((piece_id, index) => {
      process.stdout.write(chalk.red(index + ' '));
      process.stdout.write(piece_id + '   ');
    });
    process.stdout.write('\n\n');
  });
