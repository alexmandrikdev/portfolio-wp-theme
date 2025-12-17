import { __ } from '@wordpress/i18n';
import { BaseControl, Button } from '@wordpress/components';
import './editor.scss';
import { useListManagement } from '../../js/shared/edit/hooks/use-list-management';
import BlockCard from '../../js/shared/edit/components/block-card';
import PackageSettings from './components/package-settings';
import PackageCardEditor from './components/package-card-editor';

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
			<PackageSettings
				title={ title }
				subtitle={ subtitle }
				setAttributes={ setAttributes }
			/>

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
