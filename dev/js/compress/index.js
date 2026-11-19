import { showAdminNotice } from '../components';
import { getFormData } from '../common/DOMElements';
import { applyCompressRowResult, refreshCompressSummary } from '../common/rowStatus';

/**
 * Compress a file by sending a request to the server.
 * @param {HTMLElement} container - The container element where the status will be displayed.
 * @param {HTMLElement} btnTrigger - The button element that triggered the compression action.
 * @since 3.0.0
 */
export const compressFile = (container, btnTrigger) => {
	const statusNotCompressed = container.querySelector('.ipdf-item-status-not-compressed');
	statusNotCompressed?.classList.remove('ipdf-item-status-active');
	const statusFail = container.querySelector('.ipdf-item-status-fail');
	statusFail.classList.remove('ipdf-item-status-active');

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

	const loading = container.querySelector('.ipdf-item-status-compressing');
	loading?.classList.add('ipdf-item-status-active');

	const btnWatermark = container.querySelector('#ipdf-action-watermark');
	let isFileWatermarked = false;

	// Check if the watermark button was already disabled BEFORE we disable it for loading
	if (btnWatermark && btnWatermark.classList.contains('ipdf-btn--media-action-trigger')) {
		isFileWatermarked = true;
	}

	//If is loading disables the watermark button
	if (btnWatermark && loading) {
		btnWatermark.classList.add('ipdf-btn--media-action-trigger');
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

				statusNotCompressed?.classList.add('ipdf-item-status-active');
				statusFail?.classList.add('ipdf-item-status-active');
				btnTrigger.classList.remove('ipdf-btn--media-action-trigger');
			}

			if (success) {
				applyCompressRowResult(container, typeof data === 'object' ? data.data : null);

				switch (typeof data) {
					case 'string':
						showAdminNotice(data);
						break;

					case 'object':
						refreshCompressSummary(data.data);

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

			if (btnWatermark && !isFileWatermarked) {
				btnWatermark.classList.remove('ipdf-btn--media-action-trigger');
			}

			if (btnRestoreFile && isFileBackup) {
				btnRestoreFile.classList.add('ipdf-btn--media-action-restore-active');
			}
		})
		.catch((error) => {
			showAdminNotice(error.data, 'error');
			loading?.classList.remove('ipdf-item-status-active');

			if (btnWatermark && !isFileWatermarked) {
				btnWatermark.classList.remove('ipdf-btn--media-action-trigger');
			}

			if (btnRestoreFile && isFileBackup) {
				btnRestoreFile.classList.add('ipdf-btn--media-action-restore-active');
			}

			console.error(error);
		});
};
