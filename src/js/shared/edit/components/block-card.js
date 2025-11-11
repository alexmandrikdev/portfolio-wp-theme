import { Card, CardBody, CardHeader } from '@wordpress/components';
import BlockContainer from './block-container';

const BlockCard = ( { title, className = '', children } ) => {
	return (
		<BlockContainer className={ className }>
			<Card>
				<CardHeader>{ title }</CardHeader>
				<CardBody>{ children }</CardBody>
			</Card>
		</BlockContainer>
	);
};

export default BlockCard;
