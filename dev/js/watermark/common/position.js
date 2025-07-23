/**
 * Array of valid position strings for element positioning.
 *
 * Each string follows the format "[horizontal] [vertical]" where:
 * - horizontal can be: 'left', 'center', 'right'
 * - vertical can be: 'top', 'middle', 'bottom'
 *
 * @constant {string[]}
 */
export const POSITION_ACCEPTED = [
	'left top',
	'center top',
	'right top',
	'left middle',
	'center middle',
	'right middle',
	'left bottom',
	'center bottom',
	'right bottom'
];

/**
 * Resets all position-related styles of a DOM element.
 *
 * @param {HTMLElement} preview - The DOM element whose position needs to be reset.
 * @returns {void}
 */
export const resetPosition = (preview) => {
	preview.style.inset = 'auto';
	preview.style.top = '';
	preview.style.bottom = '';
	preview.style.left = '';
	preview.style.right = '';
};
