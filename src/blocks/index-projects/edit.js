import { __ } from '@wordpress/i18n';
import {
	BaseControl,
	Button,
	Card,
	CardBody,
	CardHeader,
	Flex,
	FlexBlock,
	TextControl,
	ComboboxControl,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import './editor.scss';
import RemoveButton from '../../js/shared/edit/components/remove-button';
import MoveButtons from '../../js/shared/edit/components/move-buttons';
import { useListManagement } from '../../js/shared/edit/hooks/use-list-management';
import BlockContainer from '../../js/shared/edit/components/block-container';

const ProjectItem = ( {
	item,
	index,
	projectItems,
	onUpdate,
	onRemove,
	onMove,
	availablePosts,
} ) => {
	const isFirst = index === 0;
	const isLast = index === projectItems.length - 1;

	return (
		<div className="project-item">
			<Flex align="center" gap={ 3 } style={ { marginBottom: '12px' } }>
				<MoveButtons
					index={ index }
					isFirst={ isFirst }
					isLast={ isLast }
					onMove={ onMove }
				/>

				<FlexBlock>
					<ComboboxControl
						label={ __( 'Project', 'portfolio' ) }
						value={ item.post_id }
						onChange={ ( selected ) =>
							onUpdate( index, 'post_id', selected )
						}
						options={ availablePosts }
						allowReset={ false }
					/>
				</FlexBlock>

				<RemoveButton
					index={ index }
					onRemove={ onRemove }
					style={ { alignSelf: 'flex-end', marginBottom: '8px' } }
				/>
			</Flex>

			{ ! isLast && <hr className="project-item-separator" /> }
		</div>
	);
};

export default function Edit( { attributes, setAttributes } ) {
	const { title = '', post_ids: projectItems = [] } = attributes;

	const { addItem, moveItem, removeItem, updateItem } = useListManagement(
		projectItems,
		setAttributes,
		'post_ids'
	);

	const availablePosts = useSelect( ( select ) => {
		const posts = select( 'core' ).getEntityRecords(
			'postType',
			'project',
			{
				per_page: -1,
				status: 'publish',
			}
		);

		if ( ! posts ) {
			return [];
		}

		return posts.map( ( post ) => ( {
			label: post.title.rendered,
			value: post.id,
		} ) );
	}, [] );

	const defaultItem = { post_id: '' };

	const handleTitleChange = ( value ) => {
		setAttributes( { title: value } );
	};

	return (
		<BlockContainer>
			<Card style={ { width: '100%' } }>
				<CardHeader>
					<h4>{ __( 'Projects Section', 'portfolio' ) }</h4>
				</CardHeader>
				<CardBody>
					<TextControl
						label={ __( 'Title', 'portfolio' ) }
						value={ title }
						onChange={ handleTitleChange }
						placeholder={ __( 'My Projects', 'portfolio' ) }
					/>

					<BaseControl
						id="project-items"
						__nextHasNoMarginBottom
						label={ __( 'Projects', 'portfolio' ) }
						help={ __( 'Select up to 3 projects', 'portfolio' ) }
					>
						{ projectItems.length > 0 && (
							<div className="project-items-list">
								{ projectItems.map( ( item, index ) => (
									<ProjectItem
										key={ index }
										item={ item }
										index={ index }
										projectItems={ projectItems }
										onUpdate={ updateItem }
										onRemove={ removeItem }
										onMove={ moveItem }
										availablePosts={ availablePosts }
									/>
								) ) }
							</div>
						) }

						<Button
							variant="primary"
							onClick={ () => addItem( defaultItem ) }
							disabled={
								projectItems.length >= 3 ||
								availablePosts.length === 0
							}
							style={ { marginTop: '16px' } }
						>
							{ __( 'Add Project', 'portfolio' ) }
						</Button>

						{ projectItems.length >= 3 && (
							<p
								style={ {
									color: '#cc1818',
									fontSize: '12px',
									marginTop: '8px',
								} }
							>
								{ __(
									'Maximum 3 projects allowed',
									'portfolio'
								) }
							</p>
						) }

						{ availablePosts.length === 0 && (
							<p
								style={ {
									color: '#cc1818',
									fontSize: '12px',
									marginTop: '8px',
								} }
							>
								{ __(
									'No projects available. Please create some project posts first.',
									'portfolio'
								) }
							</p>
						) }
					</BaseControl>
				</CardBody>
			</Card>
		</BlockContainer>
	);
}
