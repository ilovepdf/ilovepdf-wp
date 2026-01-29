/**
 * Adds and removes a CSS class to create a pulse animation effect on an element.
 *
 * @param {HTMLElement} element - The DOM element to apply the pulse animation to
 * @returns {void}
 */
export function pulseAnimation(element) {
	if (!element.classList.contains('ipdf-btn--need-saving')) {
		setTimeout(() => {
			element.classList.add('ipdf-btn--need-saving');
		}, 1000);

		setTimeout(() => {
			element.classList.remove('ipdf-btn--need-saving');
		}, 5000);
	}
}
