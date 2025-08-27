import { pulseAnimation } from '../utils';

/**
 * Get the container element based on the button trigger.
 *
 * @param {HTMLElement} btnTrigger - The button element that triggered the action.
 * @returns {HTMLElement|null} - The container element or null if not found.
 * @since 3.0.0
 */
export const getActionsContainer = (btnTrigger) => {
	if (!btnTrigger) {
		return null;
	}

	const container = btnTrigger.closest('tr');

	if (!container) {
		return null;
	}

	if (container.nodeName !== 'TR') {
		return null;
	}

	container.querySelector('.ipdf-status')?.classList.add('ipdf-status-process');
	return container;
};

/**
 * Get the form data from the button trigger element.
 *
 * @param {HTMLElement} btnTrigger - The button element that triggered the action.
 * @returns {FormData} - The FormData object containing the necessary data.
 * @since 3.0.0
 */
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

/**
 * Add pulse animation to the save changes button when the form is changed.
 *
 * @since 3.0.0
 */
export const addAnimationToBtnSaveChanges = () => {
	const btnsSaveChanges = document.querySelectorAll(
		'.ilovepdf-settings__main .ilovepdf-settings__main__section form .ipdf-input-submit'
	);
	const form = document.querySelector(
		'.ilovepdf-settings__main .ilovepdf-settings__main__section form'
	);

	form?.addEventListener('change', function (event) {
		event.preventDefault();

		btnsSaveChanges.forEach((btn) => {
			pulseAnimation(btn);
		});
	});
};

/**
 * Get the compressed size column element from the container.
 *
 * @param {HTMLElement} container - The container element.
 * @returns {HTMLElement|null} - The compressed size column element or null if not found.
 * @since 3.0.0
 */
export const getRowCompressedSize = (container) => {
	if (!container) {
		return null;
	}

	return container.querySelector('.column-size_compressed');
};

/**
 * Get the original size column element from the container.
 *
 * @param {HTMLElement} container - The container element.
 * @returns {HTMLElement|null} - The original size column element or null if not found.
 * @since 3.0.0
 */
export const getRowOriginalSize = (container) => {
	if (!container) {
		return null;
	}

	return container.querySelector('.column-size');
};

/**
 * Get the loading indicator element for the restore backup process.
 *
 * @returns {HTMLElement|null} - The loading indicator element or null if not found.
 * @since 3.0.0
 */
export const getRestoreBackupLoading = () => {
	return document.getElementById('ipdf-loading-backup');
};
