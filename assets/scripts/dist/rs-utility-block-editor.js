/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/scripts/src/includes/utilities.js":
/*!**************************************************!*\
  !*** ./assets/scripts/src/includes/utilities.js ***!
  \**************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   add_class: () => (/* binding */ add_class),
/* harmony export */   classes_to_array: () => (/* binding */ classes_to_array),
/* harmony export */   does_block_support_button_styles: () => (/* binding */ does_block_support_button_styles),
/* harmony export */   does_block_support_container_styles: () => (/* binding */ does_block_support_container_styles),
/* harmony export */   does_block_support_mobile: () => (/* binding */ does_block_support_mobile),
/* harmony export */   filter_block_choices: () => (/* binding */ filter_block_choices),
/* harmony export */   find_class_from_list: () => (/* binding */ find_class_from_list),
/* harmony export */   get_button_grid_field: () => (/* binding */ get_button_grid_field),
/* harmony export */   get_checkbox_field: () => (/* binding */ get_checkbox_field),
/* harmony export */   get_class_from_choices: () => (/* binding */ get_class_from_choices),
/* harmony export */   get_class_prefix_value: () => (/* binding */ get_class_prefix_value),
/* harmony export */   get_dropdown_field: () => (/* binding */ get_dropdown_field),
/* harmony export */   has_class: () => (/* binding */ has_class),
/* harmony export */   is_block_type_supported: () => (/* binding */ is_block_type_supported),
/* harmony export */   is_string_in_list: () => (/* binding */ is_string_in_list),
/* harmony export */   remove_class: () => (/* binding */ remove_class),
/* harmony export */   remove_prefixed_classes: () => (/* binding */ remove_prefixed_classes),
/* harmony export */   sanitize_classes: () => (/* binding */ sanitize_classes),
/* harmony export */   set_block_class_from_list: () => (/* binding */ set_block_class_from_list),
/* harmony export */   set_block_prefixed_class_from_list: () => (/* binding */ set_block_prefixed_class_from_list),
/* harmony export */   set_class_from_list: () => (/* binding */ set_class_from_list),
/* harmony export */   set_prefixed_class_from_list: () => (/* binding */ set_prefixed_class_from_list),
/* harmony export */   toggle_block_class: () => (/* binding */ toggle_block_class),
/* harmony export */   toggle_class: () => (/* binding */ toggle_class)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);


const sanitize_classes = function (classes) {
  // Convert classes to a string if needed
  if (typeof classes === 'undefined') classes = '';else if (Array.isArray(classes)) classes = classes.join(' ');else if (typeof classes !== 'string') classes = '';
  if (classes !== '') {
    // Remove extra spaces in the middle
    classes = classes.replace(/  +/g, ' ');

    // Remove spaces at the beginning and end
    classes = classes.trim();
  }
  return classes;
};
const classes_to_array = function (classes) {
  let list;
  classes = sanitize_classes(classes);

  // Convert to array, split at each space
  if (typeof classes === 'string') {
    list = classes.split(' ');
  } else if (typeof classes === 'object') {
    list = classes;
  } else {
    list = [];
  }

  // Remove empty items
  if (!!list) {
    list.filter(value => {
      return value.trim() !== "";
    });
  }
  return list;
};
const has_class = function (classes, selected_class, choices = null) {
  // classes: string or array of classes from the block: "button-color--light is-style-outline"
  // selected_class: the class being checked for: "is-style-outline"
  // choices: optional array of choices that can be selected

  if (choices === null) {
    // Simply check if a class is selected
    let classList = classes_to_array(classes);
    return classList.includes(selected_class);
  } else {
    // Check if a class is selected that exists in the list of choices
    return get_class_from_choices(classes, choices) === selected_class;
  }
};
const get_class_from_choices = function (classes, choices) {
  let classList = classes_to_array(classes);
  let found_class = false;
  let has_empty_choice = false;
  choices.forEach(choice => {
    if (!choice.value) {
      has_empty_choice = true;
    } else if (classList.includes(choice.value)) {
      found_class = choice.value;
    }
  });

  // If no choice was selected by a blank value is available, return an empty string
  if (!found_class && has_empty_choice) {
    return '';
  }
  return found_class;
};
const add_class = function (classes, new_class) {
  classes = sanitize_classes(classes);
  if (!has_class(classes, new_class)) {
    if (classes) classes += ' ';
    classes += new_class;
  }
  return classes;
};
const remove_class = function (classes, new_class) {
  classes = sanitize_classes(classes);
  let classList = classes_to_array(classes);
  classList.forEach(class_name => {
    if (class_name === new_class) {
      classes = classes.replace(class_name, '');
    }
  });
  return sanitize_classes(classes);
};

