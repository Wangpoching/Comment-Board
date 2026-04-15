const path = require('path')

module.exports = {
  entry: './src/index.js',
  output: {
    filename: 'comment-board.js',
    path: path.resolve(__dirname, 'dist'),
    library: {
      name: 'CommentBoard',
      type: 'umd',
    },
    globalObject: 'this',
  },
  mode: 'production'
}