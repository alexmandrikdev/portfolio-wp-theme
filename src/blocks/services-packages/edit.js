import { __ } from '@wordpress/i18n';
import {
	BaseControl,
	Button,
	Card,
	CardBody,
	CardHeader,
	Flex,
	TextControl,
	TextareaControl,
	ToggleControl,
} from '@wordpress/components';
import { RichText } from '@wordpress/block-editor';
import './editor.scss';
import RemoveButton from '../../js/shared/edit/components/remove-button';
import MoveButtons from '../../js/shared/edit/components/move-buttons';
import { useListManagement } from '../../js/shared/edit/hooks/use-list-management';
import BlockCard from '../../js/shared/edit/components/block-card';
import MediaUploadField from '../../js/shared/edit/components/media-upload-field';

const FeatureItem = ( { item, index, items, onUpdate, onRemove, onMove } ) => {
	const isFirst = index === 0;
	const isLast = index === items.length - 1;

	return (
		<div className="feature-item">
			<Flex align="center" gap={ 3 } style={ { marginBottom: '12px' } }>
				<MoveButtons
					index={ index }
					isFirst={ isFirst }
					isLast={ isLast }
					onMove={ onMove }
				/>

				<TextControl
					label={ __( 'Feature', 'am-portfolio-theme' ) }
					value={ item }
					onChange={ ( value ) => onUpdate( index, value ) }
					placeholder={ __(
						'e.g., Fully responsive design',
						'am-portfolio-theme'
					) }
				/>

				<RemoveButton
					index={ index }
					onRemove={ onRemove }
					style={ { alignSelf: 'flex-end', marginBottom: '8px' } }
				/>
			</Flex>
			{ ! isLast && <hr className="feature-item-separator" /> }
		</div>
	);
};

