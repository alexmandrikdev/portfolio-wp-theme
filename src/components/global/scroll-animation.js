/**
 * Scroll-triggered animation system
 * Uses Intersection Observer to detect when elements enter viewport
 */
class ScrollAnimation {
	constructor() {
		this.observer = null;
		this.init();
	}

	init() {
		const options = {
			root: null,
			rootMargin: '0px 0px -10% 0px', // Trigger when 10% of element is in viewport
			threshold: 0.1, // Trigger when at least 10% of element is visible
		};

		this.observer = new window.IntersectionObserver( ( entries ) => {
			entries.forEach( ( entry ) => {
				if ( entry.isIntersecting ) {
					entry.target.classList.add( 'scroll-fade--active' );

					this.observer.unobserve( entry.target );
				}
			} );
		}, options );

		document.querySelectorAll( '.scroll-fade' ).forEach( ( element ) => {
			this.observer.observe( element );
		} );
	}
}

export default ScrollAnimation;
