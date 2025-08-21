import { showAdminNotice } from '../components';
import { getFormData, getRowCompressedSize, getRowOriginalSize } from '../common/DOMElements';
import {
	setFilesProcessed,
	setAverageReduction,
	setSpaceSaved,
	setResume,
	setSize
} from './statistics';

/**
 * Compress a file by sending a request to the server.
 * @param {HTMLElement} container - The container element where the status will be displayed.
 * @param {HTMLElement} btnTrigger - The button element that triggered the compression action.
 * @since 3.0.0
 */
export const compressFile = (container, btnTrigger) => {
	const statusNotCompressed = container.querySelector('.ipdf-item-status-not-compressed');
	statusNotCompressed?.classList.remove('ipdf-item-status-active');
	const statusSuccess = container.querySelector('.ipdf-item-status-compressed');
	const statusFail = container.querySelector('.ipdf-item-status-fail');
	statusFail.classList.remove('ipdf-item-status-active');
	const colCompressedSize = getRowCompressedSize(container);
	const colOriginalSize = getRowOriginalSize(container);

	const btnRestoreFile = container.parentElement.querySelector('.ipdf-btn--media-action-restore');

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
				statusNotCompressed?.classList.add('ipdf-item-status-active');
				statusFail?.classList.add('ipdf-item-status-active');
				btnTrigger.classList.remove('ipdf-btn--media-action-trigger');
				showAdminNotice(data, 'error');
			}

			if (success) {
				statusSuccess?.classList.add('ipdf-item-status-active');

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
							const {
								files_processed,
								average_reduction,
								space_saved,
								total_resume,
								compressed_size,
								original_size
							} = data.data;

							setFilesProcessed(files_processed);
							setAverageReduction(average_reduction);
							setSpaceSaved(space_saved);
							setResume(total_resume);
							setSize(compressed_size, colCompressedSize);
							setSize(original_size, colOriginalSize);
						}

						if (data.data.backup) {
							btnRestoreFile.classList.add('ipdf-btn--media-action-restore-active');
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