/**
 * Check if a class exists with a given prefix, and returns the value following the prefix.
 * If the prefix is "gap-" and classes contain "gap-20", returns "20".
 */
const get_class_prefix_value = function (classes, prefix, has_value_class = '') {
  // If "has_value_class" is used, require that class also be set.
  if (!!has_value_class && !has_class(classes, has_value_class)) {
    return false;
  }
  classes = sanitize_classes(classes);

  // let matched = classes.match(/(^| )gap-(\d+|none)( |$)/)[2];

  let pattern = new RegExp('(^| )' + prefix + '([^ ]+)( |$)');
  let matched = classes.match(pattern);
  return matched !== null ? matched[2] : false;
};

/**
 * Remove all classes with a given prefix, except for the one specified as "keep_class"
 */
const remove_prefixed_classes = function (classes, prefix, keep_class = false) {
  classes = sanitize_classes(classes);
  let classList = classes_to_array(classes);

  // Check each class
  classList.forEach(class_name => {
    // Does this class have the prefix?
    if (class_name.indexOf(prefix) === 0) {
      // Should it be kept?
      if (keep_class === false || class_name !== keep_class) {
        classes = remove_class(classes, class_name);
      }
    }
  });
  return classes;
};

/**
 * Add or remove a class based on a value.
 */
const toggle_class = function (classes, new_class, value) {
  classes = sanitize_classes(classes);
  if (!!value) {
    return add_class(classes, new_class);
  } else {
    return remove_class(classes, new_class);
  }
};

/**
 * Set the class to the selected value, clearing any other classes with the same prefix.
 * Optionally toggles the "has_value_class" based on if a value is set.
 * @todo: Add support for blockType for each choice ?
 */
const set_prefixed_class_from_list = function (classes, choices, value, prefix, has_value_class = '') {
  // classes: String list of current classes
  // choices: Array of values, each item must have a ".className" property
  // value: Current value
  // prefix: Prefix for class name, if value is non-empty
  // has_value_class: Optional. If a value is present, this class is also added

  classes = sanitize_classes(classes);

  // "" removes the class.
  // all other values, including "0" or "none", are added with a prefix.
  let new_class = value === "" ? false : prefix + value;

  // Remove other classes with the same prefix
  classes = remove_prefixed_classes(classes, prefix, new_class);
  if (new_class) {
    // Add
    classes = add_class(classes, new_class);
    if (has_value_class) classes = add_class(classes, has_value_class);
  } else {
    // Remove
    classes = remove_class(classes, new_class);
    if (has_value_class) classes = remove_class(classes, has_value_class);
  }
  classes = sanitize_classes(classes);
  return classes;
};

/**
 * Set the class to the selected value, clearing other classes from the list of choices.
 * Does not support multiple values.
 * Optionally toggles the "has_value_class" based on if a value is set.
 */
const set_class_from_list = function (classes, choices, value, has_value_class = '') {
  // classes: String list of current classes
  // choices: Array of choices, each item must have a ".className" property
  // value: Current value

  // Get string of all classes
  classes = sanitize_classes(classes);

  // Get the new class being added
  let new_class = value || '';

  // Remove the class for each other option
  choices.forEach(choice => {
    if (!choice.value) return;

    // Keep the class if it's the one we are adding
    if (choice.value === new_class) return;

    // If this class is assigned, remove it
    if (has_class(classes, choice.value)) {
      classes = remove_class(classes, choice.value);
    }
  });

  // Add or remove the "has_value_class" if there is a value
  if (!!has_value_class) {
    classes = toggle_class(classes, has_value_class, !!new_class);
  }

  // Add the new class
  classes = add_class(classes, new_class);
  return classes;
};

