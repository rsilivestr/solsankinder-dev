const path = require('path');
const CopyPlugin = require('copy-webpack-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const { ProvidePlugin } = require('webpack');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
  mode: 'production',
  entry: {
    app: './src/js/index.js',
  },
  output: {
    path: path.resolve(__dirname, 'dist'),
    filename: 'scripts/app.min.js',
  },
  plugins: [
    new CleanWebpackPlugin(),
    new CopyPlugin({
      patterns: [
        { from: 'static', to: path.resolve(__dirname, 'dist') },
        {
          from: 'src/php/**/*',
          to: path.resolve(__dirname, 'dist'),
          flatten: true,
        },
      ],
    }),
    new MiniCssExtractPlugin({
      filename: 'styles/styles.min.css',
      ignoreOrder: false,
    }),
  ],
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /(node_modules|bower_components)/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env'],
          },
        },
      },
      {
        test: /\.scss$/i,
        include: path.resolve(__dirname, 'src/scss'),
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
            options: {
              publicPath: path.resolve(__dirname, 'dist/styles'),
            },
          },
          {
            loader: 'css-loader',
            options: {
              url: false,
            },
          },
          {
            loader: 'postcss-loader',
            options: {
              ident: 'postcss',
              plugins: (loader) => [
                require('postcss-sort-media-queries')({
                  sort: 'mobile-first',
                }),
                require('postcss-preset-env')(),
              ],
            },
          },
          'sass-loader',
        ],
      },
    ],
  },
  resolve: {
    alias: {
      Scss: path.resolve(__dirname, 'src/scss/'),
      ScssBlocks: path.resolve(__dirname, 'src/scss/blocks'),
      ScssLayout: path.resolve(__dirname, 'src/scss/layout'),
      ScssMisc: path.resolve(__dirname, 'src/scss/misc'),
      ScssPages: path.resolve(__dirname, 'src/scss/pages'),
      ScssUtil: path.resolve(__dirname, 'src/scss/util'),
    },
  },
};
