import { createRoot } from '@wordpress/element';
import { TechnologyOrderApp } from './components/technology-order-app';
import './style.scss';

function initTechnologyOrderApp() {
	const container = document.getElementById(
		'portfolio-technology-order-app'
	);

	if ( ! container ) {
		// eslint-disable-next-line no-console
		console.error(
			'Could not find #portfolio-technology-order-app container'
		);
		return;
	}

	const root = createRoot( container );
	root.render( <TechnologyOrderApp /> );
}

if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', initTechnologyOrderApp );
} else {
	initTechnologyOrderApp();
}
