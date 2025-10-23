const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

module.exports = defaultConfig.map( ( config ) => {
	return config;
} );