/**
 * Set the classes of a block to the selected value, clearing any other classes with the same prefix.
 * Optionally toggles the "has_value_class" based on if a value is set.
 */
const set_block_prefixed_class_from_list = function (props, choices, value, prefix, has_value_class = '') {
  props.setAttributes({
    className: set_prefixed_class_from_list(props.attributes.className, choices, value, prefix, has_value_class)
  });
};

/**
 * Set the classes to the selected value for a block, clearing other classes from the list of choices.
 * Does not support multiple values.
 * Optionally toggles the "has_value_class" based on if a value is set.
 */
const toggle_block_class = function (props, new_class, value) {
  props.setAttributes({
    className: toggle_class(props.attributes.className, new_class, value)
  });
};

/**
 * Set the classes to the selected value for a block, clearing other classes from the list of choices.
 * Does not support multiple values.
 * Optionally toggles the "has_value_class" based on if a value is set.
 */
const set_block_class_from_list = function (props, choices, value, has_value_class = '', allow_null = false) {
  if (allow_null && value === get_class_from_choices(props.attributes.className, choices)) {
    value = '';
  }
  props.setAttributes({
    className: set_class_from_list(props.attributes.className, choices, value, has_value_class)
  });
};

/**
 * Return an array of classes from the list of choices which are located in the class list.
 * If "has_value_class" is provided, only returns classes if that class is also present.
 * @todo: Add support for blockType for each choice ?
 */
const find_class_from_list = function (classes, choices, has_value_class = null) {
  // classes: String list of current classes
  // choices: Array of choices, each item must have a ".className" property
  // value: Current value

  // If using "has_value_class", require that class to be present as well.
  if (has_value_class !== null && !has_class(classes, has_value_class)) {
    return false;
  }

  // Get the list of classes
  let classList = classes_to_array(classes);
  let found_classes = [];

  // Check each class
  classList.forEach(class_name => {
    // Check each choice
    choices.forEach(choice => {
      if (class_name === choice.value) {
        found_classes.push(class_name);
      }
    });
  });
  return found_classes;
};

/**
 * Check if a block supports a given block type.
 * @param {string} block_name - The block name to check
 * @param {string|array} supported_blocks - The block type or types that are supported
 * @return {boolean} - True if the block is supported
 */
const is_block_type_supported = function (block_name, supported_blocks) {
  if (supported_blocks === 'all') {
    return true;
  } else if (is_string_in_list(block_name, supported_blocks)) {
    return supported_blocks.includes(block_name);
  } else {
    return false;
  }
};

/**
 * Returns choices that match the block settings, by checking the blockType against each option.
 */
const filter_block_choices = function (props, choices) {
  let block_name = props.name;
  let filtered_choices = [];

  // Loop through each choice and check if the blockType is supported
  choices.forEach(choice => {
    // Check if the block is supported
    let block_supported = is_block_type_supported(block_name, choice.blockType);

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
    if (block_supported) {
      filtered_choices.push(choice);
    }
  });
  return filtered_choices;
};

/**
 * Check if a string value exists in an array of strings, or matches a single string.
 * If the input value is empty, returns false.
 */
const is_string_in_list = function (value, list) {
  // Value must be a non-empty string
  if (typeof value !== 'string' || value === '') return false;

  // List may be an array of strings or a single string
  if (Array.isArray(list)) {
    return list.includes(value);
  } else {
    return value === list;
  }
};

/**
 * Check if a block supports container styles.
 */
const does_block_support_container_styles = name => {
  return is_string_in_list(name, ['core/group', 'core/columns', 'core/cover']);
};

/**
 * Check if a block supports mobile settings.
 */
const does_block_support_mobile = name => {
  return is_string_in_list(name, ['core/group', 'core/columns', 'core/buttons', 'core/navigation']);
};

/**
 * Check if a block supports button sizes.
 */
