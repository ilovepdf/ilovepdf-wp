import { showAdminNotice } from '../components';
import { getFormData } from '../common/DOMElements';
import { setFilesProcessed, setAverageReduction, setSpaceSaved, setResume } from './statistics';

/**
 * Compress a file by sending a request to the server.
 * @param {HTMLElement} container - The container element where the status will be displayed.
 * @param {HTMLElement} btnTrigger - The button element that triggered the compression action.
 * @since 3.0.0
 */
export const compressFile = (container, btnTrigger) => {
	const statusNotCompressed = container.querySelector('.ipdf-item-status-not-compressed');
	const statusSuccess = container.querySelector('.ipdf-item-status-compressed');
	const statusFail = container.querySelector('.ipdf-item-status-fail');
	statusFail.classList.remove('ipdf-item-status-active');

	// TODO: revisar que la respuesta tenga un true en caso de que el archivo tenga un backup y se pueda restaurar.
	//const btnRestoreFile = container.querySelector('.ipdf-btn--media-action-restore');

	const loading = container.querySelector('.ipdf-item-status-compressing');
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
				statusNotCompressed?.classList.remove('ipdf-item-status-active');

				switch (typeof data) {
					case 'string':
						showAdminNotice(data);
						break;

					case 'object':
						if (data.data.percentage) {
							statusSuccess.querySelector('span').textContent = data.data.percentage;
						}

						const params = new URL(window.location.href).searchParams;

						if (params.get('page') === 'ipdf-media-optimization') {
							setFilesProcessed(data.data.files_processed);
							setAverageReduction(data.data.average_reduction);
							setSpaceSaved(data.data.space_saved);
							setResume(data.data.total_resume);
						}

						showAdminNotice(data.message);
						break;

					default:
						showAdminNotice('File compressed successfully.');
						break;
				}
			}
		})
		.catch((error) => {
			showAdminNotice(error.data, 'error');
			console.error(error);
		});
};
