import {BaseControl, Button, ButtonGroup, SelectControl, ToggleControl} from "@wordpress/components";

export const sanitize_classes = function( classes ) {
	// Convert classes to a string if needed
	if ( typeof classes === 'undefined' ) classes = '';
	else if ( Array.isArray(classes) ) classes = classes.join(' ');
	else if ( typeof classes !== 'string' ) classes = '';

	if ( classes !== '' ) {
		// Remove extra spaces in the middle
		classes = classes.replace(/  +/g, ' ');

		// Remove spaces at the beginning and end
		classes = classes.trim();
	}

	return classes;
}

export const classes_to_array = function( classes ) {
	let list;

	classes = sanitize_classes( classes );

	// Convert to array, split at each space
	if ( typeof classes === 'string' ) {
		list = classes.split(' ');
	}else if ( typeof classes === 'object' ) {
		list = classes;
	}else{
		list = [];
	}

	// Remove empty items
	if ( !! list ) {
		list.filter((value) => { return value.trim() !== ""; });
	}

	return list;
}

export const has_class = function( classes, selected_class, choices = null ) {
	// classes: string or array of classes from the block: "button-color--light is-style-outline"
	// selected_class: the class being checked for: "is-style-outline"
	// choices: optional array of choices that can be selected

	if ( choices === null ) {
		// Simply check if a class is selected
		let classList = classes_to_array( classes );
		return classList.includes( selected_class );
	}else{
		// Check if a class is selected that exists in the list of choices
		return get_class_from_choices( classes, choices ) === selected_class;
	}
}

export const get_class_from_choices = function( classes, choices ) {
	let classList = classes_to_array( classes );
	let found_class = false;
	let has_empty_choice = false;

	choices.forEach(( choice ) => {
		if ( ! choice.value ) {
			has_empty_choice = true;
		}else if ( classList.includes( choice.value ) ) {
			found_class = choice.value;
		}
	});

	// If no choice was selected by a blank value is available, return an empty string
	if ( ! found_class && has_empty_choice ) {
		return '';
	}

	return found_class;
}

export const add_class = function( classes, new_class ) {
	classes = sanitize_classes( classes );

	if ( ! has_class( classes, new_class ) ) {
		if ( classes ) classes += ' ';
		classes += new_class;
	}
	return classes;
}

export const remove_class = function( classes, new_class ) {
	classes = sanitize_classes( classes );

	let classList = classes_to_array( classes );

	classList.forEach(( class_name ) => {
		if ( class_name === new_class ) {
			classes = classes.replace( class_name, '' );
		}
	});

	return sanitize_classes( classes );
}

/**
 * Check if a class exists with a given prefix, and returns the value following the prefix.
 * If the prefix is "gap-" and classes contain "gap-20", returns "20".
 */
export const get_class_prefix_value = function( classes, prefix, has_value_class = '' ) {

	// If "has_value_class" is used, require that class also be set.
	if ( !! has_value_class && ! has_class( classes, has_value_class ) ) {
		return false;
	}

	classes = sanitize_classes( classes );

	// let matched = classes.match(/(^| )gap-(\d+|none)( |$)/)[2];

	let pattern = new RegExp('(^| )' + prefix + '([^ ]+)( |$)');
	let matched = classes.match( pattern );

	return matched !== null ? matched[2] : false;
}

/**
 * Remove all classes with a given prefix, except for the one specified as "keep_class"
 */
export const remove_prefixed_classes = function( classes, prefix, keep_class = false ) {
	classes = sanitize_classes( classes );

	let classList = classes_to_array( classes );

	// Check each class
	classList.forEach(( class_name ) => {
		// Does this class have the prefix?
		if ( class_name.indexOf( prefix ) === 0 ) {
			// Should it be kept?
			if ( keep_class === false || class_name !== keep_class ) {
				classes = remove_class( classes, class_name );
			}
		}
	});

	return classes;
}

/**
 * Add or remove a class based on a value.
 */
export const toggle_class = function( classes, new_class, value ) {
	classes = sanitize_classes( classes );

	if ( !! value ) {
		return add_class( classes, new_class );
	}else{
		return remove_class( classes, new_class );
	}
}

/**
 * Set the class to the selected value, clearing any other classes with the same prefix.
 * Optionally toggles the "has_value_class" based on if a value is set.
 * @todo: Add support for blockType for each choice ?
 */
export const set_prefixed_class_from_list = function( classes, choices, value, prefix, has_value_class = '' ) {
	// classes: String list of current classes
	// choices: Array of values, each item must have a ".className" property
	// value: Current value
	// prefix: Prefix for class name, if value is non-empty
	// has_value_class: Optional. If a value is present, this class is also added

	classes = sanitize_classes( classes );

	// "" removes the class.
	// all other values, including "0" or "none", are added with a prefix.
	let new_class = (value === "" ? false : prefix + value);

	// Remove other classes with the same prefix
	classes = remove_prefixed_classes( classes, prefix, new_class );

	if ( new_class ) {
		// Add
		classes = add_class( classes, new_class );
		if ( has_value_class ) classes = add_class( classes, has_value_class );
	}else{
		// Remove
		classes = remove_class( classes, new_class );
		if ( has_value_class ) classes = remove_class( classes, has_value_class );
	}

	classes = sanitize_classes( classes );

	return classes;
}

