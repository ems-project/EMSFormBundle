module.exports = function (api) {
    api.cache(true);

    const presets = [
        [
            '@babel/preset-env',
            {
                targets: {
                    node: 'current',
                },
            },
        ],
    ];
    const plugins = [
        [
            "@babel/plugin-transform-runtime",
            {
                "absoluteRuntime": false,
                "corejs": 2,
                "version": "^7.12.1"
            }
        ],
    ];

    return {
        presets,
        plugins
    };
}
