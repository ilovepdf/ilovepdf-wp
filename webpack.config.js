const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

module.exports = {
	...defaultConfig,
	entry: './dev/js/main.js',
	output: {
		path: __dirname + '/assets/build',
		filename: 'main.min.js',
	},
	module: {
		...defaultConfig.module,
		rules: [
		...defaultConfig.module.rules,
		{
			test: /\.(png|jpe?g|gif|svg)$/i,
			type: 'asset/resource',
			generator: {
				filename: '../img/[name][ext]',
			},
		},
		],
	},
};