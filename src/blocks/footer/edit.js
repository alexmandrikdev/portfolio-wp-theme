import { __ } from '@wordpress/i18n';
import {
	BaseControl,
	Button,
	Card,
	CardBody,
	CardHeader,
	ComboboxControl,
	Flex,
	FlexBlock,
	TextControl,
	TextareaControl,
} from '@wordpress/components';
import { RichText } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { store as coreDataStore } from '@wordpress/core-data';
import RemoveButton from '../../js/shared/edit/components/remove-button';
import MoveButtons from '../../js/shared/edit/components/move-buttons';
import { useListManagement } from '../../js/shared/edit/hooks/use-list-management';
import BlockCard from '../../js/shared/edit/components/block-card';
import './editor.scss';

const TextInput = ( { label, value, onChange, placeholder } ) => (
	<FlexBlock>
		<TextControl
			label={ label }
			value={ value }
			onChange={ onChange }
			placeholder={ placeholder }
		/>
	</FlexBlock>
);

const TextareaInput = ( { label, value, onChange, placeholder } ) => (
	<FlexBlock>
		<TextareaControl
			label={ label }
			value={ value }
			onChange={ onChange }
			placeholder={ placeholder }
		/>
	</FlexBlock>
);

const QuickLinkItem = ( {
	item,
	index,
	items,
	onUpdate,
	onRemove,
	onMove,
} ) => {
	const isFirst = index === 0;
	const isLast = index === items.length - 1;

	const pages =
		useSelect( ( select ) => {
			return select( coreDataStore ).getEntityRecords(
				'postType',
				'page',
				{
					per_page: -1,
				}
			);
		} ) || [];

	const pageOptions = pages.map( ( page ) => {
		return {
			label: page.title.rendered,
			value: page.id,
		};
	} );

	return (
		<div className="quick-link-item">
			<Flex align="center" gap={ 3 } style={ { marginBottom: '12px' } }>
				<MoveButtons
					index={ index }
					isFirst={ isFirst }
					isLast={ isLast }
					onMove={ onMove }
				/>

				<FlexBlock>
					<ComboboxControl
						label={ __( 'Page', 'am-portfolio-theme' ) }
						value={ item.page_id || 0 }
						onChange={ ( value ) =>
							onUpdate( index, 'page_id', value )
						}
						options={ pageOptions }
					/>
				</FlexBlock>

				<RemoveButton
					index={ index }
					onRemove={ onRemove }
					style={ { alignSelf: 'flex-end', marginBottom: '8px' } }
				/>
			</Flex>
			{ ! isLast && <hr className="quick-link-item-separator" /> }
		</div>
	);
};

