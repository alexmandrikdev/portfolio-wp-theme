const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );

const customEntries = {
	'components/global/index': path.resolve(
		process.cwd(),
		'src',
		'components',
		'global',
		'script.js'
	),
};

module.exports = defaultConfig.map( ( config ) => {
	return {
		...config,
		entry: async () => {
			const originalEntryPoints = await config.entry();

			return {
				...originalEntryPoints,
				...customEntries,
			};
		},
	};
} );
