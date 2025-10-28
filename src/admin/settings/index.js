import { createRoot } from '@wordpress/element';
import { SettingsApp } from './components/settings-app';

function initSettingsApp() {
	const container = document.getElementById( 'portfolio-settings-app' );

	if ( ! container ) {
		// eslint-disable-next-line no-console
		console.error( 'Could not find #portfolio-settings-app container' );
		return;
	}

	const root = createRoot( container );
	root.render( <SettingsApp /> );
}

if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', initSettingsApp );
} else {
	initSettingsApp();
}