export default function Edit( { attributes, setAttributes } ) {
	const {
		personal_name: personalName = '',
		personal_tagline: personalTagline = '',
		personal_description: personalDescription = '',
		quick_links_title: quickLinksTitle = '',
		quick_links: quickLinks = [],
		social_media_title: socialMediaTitle = '',
		contact_title: contactTitle = '',
		contact_text: contactText = '',
		contact_cta_text: contactCtaText = '',
		copyright_text: copyrightText = '',
		footer_note: footerNote = '',
		cookie_preferences_text: cookiePreferencesText = '',
	} = attributes;

	const {
		addItem: addQuickLink,
		moveItem: moveQuickLink,
		removeItem: removeQuickLink,
		updateItem: updateQuickLink,
	} = useListManagement( quickLinks, setAttributes, 'quick_links' );

	const defaultQuickLink = { page_id: '' };

	return (
		<BlockCard title={ __( 'Footer Section', 'am-portfolio-theme' ) }>
			<Card style={ { width: '100%', marginBottom: '24px' } }>
				<CardHeader>
					<h4>
						{ __( 'Personal Information', 'am-portfolio-theme' ) }
					</h4>
				</CardHeader>
				<CardBody>
					<Flex direction="column" gap={ 4 }>
						<TextInput
							label={ __( 'Name', 'am-portfolio-theme' ) }
							value={ personalName }
							onChange={ ( value ) =>
								setAttributes( { personal_name: value } )
							}
							placeholder={ __(
								'e.g., Alex András Mándrik',
								'am-portfolio-theme'
							) }
						/>

						<TextInput
							label={ __( 'Tagline', 'am-portfolio-theme' ) }
							value={ personalTagline }
							onChange={ ( value ) =>
								setAttributes( { personal_tagline: value } )
							}
							placeholder={ __(
								'e.g., Full-Stack Developer & Designer',
								'am-portfolio-theme'
							) }
						/>

						<BaseControl
							id="personal-description"
							__nextHasNoMarginBottom
							label={ __( 'Description', 'am-portfolio-theme' ) }
						>
							<RichText
								style={ {
									border: '1px solid #949494',
									borderRadius: '2px',
									padding: '6px 8px',
								} }
								tagName="p"
								value={ personalDescription }
								onChange={ ( value ) =>
									setAttributes( {
										personal_description: value,
									} )
								}
								placeholder={ __(
									'Brief description about yourself…',
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
					</Flex>
				</CardBody>
			</Card>

			<Card style={ { width: '100%', marginBottom: '24px' } }>
				<CardHeader>
					<h4>{ __( 'Quick Links', 'am-portfolio-theme' ) }</h4>
				</CardHeader>
				<CardBody>
					<Flex direction="column" gap={ 4 }>
						<TextInput
							label={ __(
								'Quick Links Title',
								'am-portfolio-theme'
							) }
							value={ quickLinksTitle }
							onChange={ ( value ) =>
								setAttributes( { quick_links_title: value } )
							}
							placeholder={ __(
								'e.g., Quick Links',
								'am-portfolio-theme'
							) }
						/>
					</Flex>

					<BaseControl
						id="quick-links"
						__nextHasNoMarginBottom
						label={ __( 'Quick Links', 'am-portfolio-theme' ) }
						help={ __(
							'Add navigation links for quick access.',
							'am-portfolio-theme'
						) }
					>
						{ quickLinks.length > 0 && (
							<div className="quick-links-list">
								{ quickLinks.map( ( item, index ) => (
									<QuickLinkItem
										key={ index }
										item={ item }
										index={ index }
										items={ quickLinks }
										onUpdate={ updateQuickLink }
										onRemove={ removeQuickLink }
										onMove={ moveQuickLink }
									/>
								) ) }
							</div>
						) }

						<Button
							variant="primary"
							onClick={ () => addQuickLink( defaultQuickLink ) }
							style={ { marginTop: '16px' } }
						>
							{ __( 'Add Quick Link', 'am-portfolio-theme' ) }
						</Button>
					</BaseControl>
				</CardBody>
			</Card>

			<Card style={ { width: '100%', marginBottom: '24px' } }>
				<CardHeader>
					<h4>
						{ __( 'Social Media Section', 'am-portfolio-theme' ) }
					</h4>
				</CardHeader>
				<CardBody>
					<Flex direction="column" gap={ 4 }>
						<TextInput
							label={ __(
								'Social Media Title',
								'am-portfolio-theme'
							) }
							value={ socialMediaTitle }
							onChange={ ( value ) =>
								setAttributes( { social_media_title: value } )
							}
							placeholder={ __(
								'e.g., Follow Me',
								'am-portfolio-theme'
							) }
						/>
					</Flex>
				</CardBody>
			</Card>

			<Card style={ { width: '100%', marginBottom: '24px' } }>
				<CardHeader>
					<h4>{ __( 'Contact Section', 'am-portfolio-theme' ) }</h4>
				</CardHeader>
				<CardBody>
					<Flex direction="column" gap={ 4 }>
						<TextInput
							label={ __(
								'Contact Title',
								'am-portfolio-theme'
							) }
							value={ contactTitle }
							onChange={ ( value ) =>
								setAttributes( { contact_title: value } )
							}
							placeholder={ __(
								'e.g., Get in Touch',
								'am-portfolio-theme'
							) }
						/>

						<BaseControl
							id="contact-text"
							__nextHasNoMarginBottom
							label={ __( 'Contact Text', 'am-portfolio-theme' ) }
						>
							<RichText
								style={ {
									border: '1px solid #949494',
									borderRadius: '2px',
									padding: '6px 8px',
								} }
								tagName="p"
								value={ contactText }
								onChange={ ( value ) =>
									setAttributes( { contact_text: value } )
								}
								placeholder={ __(
									'Text inviting visitors to contact you…',
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

						<TextInput
							label={ __(
								'Contact Button Text',
								'am-portfolio-theme'
							) }
							value={ contactCtaText }
							onChange={ ( value ) =>
								setAttributes( { contact_cta_text: value } )
							}
							placeholder={ __(
								'e.g., Get in Touch',
								'am-portfolio-theme'
							) }
						/>
					</Flex>
				</CardBody>
			</Card>

			<Card style={ { width: '100%', marginBottom: '24px' } }>
				<CardHeader>
					<h4>{ __( 'Bottom Section', 'am-portfolio-theme' ) }</h4>
				</CardHeader>
				<CardBody>
					<Flex direction="column" gap={ 4 }>
						<TextInput
							label={ __(
								'Copyright Text',
								'am-portfolio-theme'
							) }
							value={ copyrightText }
							onChange={ ( value ) =>
								setAttributes( { copyright_text: value } )
							}
							placeholder={ __(
								'e.g., © [year] Alex András Mándrik. All rights reserved.',
								'am-portfolio-theme'
							) }
							help={ __(
								'Use [year] placeholder to automatically insert the current year.',
								'am-portfolio-theme'
							) }
						/>

						<TextareaInput
							label={ __( 'Footer Note', 'am-portfolio-theme' ) }
							value={ footerNote }
							onChange={ ( value ) =>
								setAttributes( { footer_note: value } )
							}
							placeholder={ __(
								'Additional footer information or notes…',
								'am-portfolio-theme'
							) }
						/>

						<TextInput
							label={ __(
								'Cookie Preferences Link Text',
								'am-portfolio-theme'
							) }
							value={ cookiePreferencesText }
							onChange={ ( value ) =>
								setAttributes( {
									cookie_preferences_text: value,
								} )
							}
							placeholder={ __(
								'e.g., Cookie Preferences',
								'am-portfolio-theme'
							) }
							help={ __(
								'Text for the link that opens the cookie consent banner.',
								'am-portfolio-theme'
							) }
						/>
					</Flex>
				</CardBody>
			</Card>
		</BlockCard>
	);
}
