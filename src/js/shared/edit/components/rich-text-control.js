import { BaseControl } from '@wordpress/components';
import { RichText } from '@wordpress/block-editor';

const DEFAULT_ALLOWED_FORMATS = [
	'core/bold',
	'core/italic',
	'core/link',
	'core/strikethrough',
];

const EDITOR_STYLE = {
	border: '1px solid #949494',
	borderRadius: '2px',
	padding: '6px 8px',
};

const RichTextControl = ( {
	label,
	value,
	onChange,
	placeholder = '',
	id,
	help,
	allowedFormats = DEFAULT_ALLOWED_FORMATS,
} ) => {
	const controlId =
		id || `rich-text-${ label.toLowerCase().replace( /\s+/g, '-' ) }`;

	return (
		<BaseControl
			id={ controlId }
			label={ label }
			help={ help }
			__nextHasNoMarginBottom
		>
			<RichText
				style={ EDITOR_STYLE }
				tagName="p"
				value={ value }
				onChange={ onChange }
				placeholder={ placeholder }
				allowedFormats={ allowedFormats }
				__nextHasNoMarginBottom
			/>
		</BaseControl>
	);
};

export default RichTextControl;
