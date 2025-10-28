import { Card, CardBody, CardHeader } from '@wordpress/components';
import BlockContainer from './block-container';

const BlockPlaceholder = ( { name } ) => {
	return (
		<BlockContainer>
			<Card style={ { width: '100%' } }>
				<CardHeader>
					<h4>{ name }</h4>
				</CardHeader>
				<CardBody>
					<div className="portfolio-block-placeholder">
						<p>
							{ `"${ name }" block - This content is dynamically generated and will display automatically on the frontend.` }
						</p>
					</div>
				</CardBody>
			</Card>
		</BlockContainer>
	);
};

export default BlockPlaceholder;
