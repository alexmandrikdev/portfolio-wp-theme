import BlockCard from './block-card';

const BlockPlaceholder = ( { name } ) => {
	return (
		<BlockCard title={ name }>
			<div className="portfolio-block-placeholder">
				<p>
					{ `"${ name }" block - This content is dynamically generated and will display automatically on the frontend.` }
				</p>
			</div>
		</BlockCard>
	);
};

export default BlockPlaceholder;
