// Custom utilities
import {
	find_class_from_list,
	get_checkbox_field
} from "../includes/utilities";

// Dependencies
import domReady from '@wordpress/dom-ready';
import { createHigherOrderComponent } from '@wordpress/compose';
import { addFilter } from '@wordpress/hooks';

// Elements
import { InspectorControls } from "@wordpress/block-editor";
import { Fragment } from '@wordpress/element';
import { PanelBody } from '@wordpress/components';

domReady(() => {

	const registerVisibilityField = createHigherOrderComponent(( BlockEdit ) => {

		const any_selection_class = 'has-visibility';
		const multiple_selection_class = 'has-multiple-visibility';

		const choices = [
			{ value: 'hide-always'       , label: 'Hide everywhere', exclude: [] },
			{ value: 'hide-on-mobile'    , label: 'Hide on mobile', exclude: [ 'hide-on-desktop' ] },
			{ value: 'hide-on-desktop'   , label: 'Hide on desktop', exclude: [ 'hide-on-mobile' ] },
			{ value: 'hide-if-logged-in' , label: 'Hide if logged in', exclude: [ 'hide-if-not-logged-in' ] },
			{ value: 'hide-if-not-logged-in', label: 'Hide if logged not logged in', exclude: [ 'hide-if-logged-in' ] },
			{ value: 'hide-if-user-admin'     , label: 'Hide for admins', exclude: [ 'hide-if-user-not-admin' ] },
			{ value: 'hide-if-user-not-admin' , label: 'Hide for non-admins', exclude: [ 'hide-if-user-admin' ] },
		];

		return (props) => {
			const { name, setAttributes, isSelected } = props;

			let classes = props.attributes.className || '';

			let selected_classes = find_class_from_list( classes, choices );

			let conditionsEnabled = selected_classes.length > 0;

			return (
				<Fragment>
					<BlockEdit {...props} />

					<InspectorControls>

						{ /* Display a list of checkboxes, collapsed unless an item is selected */ }
						{isSelected && (

							<PanelBody title="Visibility" initialOpen={conditionsEnabled}>
								{ get_checkbox_field( props, choices, any_selection_class, multiple_selection_class ) }
							</PanelBody>

						)}

					</InspectorControls>
				</Fragment>
			);
		};

	}, 'registerVisibilityField' );

	addFilter( 'editor.BlockEdit', 'rs_utility_blocks/register_visibility_field', registerVisibilityField, 30);

} );