/**
 * Set the class to the selected value, clearing other classes from the list of choices.
 * Does not support multiple values.
 * Optionally toggles the "has_value_class" based on if a value is set.
 */
export const set_class_from_list = function( classes, choices, value, has_value_class = '' ) {
	// classes: String list of current classes
	// choices: Array of choices, each item must have a ".className" property
	// value: Current value

	// Get string of all classes
	classes = sanitize_classes( classes );

	// Get the new class being added
	let new_class = value || '';

	// Remove the class for each other option
	choices.forEach(( choice ) => {
		if ( ! choice.value ) return;

		// Keep the class if it's the one we are adding
		if ( choice.value === new_class ) return;

		// If this class is assigned, remove it
		if ( has_class( classes, choice.value ) ) {
			classes = remove_class( classes, choice.value );
		}
	});

	// Add or remove the "has_value_class" if there is a value
	if ( !! has_value_class ) {
		classes = toggle_class( classes, has_value_class, !! new_class );
	}

	// Add the new class
	classes = add_class( classes, new_class );

	return classes;
}

/**
 * Set the classes of a block to the selected value, clearing any other classes with the same prefix.
 * Optionally toggles the "has_value_class" based on if a value is set.
 */
export const set_block_prefixed_class_from_list = function( props, choices, value, prefix, has_value_class = '' ) {
	props.setAttributes({
		className: set_prefixed_class_from_list( props.attributes.className, choices, value, prefix, has_value_class )
	});
}

/**
 * Set the classes to the selected value for a block, clearing other classes from the list of choices.
 * Does not support multiple values.
 * Optionally toggles the "has_value_class" based on if a value is set.
 */
export const toggle_block_class = function( props, new_class, value ) {
	props.setAttributes({
		className: toggle_class( props.attributes.className, new_class, value )
	});
}

/**
 * Set the classes to the selected value for a block, clearing other classes from the list of choices.
 * Does not support multiple values.
 * Optionally toggles the "has_value_class" based on if a value is set.
 */
export const set_block_class_from_list = function( props, choices, value, has_value_class = '', allow_null = false ) {

	if ( allow_null && value === get_class_from_choices( props.attributes.className, choices ) ) {
		value = '';
	}

	props.setAttributes({
		className: set_class_from_list( props.attributes.className, choices, value, has_value_class )
	});
}

/**
 * Return an array of classes from the list of choices which are located in the class list.
 * If "has_value_class" is provided, only returns classes if that class is also present.
 * @todo: Add support for blockType for each choice ?
 */
export const find_class_from_list = function( classes, choices, has_value_class = null ) {
	// classes: String list of current classes
	// choices: Array of choices, each item must have a ".className" property
	// value: Current value

	// If using "has_value_class", require that class to be present as well.
	if ( has_value_class !== null && ! has_class( classes, has_value_class ) ) {
		return false;
	}

	// Get the list of classes
	let classList = classes_to_array( classes );
	let found_classes = [];

	// Check each class
	classList.forEach(( class_name ) => {
		// Check each choice
		choices.forEach(( choice ) => {
			if ( class_name === choice.value ) {
				found_classes.push( class_name );
			}
		});
	});

	return found_classes;
}

/**
 * Check if a block supports a given block type.
 * @param {string} block_name - The block name to check
 * @param {string|array} supported_blocks - The block type or types that are supported
 * @return {boolean} - True if the block is supported
 */
export const is_block_type_supported = function( block_name, supported_blocks ) {
	if ( supported_blocks === 'all' ) {
		return true;
	}else if ( is_string_in_list( block_name, supported_blocks ) ) {
		return supported_blocks.includes( block_name );
	}else{
		return false;
	}
}

/**
 * Returns choices that match the block settings, by checking the blockType against each option.
 */
export const filter_block_choices = function( props, choices ) {
	let block_name = props.name;
	let filtered_choices = [];

	// Loop through each choice and check if the blockType is supported
	choices.forEach(( choice ) => {

		// Check if the block is supported
		let block_supported = is_block_type_supported( block_name, choice.blockType );

		/*
		if ( ! choice.blockType ) {
			block_supported = true; // supported if blockType is not specified
		}else if ( choice.blockType === 'all' ) {
			block_supported = true; // supported if blockType is "all"
		}else if ( is_string_in_list( block_name, choice.blockType ) ) {
			block_supported = true; // supported if block name is in the blockType list (array or string)
		}
		*/

		// If the block is supported, add the choice to the list
		if ( block_supported ) {
			filtered_choices.push( choice );
		}

	});

	return filtered_choices;
};

