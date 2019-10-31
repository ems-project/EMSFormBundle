const path = require('path');
const ManifestPlugin = require('webpack-manifest-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');

module.exports = {
    mode: 'production',
    entry: {
        'form': './Resources/assets/js/form.js',
        'formDebug': './Resources/assets/js/formDebug.js',
        'backend': './Resources/assets/js/backend.js',
        'dynamicFields': './Resources/assets/js/dynamicFields.js',
        'validation': './Resources/assets/js/validation.js'
    },
    plugins: [
        new ManifestPlugin({'publicPath': 'bundles/emsform/'}),
        new CleanWebpackPlugin({
            cleanOnceBeforeBuildPatterns: ['js/*', '!static/*'],
        }),
    ],
    output: {
        filename: 'js/[name].[contenthash].js',
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
