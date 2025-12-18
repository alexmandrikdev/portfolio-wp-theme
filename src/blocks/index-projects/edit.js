import { __ } from '@wordpress/i18n';
import { Flex, TextControl } from '@wordpress/components';
import './editor.scss';
import BlockCard from '../../js/shared/edit/components/block-card';

export default function Edit( { attributes, setAttributes } ) {
	const { title = '' } = attributes;

	const handleTitleChange = ( value ) => {
		setAttributes( { title: value } );
	};

	return (
		<BlockCard title={ __( 'Projects Section', 'portfolio' ) }>
			<Flex direction="column" gap={ 4 }>
				<TextControl
					__nextHasNoMarginBottom
					label={ __( 'Title', 'portfolio' ) }
					value={ title }
					onChange={ handleTitleChange }
					placeholder={ __( 'My Projects', 'portfolio' ) }
				/>

				<p
					style={ {
						color: '#757575',
						fontSize: '13px',
						marginTop: '16px',
						fontStyle: 'italic',
					} }
				>
					{ __(
						'This block automatically displays the 3 projects with the highest priority (lowest menu_order values).',
						'portfolio'
					) }
				</p>
			</Flex>
		</BlockCard>
	);
}
