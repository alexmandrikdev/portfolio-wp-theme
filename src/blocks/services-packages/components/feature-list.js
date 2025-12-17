import { __ } from '@wordpress/i18n';
import { BaseControl, Button } from '@wordpress/components';
import FeatureItem from './feature-item';

const FeatureList = ( {
	packageData,
	index,
	updatePackageField,
	updateFeatureItem,
} ) => {
	const handleRemoveFeature = ( removeIndex ) => {
		const updatedFeatures = packageData.features.filter(
			( _, i ) => i !== removeIndex
		);
		updatePackageField( 'features', updatedFeatures );
	};

	const handleMoveFeature = ( fromIndex, direction ) => {
		const featureIsFirst = fromIndex === 0;
		const featureIsLast = fromIndex === packageData.features.length - 1;

		if (
			( direction === 'up' && featureIsFirst ) ||
			( direction === 'down' && featureIsLast )
		) {
			return;
		}

		const newIndex = direction === 'up' ? fromIndex - 1 : fromIndex + 1;
		const updatedFeatures = [ ...packageData.features ];

		[ updatedFeatures[ fromIndex ], updatedFeatures[ newIndex ] ] = [
			updatedFeatures[ newIndex ],
			updatedFeatures[ fromIndex ],
		];
		updatePackageField( 'features', updatedFeatures );
	};

	return (
		<BaseControl
			id={ `package-${ index }-features` }
			__nextHasNoMarginBottom
			label={ __( 'Package Features', 'am-portfolio-theme' ) }
			help={ __(
				'Add features and benefits for this package.',
				'am-portfolio-theme'
			) }
		>
			{ packageData.features && packageData.features.length > 0 && (
				<div className="features-list">
					{ packageData.features.map( ( feature, featureIndex ) => (
						<FeatureItem
							key={ `feature-${ featureIndex }` }
							item={ feature }
							index={ featureIndex }
							items={ packageData.features }
							onUpdate={ updateFeatureItem }
							onRemove={ handleRemoveFeature }
							onMove={ handleMoveFeature }
						/>
					) ) }
				</div>
			) }

			<Button
				variant="primary"
				onClick={ () => {
					const currentFeatures = packageData.features || [];
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
	);
};

export default FeatureList;
