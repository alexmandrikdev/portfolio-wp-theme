import { __ } from '@wordpress/i18n';
import { trash as trashIcon } from '@wordpress/icons';
import { Button, FlexItem, Tooltip } from '@wordpress/components';

const RemoveButton = ( {
	index,
	onRemove,
	style = {},
	tooltipText = __( 'Remove item', 'portfolio' ),
} ) => (
	<FlexItem style={ style }>
		<Tooltip text={ tooltipText }>
			<Button
				size="small"
				isDestructive
				icon={ trashIcon }
				onClick={ () => onRemove( index ) }
			/>
		</Tooltip>
	</FlexItem>
);

export default RemoveButton;