const does_block_support_button_styles = name => {
  return is_string_in_list(name, ['core/button', 'sbi/company-tags', 'sbi/company-actions', 'sbi/company-review-button']);
};

/**
 * Return a button grid component for the block editor, displaying a list of buttons that each toggle a class.
 * @todo: Add support for blockType for each choice
 */
const get_button_grid_field = function (props, choices, name, label = '', cols = '', allow_null = true) {
  let debug = function (choice) {
    console.log('button group', {
      value: choice.value,
      choice: choice,
      is_primary: has_class(props.attributes.className, choice.value),
      is_secondary: !has_class(props.attributes.className, choice.value)
    });
  };
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.BaseControl, {
    label: label,
    className: `rs-utility-blocks--button-group--container rs-utility-blocks--button-group--${name}`
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.ButtonGroup, {
    className: "rs-utility-blocks--button-group",
    "data-cols": cols
  }, choices.map(choice => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
    isPrimary: has_class(props.attributes.className, choice.value, choices),
    isSecondary: !has_class(props.attributes.className, choice.value, choices),
    onClick: () => {
      set_block_class_from_list(props, choices, choice.value, '', allow_null);
      debug(choice);
    },
    className: choice.value
  }, choice.label))));
};

/**
 * Return a select dropdown component for the block editor, displaying a list of options which toggle a class when selected.
 */
const get_dropdown_field = function (props, choices, name, label = '') {
  const filtered_choices = filter_block_choices(props, choices);
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.SelectControl, {
    className: `rs-utility-blocks--dropdown rs-utility-blocks--dropdown--${name}`,
    label: label,
    value: get_class_from_choices(props.attributes.className, filtered_choices),
    options: filtered_choices,
    onChange: value => {
      set_block_class_from_list(props, filtered_choices, value, '', true);
    }
  });
};

/**
 * Return a list of checkboxes as a component for the block editor
 * @param {object} props - The block props
 * @param {array} choices - An array of objects, each with a "value" and "label" property
 * @param {string} any_selection_class - An optional class to add if any choices are selected
 * @param {string} multiple_selection_class - An optional class to add if multiple choices are selected
 */
const get_checkbox_field = function (props, choices, any_selection_class = '', multiple_selection_class = '') {
  let checkboxes = [];
  choices.forEach(choice => {
    let value = choice.value;
    let class_name = value;
    let label = choice.label;
    let excluded_classes = choice.exclude || [];
    let onChange = value => {
      let classes = sanitize_classes(props.attributes.className);
      if (!value) {
        // If not selected, remove the class
        classes = remove_class(classes, class_name);
      } else {
        // If selected, add the class
        classes = add_class(classes, class_name);

        // Remove classes that should be excluded
        if (excluded_classes.length) excluded_classes.forEach(exclude_class => {
          classes = remove_class(classes, exclude_class);
        });
      }

      // Toggle the any_selection_class if any choices are selected
      if (any_selection_class || multiple_selection_class) {
        // Get any classes that are part of the choices
        let selected_choice_classes = find_class_from_list(classes, choices);

        // If any classes are selected, add the any_selection_class
        if (any_selection_class) {
          if (selected_choice_classes.length) {
            classes = add_class(classes, any_selection_class);
          } else {
            classes = remove_class(classes, any_selection_class);
          }
        }

        // If multiple classes are selected, add the multiple_selection_class
        if (multiple_selection_class) {
          if (selected_choice_classes.length > 1) {
            classes = add_class(classes, multiple_selection_class);
          } else {
            classes = remove_class(classes, multiple_selection_class);
          }
        }
      }

      // Save the classes
      props.setAttributes({
        className: classes
      });
    };
    checkboxes.push((0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.ToggleControl, {
      label: label,
      checked: has_class(props.attributes.className, value),
      onChange: onChange
    }));
  });
  return checkboxes;
};

/***/ }),

/***/ "./assets/scripts/src/settings/visibility.js":
/*!***************************************************!*\
  !*** ./assets/scripts/src/settings/visibility.js ***!
  \***************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _includes_utilities__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../includes/utilities */ "./assets/scripts/src/includes/utilities.js");