const PackageCardEditor = ( {
	packageData,
	index,
	packages,
	setAttributes,
} ) => {
	const { moveItem, removeItem } = useListManagement(
		packages,
		setAttributes,
		'package_cards'
	);

	const updatePackageField = ( field, value ) => {
		const updatedPackages = [ ...packages ];
		updatedPackages[ index ] = {
			...updatedPackages[ index ],
			[ field ]: value,
		};
		setAttributes( { package_cards: updatedPackages } );
	};

	const updateFeatureItem = ( featureIndex, value ) => {
		const updatedFeatures = [ ...( packageData.features || [] ) ];
		updatedFeatures[ featureIndex ] = value;
		updatePackageField( 'features', updatedFeatures );
	};

	const isFirst = index === 0;
	const isLast = index === packages.length - 1;

	return (
		<Card style={ { width: '100%', marginBottom: '24px' } }>
			<CardHeader>
				<Flex align="center" gap={ 3 }>
					<MoveButtons
						index={ index }
						isFirst={ isFirst }
						isLast={ isLast }
						onMove={ moveItem }
					/>
					<h4>
						{ __( 'Package Card', 'am-portfolio-theme' ) }{ ' ' }
						{ index + 1 }
					</h4>
					<RemoveButton
						index={ index }
						onRemove={ removeItem }
						style={ { marginLeft: 'auto' } }
					/>
				</Flex>
			</CardHeader>
			<CardBody>
				<Flex direction="column" gap={ 4 }>
					<ToggleControl
						label={ __( 'Featured Package', 'am-portfolio-theme' ) }
						checked={ packageData.is_featured || false }
						onChange={ ( value ) =>
							updatePackageField( 'is_featured', value )
						}
					/>

					{ packageData.is_featured && (
						<TextControl
							label={ __(
								'Featured Label',
								'am-portfolio-theme'
							) }
							value={ packageData.featured_label }
							onChange={ ( value ) =>
								updatePackageField( 'featured_label', value )
							}
							placeholder={ __(
								'Most Popular',
								'am-portfolio-theme'
							) }
						/>
					) }

					<MediaUploadField
						label={ __( 'Package Icon', 'am-portfolio-theme' ) }
						value={ packageData.icon || '' }
						onChange={ ( value ) =>
							updatePackageField( 'icon', value )
						}
					/>

					<TextControl
						label={ __( 'Package Title', 'am-portfolio-theme' ) }
						value={ packageData.title || '' }
						onChange={ ( value ) =>
							updatePackageField( 'title', value )
						}
						placeholder={ __(
							'e.g., Complete Development',
							'am-portfolio-theme'
						) }
					/>

					<TextareaControl
						label={ __(
							'Package Description',
							'am-portfolio-theme'
						) }
						value={ packageData.description || '' }
						onChange={ ( value ) =>
							updatePackageField( 'description', value )
						}
						placeholder={ __(
							'Brief description of the package…',
							'am-portfolio-theme'
						) }
					/>

					<TextControl
						label={ __(
							'Highlighted Value Title',
							'am-portfolio-theme'
						) }
						value={ packageData.highlighted_value_title || '' }
						onChange={ ( value ) =>
							updatePackageField(
								'highlighted_value_title',
								value
							)
						}
						placeholder={ __(
							'Highlighted Value',
							'am-portfolio-theme'
						) }
					/>

					<BaseControl
						id={ `package-${ index }-highlighted-value` }
						__nextHasNoMarginBottom
						label={ __(
							'Highlighted Value Content',
							'am-portfolio-theme'
						) }
					>
						<RichText
							style={ {
								border: '1px solid #949494',
								borderRadius: '2px',
								padding: '6px 8px',
							} }
							tagName="p"
							value={ packageData.highlighted_value || '' }
							onChange={ ( value ) =>
								updatePackageField( 'highlighted_value', value )
							}
							placeholder={ __(
								'What makes this package special…',
								'am-portfolio-theme'
							) }
							allowedFormats={ [
								'core/bold',
								'core/italic',
								'core/link',
								'core/strikethrough',
							] }
						/>
					</BaseControl>

					<TextControl
						label={ __(
							'Design Approach Title',
							'am-portfolio-theme'
						) }
						value={ packageData.design_approach_title || '' }
						onChange={ ( value ) =>
							updatePackageField( 'design_approach_title', value )
						}
						placeholder={ __(
							'Design Approach',
							'am-portfolio-theme'
						) }
					/>

					<BaseControl
						id={ `package-${ index }-design-approach` }
						__nextHasNoMarginBottom
						label={ __(
							'Design Approach Content',
							'am-portfolio-theme'
						) }
					>
						<RichText
							style={ {
								border: '1px solid #949494',
								borderRadius: '2px',
								padding: '6px 8px',
							} }
							tagName="p"
							value={ packageData.design_approach || '' }
							onChange={ ( value ) =>
								updatePackageField( 'design_approach', value )
							}
							placeholder={ __(
								'How we approach design for this package…',
								'am-portfolio-theme'
							) }
							allowedFormats={ [
								'core/bold',
								'core/italic',
								'core/link',
								'core/strikethrough',
							] }
						/>
					</BaseControl>

					<BaseControl
						id={ `package-${ index }-features` }
						__nextHasNoMarginBottom
						label={ __( 'Package Features', 'am-portfolio-theme' ) }
						help={ __(
							'Add features and benefits for this package.',
							'am-portfolio-theme'
						) }
					>
						{ packageData.features &&
							packageData.features.length > 0 && (
								<div className="features-list">
									{ packageData.features.map(
										( feature, featureIndex ) => (
											<FeatureItem
												key={ featureIndex }
												item={ feature }
												index={ featureIndex }
												items={ packageData.features }
												onUpdate={ updateFeatureItem }
												onRemove={ ( removeIndex ) => {
													const updatedFeatures =
														packageData.features.filter(
															( _, i ) =>
																i !==
																removeIndex
														);
													updatePackageField(
														'features',
														updatedFeatures
													);
												} }
												onMove={ (
													fromIndex,
													direction
												) => {
													const featureIsFirst =
														fromIndex === 0;
													const featureIsLast =
														fromIndex ===
														packageData.features
															.length -
															1;

													if (
														( direction === 'up' &&
															featureIsFirst ) ||
														( direction ===
															'down' &&
															featureIsLast )
													) {
														return;
													}

													const newIndex =
														direction === 'up'
															? fromIndex - 1
															: fromIndex + 1;
													const updatedFeatures = [
														...packageData.features,
													];

													[
														updatedFeatures[
															fromIndex
														],
														updatedFeatures[
															newIndex
														],
													] = [
														updatedFeatures[
															newIndex
														],
														updatedFeatures[
															fromIndex
														],
													];
													updatePackageField(
														'features',
														updatedFeatures
													);
												} }
											/>
										)
									) }
								</div>
							) }

						<Button
							variant="primary"
							onClick={ () => {
								const currentFeatures =
									packageData.features || [];
								updatePackageField( 'features', [
									...currentFeatures,
									'',
								] );
							} }
							style={ { marginTop: '16px' } }
						>
							{ __( 'Add Feature', 'am-portfolio-theme' ) }
						</Button>
					</BaseControl>

					<TextControl
						label={ __( 'Button Text', 'am-portfolio-theme' ) }
						value={ packageData.button_text || '' }
						onChange={ ( value ) =>
							updatePackageField( 'button_text', value )
						}
						placeholder={ __(
							'e.g., Get Started',
							'am-portfolio-theme'
						) }
					/>
				</Flex>
			</CardBody>
		</Card>
	);
};

