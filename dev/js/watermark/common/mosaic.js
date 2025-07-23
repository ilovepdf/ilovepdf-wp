import { POSITION_ACCEPTED, resetPosition } from './position';
import { updateElementCoordinates } from './transform';

/**
 * Creates a mosaic pattern by cloning and positioning an element in 9 different positions.
 *
 * The function removes the original element and creates clones positioned according to
 * the POSITION_ACCEPTED array.
 *
 * @param {HTMLElement} elementPreview - The element to be cloned and positioned in a mosaic pattern
 * @returns {void}
 */
export const generateMosaic = (elementPreview) => {
	const elementWatermarkContainer = document.querySelector('#ipdf-element-watermark');

	POSITION_ACCEPTED.forEach((position) => {
		const newElement = elementPreview.cloneNode(true);
		newElement.classList.add('ipdf-element-clone');
		resetPosition(newElement);

		updateElementCoordinates(newElement, position);

		elementWatermarkContainer.appendChild(newElement);
	});

	elementPreview.hidden = true;
};

/**
 * Checks if the mosaic mode is active.
 *
 * @returns {boolean} True if the mosaic mode is active, false otherwise.
 */
export const isMosaicActive = () => document.querySelector('#ipdf_option_mosaic').checked;
