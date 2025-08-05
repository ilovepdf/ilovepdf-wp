import './settings';
import { getFormData } from '../common/DOMElements';
import { showAdminNotice } from '../components';

/**
 * Apply a watermark to a file by sending a request to the server.
 *
 * @param {HTMLElement} container - The container element where the status will be displayed.
 * @param {HTMLElement} btnTrigger - The button element that triggered the watermark action.
 * @since 3.0.0
 */
export const applyWatermark = (container, btnTrigger) => {
	const statusWatermarkNotApplied = container.querySelector('.ipdf-item-status-not-watermarked');
	const statusSuccess = container.querySelector('.ipdf-item-status-watermark-applied');
	const statusFail = container.querySelector('.ipdf-item-status-fail');
	statusFail.classList.remove('ipdf-item-status-active');

	// TODO: revisar que la respuesta tenga un true en caso de que el archivo tenga un backup y se pueda restaurar.
	//const btnRestoreFile = container.querySelector('.ipdf-btn--media-action-restore');

	const loading = container.querySelector('.ipdf-item-status-watermark-processing');
	loading?.classList.add('ipdf-item-status-active');

	const formData = getFormData(btnTrigger);

	const options = {
		method: 'POST',
		body: formData
	};

	fetch(ajaxurl, options)
		.then((response) => response.json())
		.then((response) => {
			const { success, data } = response;

			loading?.classList.remove('ipdf-item-status-active');

			if (!success && typeof data === 'string') {
				statusFail?.classList.add('ipdf-item-status-active');
				btnTrigger.classList.remove('ipdf-btn--media-action-trigger');
				showAdminNotice(data, 'error');
			}

			if (success) {
				statusSuccess?.classList.add('ipdf-item-status-active');
				statusWatermarkNotApplied?.classList.remove('ipdf-item-status-active');

				switch (typeof data) {
					case 'string':
						showAdminNotice(data);
						break;

					case 'object':
						showAdminNotice(data.message);
						break;

					default:
						showAdminNotice('The watermark was applied correctly.');
						break;
				}
			}
		})
		.catch((error) => {
			showAdminNotice(error.data, 'error');
			console.error(error);
		});
};