/**
 * Check if a string value exists in an array of strings, or matches a single string.
 * If the input value is empty, returns false.
 */
export const is_string_in_list = function( value, list ) {
	// Value must be a non-empty string
	if ( typeof value !== 'string' || value === '' ) return false;

	// List may be an array of strings or a single string
	if ( Array.isArray( list ) ) {
		return list.includes( value );
	}else{
		return value === list;
	}
}

/**
 * Check if a block supports container styles.
 */
export const does_block_support_container_styles = ( name ) => {
	return is_string_in_list( name, [
		'core/group',
		'core/columns',
		'core/cover'
	]);
}

/**
 * Check if a block supports mobile settings.
 */
export const does_block_support_mobile = ( name ) => {
	return is_string_in_list( name, [
		'core/group',
		'core/columns',
		'core/buttons',
		'core/navigation'
	]);
}

/**
 * Check if a block supports button sizes.
 */
export const does_block_support_button_styles = ( name ) => {
	return is_string_in_list( name, [
		'core/button',
		'sbi/company-tags',
		'sbi/company-actions',
		'sbi/company-review-button'
	]);
}

/**
 * Return a button grid component for the block editor, displaying a list of buttons that each toggle a class.
 * @todo: Add support for blockType for each choice
 */
export const get_button_grid_field = function( props, choices, name, label = '', cols = '', allow_null = true ) {

	let debug = function( choice ) {
		console.log('button group', {
			value: choice.value,
			choice: choice,
			is_primary: has_class(props.attributes.className, choice.value),
			is_secondary: !has_class(props.attributes.className, choice.value),
		});
	};

	return (
		<BaseControl label={label} className={`rs-utility-blocks--button-group--container rs-utility-blocks--button-group--${name}`}>
			<ButtonGroup className="rs-utility-blocks--button-group" data-cols={cols}>
				{choices.map(( choice ) => (
					<Button
						isPrimary={has_class(props.attributes.className, choice.value, choices)}
						isSecondary={!has_class(props.attributes.className, choice.value, choices)}
						onClick={() => {
							set_block_class_from_list( props, choices, choice.value, '', allow_null );
							debug( choice );
						}}
						className={choice.value}
						>
					{choice.label}
					</Button>
				))}
			</ButtonGroup>
		</BaseControl>
	);

};


/**
 * Return a select dropdown component for the block editor, displaying a list of options which toggle a class when selected.
 */
export const get_dropdown_field = function( props, choices, name, label = '' ) {

	const filtered_choices = filter_block_choices( props, choices );

	return (
		<SelectControl
			className={`rs-utility-blocks--dropdown rs-utility-blocks--dropdown--${name}`}
			label={label}
			value={get_class_from_choices( props.attributes.className, filtered_choices )}
			options={filtered_choices}
			onChange={(value) => {
				set_block_class_from_list( props, filtered_choices, value, '', true )
			}}
		/>
	);

};


/**
 * Return a list of checkboxes as a component for the block editor
 * @param {object} props - The block props
 * @param {array} choices - An array of objects, each with a "value" and "label" property
 * @param {string} any_selection_class - An optional class to add if any choices are selected
 * @param {string} multiple_selection_class - An optional class to add if multiple choices are selected
 */
export const get_checkbox_field = function( props, choices, any_selection_class = '', multiple_selection_class = '' ) {

	let checkboxes = [];

	choices.forEach((choice) => {
		let value = choice.value;
		let class_name = value;
		let label = choice.label;
		let excluded_classes = choice.exclude || [];

		let onChange = (value) => {
			let classes = sanitize_classes( props.attributes.className );

			if ( ! value ) {

				// If not selected, remove the class
				classes = remove_class( classes, class_name );

			}else{

				// If selected, add the class
				classes = add_class( classes, class_name );

				// Remove classes that should be excluded
				if ( excluded_classes.length ) excluded_classes.forEach((exclude_class) => {
					classes = remove_class( classes, exclude_class );
				});

			}

			// Toggle the any_selection_class if any choices are selected
			if ( any_selection_class || multiple_selection_class ) {

				// Get any classes that are part of the choices
				let selected_choice_classes = find_class_from_list( classes, choices );

				// If any classes are selected, add the any_selection_class
				if ( any_selection_class ) {
					if ( selected_choice_classes.length ) {
						classes = add_class( classes, any_selection_class );
					}else{
						classes = remove_class( classes, any_selection_class );
					}
				}

				// If multiple classes are selected, add the multiple_selection_class
				if ( multiple_selection_class ) {
					if ( selected_choice_classes.length > 1 ) {
						classes = add_class( classes, multiple_selection_class );
					}else{
						classes = remove_class( classes, multiple_selection_class );
					}
				}

			}

			// Save the classes
			props.setAttributes({ className: classes });
		};

		checkboxes.push(
			<ToggleControl
				label={label}
				checked={has_class( props.attributes.className, value )}
				onChange={onChange}
			/>
		);
	});

	return checkboxes;
};