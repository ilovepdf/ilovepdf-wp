import './settings';
import { getFormData, getRowCompressedSize, getRowOriginalSize } from '../common/DOMElements';
import { showAdminNotice } from '../components';
import { setFilesProtected, setResume } from './statistics';
import {
	setAverageReduction,
	setSize,
	setFilesProcessed,
	setSpaceSaved,
	setResume as setCompressResume
} from '../compress/statistics';

/**
 * Apply a watermark to a file by sending a request to the server.
 *
 * @param {HTMLElement} container - The container element where the status will be displayed.
 * @param {HTMLElement} btnTrigger - The button element that triggered the watermark action.
 * @since 3.0.0
 */
export const applyWatermark = (container, btnTrigger) => {
	const statusWatermarkNotApplied = container.querySelector('.ipdf-item-status-not-watermarked');
	statusWatermarkNotApplied?.classList.remove('ipdf-item-status-active');
	const statusSuccess = container.querySelector('.ipdf-item-status-watermark-applied');
	const statusCompressed = container.querySelector('.ipdf-item-status-compressed');
	const statusFail = container.querySelector('.ipdf-item-status-fail');
	statusFail.classList.remove('ipdf-item-status-active');
	const colCompressedSize = getRowCompressedSize(container);
	const colOriginalSize = getRowOriginalSize(container);

	const btnRestoreFile = container.querySelector('.ipdf-btn--media-action-restore');
	let isFileBackup = false;

	if (
		btnRestoreFile &&
		btnRestoreFile.classList.contains('ipdf-btn--media-action-restore-active')
	) {
		isFileBackup = true;
	}

	if (btnRestoreFile) {
		btnRestoreFile.classList.remove('ipdf-btn--media-action-restore-active');
	}

	const loading = container.querySelector('.ipdf-item-status-watermark-processing');
	loading?.classList.add('ipdf-item-status-active');

	const btnCompress = container.querySelector('#ipdf-action-compress');
	let isFileCompressed = false;

	// If is loading disables the compress button
	if (btnCompress && loading) {
		btnCompress.classList.add('ipdf-btn--media-action-trigger');
	}
	// Check if the compress button was already disabled
	if (btnCompress && btnCompress.classList.contains('ipdf-btn--media-action-trigger')) {
		isFileCompressed = true;
	}

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

			if (!success) {
				switch (typeof data) {
					case 'string':
						showAdminNotice(data, 'error');
						break;

					case 'object':
						if (data.message) {
							showAdminNotice(data.message, 'error');
						}

						break;
				}

				statusFail?.classList.add('ipdf-item-status-active');
				statusWatermarkNotApplied?.classList.add('ipdf-item-status-active');
				btnTrigger.classList.remove('ipdf-btn--media-action-trigger');
			}

			if (success) {
				statusSuccess?.classList.add('ipdf-item-status-active');
				btnTrigger.classList.add('ipdf-btn--media-action-trigger');

				switch (typeof data) {
					case 'string':
						showAdminNotice(data);
						break;

					case 'object':
						const params = new URL(window.location.href).searchParams;

						if (params.get('page') === 'ipdf-media-optimization') {
							const { files_protected, resume, file_is_compressed } = data.data;
							setFilesProtected(files_protected);
							setResume(resume);

							if (file_is_compressed) {
								const {
									files_processed,
									average_reduction,
									space_saved,
									total_resume,
									compressed_size,
									original_size,
									percentage
								} = data.data.compress_statistics;

								statusCompressed.querySelector('span').textContent = percentage;

								setFilesProcessed(files_processed);
								setAverageReduction(average_reduction);
								setSpaceSaved(space_saved);
								setCompressResume(total_resume);
								setSize(compressed_size, colCompressedSize);
								setSize(original_size, colOriginalSize);
							}
						}

						if (data.data.backup) {
							btnRestoreFile.classList.add('ipdf-btn--media-action-restore-active');
						}

						showAdminNotice(data.message);
						break;

					default:
						showAdminNotice('The watermark was applied correctly.');
						break;
				}
			}

			if (btnCompress && !isFileCompressed) {
				btnCompress.classList.remove('ipdf-btn--media-action-trigger');
			}

			if (btnRestoreFile && isFileBackup) {
				btnRestoreFile.classList.add('ipdf-btn--media-action-restore-active');
			}
		})
		.catch((error) => {
			showAdminNotice(error.data, 'error');
			loading?.classList.remove('ipdf-item-status-active');

			if (btnCompress && !isFileCompressed) {
				btnCompress.classList.remove('ipdf-btn--media-action-trigger');
			}

			if (btnRestoreFile && isFileBackup) {
				btnRestoreFile.classList.add('ipdf-btn--media-action-restore-active');
			}
			console.error(error);
		});
};
