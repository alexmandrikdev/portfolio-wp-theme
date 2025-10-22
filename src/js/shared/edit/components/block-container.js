import { useBlockProps } from '@wordpress/block-editor';

const BlockContainer = ( {
	children,
	style = {
		paddingLeft: '24px',
		paddingRight: '24px',
		marginBottom: '24px',
	},
} ) => {
	const blockProps = useBlockProps( { style } );

	return <div { ...blockProps }>{ children }</div>;
};

export default BlockContainer;
