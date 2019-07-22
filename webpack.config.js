const path = require('path');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');

module.exports = {
    mode: 'production',
    entry: {
        'form': './Resources/assets/js/form.js',
        'backend': './Resources/assets/js/backend.js'
    },
    plugins: [
        new CleanWebpackPlugin(),
    ],
    output: {
        filename: 'js/[name].js',
        path: path.resolve(__dirname, 'Resources/public')
    },
    module: {
        rules: [
            {
                enforce: 'pre',
                test: /\.js$/,
                exclude: [/node_modules/],
                loader: 'eslint-loader',
            },
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env']
                    }
                }
            }
        ],
    }
};