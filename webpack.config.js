const { resolve } = require('path');
const CopyPlugin = require('copy-webpack-plugin');
const CompressionPlugin = require('compression-webpack-plugin');

module.exports = (env, { mode }) => {
    const plugins = [
        new CopyPlugin([
            {
                from: resolve(require.resolve('@plesk/ui-library'), '../dist'),
                ignore: [
                    '**/*.js*',
                ],
            },
        ]),
        new CompressionPlugin({
            test: /\.(js|css|svg)$/,
            threshold: 8192,
        }),
    ];

    return {
        entry: { index: './frontend/main.js'},
        output: {
            filename: '[name].js',
            path: resolve(__dirname, 'httpdocs/ui-library'),
            publicPath: '/ui-library/',
        },
        devServer: {
            proxy: {
                '/': 'http://94.250.248.47/',
            },
        },
        module: {
            rules: [
                {
                    test: /\.js?$/,
                    include: [
                        resolve(__dirname, 'frontend'),
                    ],
                    use: 'babel-loader',
                },
            ],
        },
        plugins,
    };
};
