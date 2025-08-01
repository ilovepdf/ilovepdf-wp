/**
 * Get the status container element based on the button trigger.
 *
 * @param {HTMLElement} btnTrigger - The button element that triggered the action.
 * @returns {HTMLElement|null} - The status container element or null if not found.
 * @since 3.0.0
 */
export const getStatusContainer = (btnTrigger) => {
	if (!btnTrigger) {
		return null;
	}

	const container = btnTrigger.parentElement.nextElementSibling ?? btnTrigger.closest('tr');

	if (!container) {
		return null;
	}

	if (container.nodeName === 'TR' || container.classList.contains('ipdf-status-process')) {
		return container;
	}

	container.classList.add('ipdf-status-process');
	return container;
};

export const getFormData = (btnTrigger) => {
	const formData = new FormData();

	if (!btnTrigger) {
		return formData;
	}

	const postId = btnTrigger.getAttribute('data-post-id');
	const action = btnTrigger.getAttribute('data-action');
	const codeNonce = btnTrigger.getAttribute('data-nonce');

	formData.append('action', action);
	formData.append('post_id', postId);
	formData.append('_wpnonce', codeNonce);

	return formData;
};
