/**
 * Updates the transform property of an element by adding or replacing
 * a specific transform type ('translate' or 'rotate'), while preserving the other.
 *
 * @param {HTMLElement} element - The element whose transform will be updated.
 * @param {string} type - The type of transform to apply ('translate' or 'rotate').
 * @param {string} newValue - The new transform value (e.g. 'translate(-50%, -50%)' or 'rotate(90deg)').
 * @returns {string} - The updated transform string combining the new and preserved values.
 */
export const updateTransform = (element, type, newValue) => {
	const currentTransform = element.style.getPropertyValue('transform') || '';
	let preservedPart = '';

	if (type === 'translate') {
		const rotateMatch = currentTransform.match(/rotate\(([^)]+)\)/);

		if (rotateMatch) {
			preservedPart = rotateMatch[0];
		}

		return `${newValue} ${preservedPart}`.trim();
	}

	if (type === 'rotate') {
		const translateMatch = currentTransform.match(/translate\(([^)]+)\)/);

		if (translateMatch) {
			preservedPart = translateMatch[0];
		}

		return `${preservedPart} ${newValue}`.trim();
	}

	return newValue;
};

/**
 * Updates the position and transform of a DOM element based on specified position string.
 *
 * @param {HTMLElement} preview - The DOM element to position.
 * @param {string} position - Position string in format "[horizontal] [vertical]" where:
 *                           horizontal can be: 'left', 'center', 'right'
 *                           vertical can be: 'top', 'middle', 'bottom'
 * @returns {void}
 */
export const updateElementCoordinates = (preview, position) => {
	if (position === 'left top') {
		preview.style.left = 0;
		preview.style.top = 0;
		preview.style.transform = updateTransform(preview, 'translate', 'translate(0)');
	}

	if (position === 'center top') {
		preview.style.left = '50%';
		preview.style.top = 0;
		preview.style.transform = updateTransform(preview, 'translate', 'translate(-50%, 0)');
	}

	if (position === 'right top') {
		preview.style.right = 0;
		preview.style.top = 0;
		preview.style.transform = updateTransform(preview, 'translate', 'translate(0)');
	}

	if (position === 'left middle') {
		preview.style.left = 0;
		preview.style.top = '50%';
		preview.style.transform = updateTransform(preview, 'translate', 'translate(0, -50%)');
	}

	if (position === 'center middle') {
		preview.style.left = '50%';
		preview.style.top = '50%';
		preview.style.transform = updateTransform(preview, 'translate', 'translate(-50%, -50%)');
	}

	if (position === 'right middle') {
		preview.style.right = 0;
		preview.style.top = '50%';
		preview.style.transform = updateTransform(preview, 'translate', 'translate(0, -50%)');
	}

	if (position === 'left bottom') {
		preview.style.left = 0;
		preview.style.bottom = 0;
		preview.style.transform = updateTransform(preview, 'translate', 'translate(0)');
	}

	if (position === 'center bottom') {
		preview.style.left = '50%';
		preview.style.bottom = 0;
		preview.style.transform = updateTransform(preview, 'translate', 'translate(-50%, 0)');
	}

	if (position === 'right bottom') {
		preview.style.right = 0;
		preview.style.bottom = 0;
		preview.style.transform = updateTransform(preview, 'translate', 'translate(0)');
	}
};

/**
 * Updates the tooltip text to reflect the current value of a range input.
 *
 * @param {HTMLInputElement} rangeElement - The input of type range.
 * @param {HTMLElement} tooltip - The element displaying the selected value (e.g., a tooltip).
 * @returns {void}
 */
export const updateTooltipValue = (rangeElement, tooltip) => {
	const val = parseInt(rangeElement.value);
	const prefix = tooltip.dataset?.prefix || 'px';

	if (isNaN(val)) {
		return;
	}

	tooltip.textContent = `${val}${prefix}`;
};
