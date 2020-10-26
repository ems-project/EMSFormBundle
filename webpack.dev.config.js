const path = require('path');
const ManifestPlugin = require('webpack-manifest-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');

module.exports = {
    mode: 'development',
    entry: {
        'form': './Resources/assets/js/form.js',
        'formDebug': './Resources/assets/js/formDebug.js',
        'debug': './Resources/assets/js/debug.js',
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
        filename: 'js/[name].js',
        path: path.resolve(__dirname, 'Resources/public')
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
                    loader: 'babel-loader'
                }
            }
        ],
    }
};
