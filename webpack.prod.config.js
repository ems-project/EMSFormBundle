const path = require('path');
const webpack = require('webpack');
const ManifestPlugin = require('webpack-manifest-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');

module.exports = {
    mode: 'production',
    entry: {
        'form': './src/Resources/assets/js/form.js',
        'formDebug': './src/Resources/assets/js/formDebug.js',
        'debug': './src/Resources/assets/js/debug.js',
        'backend': './src/Resources/assets/js/backend.js',
        'dynamicFields': './src/Resources/assets/js/dynamicFields.js',
        'validation': './src/Resources/assets/js/validation.js'
    },
    plugins: [
        new ManifestPlugin({'publicPath': 'bundles/emsform/'}),
        new CleanWebpackPlugin({
            cleanOnceBeforeBuildPatterns: ['js/*', '!static/*'],
        }),
        new webpack.ProvidePlugin({
            Promise: 'core-js-pure/features/promise'
        })
    ],
    output: {
        filename: 'js/[name].js',
        path: path.resolve(__dirname, 'src/Resources/public')
    },
    module: {
        rules: [
            {
                test: /\.css$/,
                use: ['style-loader', 'css-loader']
            },
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
                        presets: [
                            [
                                '@babel/preset-env',
                                {
                                    'targets': {
                                        'browsers': [
                                            "> 1%",
                                            "last 2 versions",
                                            "IE 10"
                                        ],
                                    },
                                }
                            ]
                        ],
                        plugins: [
                            [
                                '@babel/plugin-transform-runtime',
                                {
                                    'corejs': 3
                                }
                            ],
                        ],
                    }
                }
            }
        ],
    }
};
