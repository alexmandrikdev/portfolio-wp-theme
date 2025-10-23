const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );
const glob = require( 'glob' );

const getComponentEntries = () => {
	const entryFiles = glob.sync(
		path.resolve( process.cwd(), 'src/components/**/index.js' )
	);

	const entries = {};

	entryFiles.forEach( ( filePath ) => {
		const componentPath = path
			.relative( path.resolve( process.cwd(), 'src' ), filePath )
			.replace( /\.js$/, '' );

		entries[ componentPath ] = filePath;
	} );

	return entries;
};

module.exports = defaultConfig.map( ( config ) => {
	return {
		...config,
		entry: async () => {
			const originalEntryPoints = await config.entry();
			const componentEntries = getComponentEntries();

			return {
				...originalEntryPoints,
				...componentEntries,
			};
		},
	};
} );