/* harmony import */ var _wordpress_dom_ready__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/dom-ready */ "@wordpress/dom-ready");
/* harmony import */ var _wordpress_dom_ready__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_dom_ready__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/hooks */ "@wordpress/hooks");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__);

// Custom utilities


// Dependencies




// Elements



_wordpress_dom_ready__WEBPACK_IMPORTED_MODULE_2___default()(() => {
  const registerVisibilityField = (0,_wordpress_compose__WEBPACK_IMPORTED_MODULE_3__.createHigherOrderComponent)(BlockEdit => {
    const any_selection_class = 'has-visibility';
    const multiple_selection_class = 'has-multiple-visibility';
    const choices = [{
      value: 'hide-always',
      label: 'Hide everywhere',
      exclude: []
    }, {
      value: 'hide-on-mobile',
      label: 'Hide on mobile',
      exclude: ['hide-on-desktop']
    }, {
      value: 'hide-on-desktop',
      label: 'Hide on desktop',
      exclude: ['hide-on-mobile']
    }, {
      value: 'hide-if-logged-in',
      label: 'Hide if logged in',
      exclude: ['hide-if-not-logged-in']
    }, {
      value: 'hide-if-not-logged-in',
      label: 'Hide if logged not logged in',
      exclude: ['hide-if-logged-in']
    }, {
      value: 'hide-if-user-admin',
      label: 'Hide for admins',
      exclude: ['hide-if-user-not-admin']
    }, {
      value: 'hide-if-user-not-admin',
      label: 'Hide for non-admins',
      exclude: ['hide-if-user-admin']
    }];
    return props => {
      const {
        name,
        setAttributes,
        isSelected
      } = props;
      let classes = props.attributes.className || '';
      let selected_classes = (0,_includes_utilities__WEBPACK_IMPORTED_MODULE_1__.find_class_from_list)(classes, choices);
      let conditionsEnabled = selected_classes.length > 0;
      return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__.Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(BlockEdit, {
        ...props
      }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__.InspectorControls, null, isSelected && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__.PanelBody, {
        title: "Visibility",
        initialOpen: conditionsEnabled
      }, (0,_includes_utilities__WEBPACK_IMPORTED_MODULE_1__.get_checkbox_field)(props, choices, any_selection_class, multiple_selection_class))));
    };
  }, 'registerVisibilityField');
  (0,_wordpress_hooks__WEBPACK_IMPORTED_MODULE_4__.addFilter)('editor.BlockEdit', 'rs_utility_blocks/register_visibility_field', registerVisibilityField, 30);
});

/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ ((module) => {

module.exports = window["React"];

/***/ }),

/***/ "@wordpress/block-editor":
/*!*************************************!*\
  !*** external ["wp","blockEditor"] ***!
  \*************************************/
/***/ ((module) => {

module.exports = window["wp"]["blockEditor"];

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ ((module) => {

module.exports = window["wp"]["components"];

/***/ }),

/***/ "@wordpress/compose":
/*!*********************************!*\
  !*** external ["wp","compose"] ***!
  \*********************************/
/***/ ((module) => {

module.exports = window["wp"]["compose"];

/***/ }),

/***/ "@wordpress/dom-ready":
/*!**********************************!*\
  !*** external ["wp","domReady"] ***!
  \**********************************/
/***/ ((module) => {

module.exports = window["wp"]["domReady"];

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ ((module) => {

module.exports = window["wp"]["element"];

/***/ }),

/***/ "@wordpress/hooks":
/*!*******************************!*\
  !*** external ["wp","hooks"] ***!
  \*******************************/
/***/ ((module) => {

module.exports = window["wp"]["hooks"];

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!*******************************************************!*\
  !*** ./assets/scripts/src/rs-utility-block-editor.js ***!
  \*******************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _settings_visibility_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./settings/visibility.js */ "./assets/scripts/src/settings/visibility.js");
// Include functionality

// import './settings/buttons.js';
})();

/******/ })()
;
//# sourceMappingURL=rs-utility-block-editor.js.map