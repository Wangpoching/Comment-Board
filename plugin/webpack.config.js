const path = require('path');
const dotenv = require('dotenv');
const webpack = require('webpack');

module.exports = (env, argv) => {
  const mode = argv.mode || 'development';

  // 載入對應 env 檔
  dotenv.config({
    path: `.env.${mode}`
  });

  // fallback（避免沒抓到）
  dotenv.config();

  return {
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
    mode: mode,
    plugins: [
      new webpack.DefinePlugin({
        'process.env.BOARD_URL': JSON.stringify(process.env.BOARD_URL)
      })
    ]
  };
};