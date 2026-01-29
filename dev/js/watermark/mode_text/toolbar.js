import ColorPicker from '../../lib/colorpicker.min.js';
import '../../lib/colorpicker.min.css';
import { getPreview } from '../common/preview';
import { isMosaicActive } from '../common/mosaic.js';

/**
 * Array of font families that do not support font style modifications (bold/italic).
 * @constant {string[]}
 */
const FFAMILY_STYLE_OFF = [
	'Courier',
	'Comic Sans MS',
	'WenQuanYi Zen Hei',
	'Lohit Marathi',
	'Arial Unicode MS'
];

/**
 * Resets the font style options UI to default state.
 * - Hides the "font style not available" message.
 * - Enables all font style radio buttons.
 * - Makes radio buttons clickable.
 * @returns {void}
 */
const resetStyleOptions = () => {
	document.querySelector('.ilovepdf_font_none_style').style.display = 'none';
	document.querySelectorAll('input.ipdf-input-font-style').forEach((item) => {
		item.disabled = false;
		item.style.cursor = 'pointer';
	});
};

/**
 * Checks if a font family is in the list of fonts that don't support styling.
 * @param {string} font - The font family name to check.
 * @returns {boolean} True if the font is in the restricted list, false otherwise.
 */
const isFontAllowed = (font) => {
	return FFAMILY_STYLE_OFF.includes(font.split('"').join('').trim());
};

/**
 * Disables font style options in the UI.
 * - Shows the "font style not available" message.
 * - Disables and unchecks all font style radio buttons.
 * - Sets cursor to not-allowed.
 * - Resets font weight and style to normal.
 * @param {HTMLElement} element - The element whose font styles will be reset.
 * @returns {void}
 */
const disableStyleOptions = (element) => {
	document.querySelector('.ilovepdf_font_none_style').style.display = 'block';
	document.querySelectorAll('input.ipdf-input-font-style').forEach((item) => {
		item.disabled = true;
		item.checked = false;
		item.style.cursor = 'not-allowed';
		element.style.fontWeight = 'normal';
		element.style.fontStyle = 'normal';
	});
};

/**
 * Updates the font weight and style of an element based on the provided input ID and value.
 *
 * @param {HTMLElement} element - The element whose font style will be updated.
 * @param {string} inputId - The ID of the input element that triggered the update ('ipdf_option_font_style_bold' or 'ipdf_option_font_style_italic').
 * @param {string} value - The new font style value to apply ('bold' or 'italic').
 * @returns {void}
 */
export const updateFontStyle = (element, inputId, value) => {
	if (inputId === 'ipdf_option_font_style_bold') {
		element.style.fontWeight = value.toLowerCase();
		element.style.fontStyle = 'normal';
	}

	if (inputId === 'ipdf_option_font_style_italic') {
		element.style.fontStyle = value.toLowerCase();
		element.style.fontWeight = 'normal';
	}

	if (inputId === 'ipdf_option_font_style_normal') {
		element.style.fontStyle = 'normal';
		element.style.fontWeight = 'normal';
	}
};

/**
 * Update Font Family value
 *
 * @param {HTMLElement} element - The element whose transform will be updated.
 * @param {string} value - The new font family value.
 * @returns {void}
 */
export const updatedFontFamily = (element, value) => {
	element.style.fontFamily = value;

	resetStyleOptions();

	if (isFontAllowed(element.style.fontFamily)) {
		disableStyleOptions(element);
	}
};

/**
 * Updates the font size of an element by setting its fontSize style property.
 *
 * @param {HTMLElement} element - The element whose font size will be updated.
 * @param {number|string} value - The new font size value in pixels.
 * @returns {void}
 */
export const updateFontSize = (element, value) => {
	element.style.fontSize = `${value}px`;
};

/**
 * Sets up a color picker for updating text color of a preview element.
 *
 * Initializes event listener on color picker button that opens a color picker dialog.
 * When a color is selected, updates both the color input value and preview element's text color.
 *
 * @param {HTMLElement} preview - The preview element whose text color will be updated
 * @returns {void}
 */
export const updateTextColor = (preview) => {
	document.querySelector('#ipdf-color-picker').addEventListener('click', (e) => {
		e.preventDefault();

		const picker = new ColorPicker('#ipdf-color-picker', {
			submitMode: 'instant',
			headless: true,
			formats: false,
			enableAlpha: false
		});

		picker.prompt();
		picker.on('pick', (color) => {
			if (!color) {
				return;
			}

			const colorPick = color.string('hex');
			const inputColor = document.getElementById('ipdf_option_font_color');

			inputColor.value = colorPick;

			if (isMosaicActive()) {
				const { texts } = getPreview('', true);
				texts.forEach((element) => {
					element.style.color = colorPick;
				});
			}

			preview.style.color = colorPick;
		});
	});
};

/**
 * Updates the text content of an element.
 *
 * @param {HTMLElement} element - The element whose text content will be updated.
 * @param {string} value - The new text content to set.
 * @returns {void}
 */
export const updateTextPreview = (element, value) => {
	element.textContent = value;
};