export default function Edit( { attributes, setAttributes } ) {
	const {
		title = '',
		subtitle = '',
		package_cards: packageCards = [],
	} = attributes;

	const { addItem } = useListManagement(
		packageCards,
		setAttributes,
		'package_cards'
	);

	const defaultPackageCard = {
		is_featured: false,
		icon: null,
		title: '',
		description: '',
		highlighted_value: '',
		highlighted_value_title: '',
		design_approach: '',
		design_approach_title: '',
		features: [],
		button_text: '',
		featured_label: __( 'Most Popular', 'am-portfolio-theme' ),
	};

	return (
		<BlockCard
			title={ __( 'Services Packages Section', 'am-portfolio-theme' ) }
		>
			<Card style={ { width: '100%', marginBottom: '24px' } }>
				<CardHeader>
					<h4>{ __( 'Section Settings', 'am-portfolio-theme' ) }</h4>
				</CardHeader>
				<CardBody>
					<Flex direction="column" gap={ 4 }>
						<TextControl
							label={ __(
								'Section Title',
								'am-portfolio-theme'
							) }
							value={ title }
							onChange={ ( value ) =>
								setAttributes( { title: value } )
							}
							placeholder={ __(
								'e.g., Service Packages',
								'am-portfolio-theme'
							) }
						/>

						<TextareaControl
							label={ __(
								'Section Subtitle',
								'am-portfolio-theme'
							) }
							value={ subtitle }
							onChange={ ( value ) =>
								setAttributes( { subtitle: value } )
							}
							placeholder={ __(
								'Brief description of the packages…',
								'am-portfolio-theme'
							) }
						/>
					</Flex>
				</CardBody>
			</Card>

			<BaseControl
				id="package-cards"
				__nextHasNoMarginBottom
				label={ __( 'Package Cards', 'am-portfolio-theme' ) }
				help={ __(
					'Add up to 2 package cards to display.',
					'am-portfolio-theme'
				) }
			>
				{ packageCards.map( ( packageData, index ) => (
					<PackageCardEditor
						key={ index }
						packageData={ packageData }
						index={ index }
						packages={ packageCards }
						setAttributes={ setAttributes }
					/>
				) ) }

				{ packageCards.length < 2 && (
					<Button
						variant="primary"
						onClick={ () => addItem( defaultPackageCard ) }
						style={ { marginTop: '16px' } }
					>
						{ __( 'Add Package Card', 'am-portfolio-theme' ) }
					</Button>
				) }
			</BaseControl>
		</BlockCard>
	);
}
