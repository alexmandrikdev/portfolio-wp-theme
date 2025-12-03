const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );
const glob = require( 'glob' );
const CopyPlugin = require( 'copy-webpack-plugin' );

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

const getAdminEntries = () => {
	const entryFiles = glob.sync(
		path.resolve( process.cwd(), 'src/admin/**/index.js' )
	);

	const entries = {};

	entryFiles.forEach( ( filePath ) => {
		const adminPath = path
			.relative( path.resolve( process.cwd(), 'src' ), filePath )
			.replace( /\.js$/, '' );

		entries[ adminPath ] = filePath;
	} );

	return entries;
};

const componentEntries = getComponentEntries();
const adminEntries = getAdminEntries();

module.exports = defaultConfig.map( ( config, index ) => {
	return {
		...config,
		entry: async () => {
			const originalEntryPoints = await config.entry();

			let entryPoints = {
				...originalEntryPoints,
				...componentEntries,
			};

			if ( index === 0 ) {
				entryPoints = {
					...entryPoints,
					...adminEntries,
				};
			}

			return entryPoints;
		},
		plugins: [
			...( config.plugins || [] ),
			new CopyPlugin( {
				patterns: [
					{
						from: 'src/components/project-card/render.php',
						to: 'components/project-card/render.php',
					},
				],
			} ),
		],
	};
} );
