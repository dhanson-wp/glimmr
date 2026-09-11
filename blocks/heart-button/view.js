import { getContext, store } from '@wordpress/interactivity';

const likedKey = ( postId ) => `glimmr:liked:${ postId }`;

const getStoredLike = ( postId ) => {
	try {
		return window.localStorage.getItem( likedKey( postId ) ) === '1';
	} catch ( error ) {
		return false;
	}
};

const setStoredLike = ( postId, liked ) => {
	try {
		window.localStorage.setItem( likedKey( postId ), liked ? '1' : '0' );
	} catch ( error ) {}
};

const syncCount = ( context ) => {
	const baseCount = Number.parseInt( context.baseCount || 0, 10 );
	context.count = Math.max( 0, baseCount + ( context.liked ? 1 : 0 ) );
};

store( 'glimmr/heart', {
	state: {
		get ariaLabel() {
			return getContext().liked ? 'Unlike this photo' : 'Like this photo';
		},
		get countText() {
			return new Intl.NumberFormat().format( getContext().count || 0 );
		},
		get label() {
			return getContext().liked ? 'Liked' : 'Like';
		},
	},
	actions: {
		toggle() {
			const context = getContext();
			context.liked = ! context.liked;
			syncCount( context );
			setStoredLike( context.postId, context.liked );
		},
	},
	callbacks: {
		init() {
			const context = getContext();
			context.liked = getStoredLike( context.postId );
			context.ready = true;
			syncCount( context );
		},
	},
} );
