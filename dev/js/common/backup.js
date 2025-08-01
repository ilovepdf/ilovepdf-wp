import { _x, sprintf } from '@wordpress/i18n';
import { createDialogComponent, showAdminNotice } from '../components';
import { getStatusContainer } from './DOMElements';

const btnRestoreAll = document.getElementById('ilovepdf_restore_all');
const btnClearBackup = document.getElementById('ilovepdf_clear_backup');
const bodyContent = document.querySelector('#wpbody-content');

const removeAllStatus = (btnTrigger) => {
	const statusContainer = getStatusContainer(btnTrigger);
	const allStatus = statusContainer.querySelectorAll('.ipdf-item-status');

	allStatus?.forEach((status) => {
		status.classList.remove('ipdf-item-status-active');
	});
};

btnRestoreAll?.addEventListener('click', function (e) {
	e.preventDefault();

	const currentTarget = e.currentTarget;
	const contentDialog = _x(
		'The changes applied by all the tools will be lost. Do you want to continue?',
		'body content dialog box',
		'ilove-pdf'
	);
	const dialogComponent = createDialogComponent(contentDialog);

	currentTarget.insertAdjacentHTML('afterend', dialogComponent);

	const dialogElem = document.getElementById('ipdf-restore-dialog');
	const btnConfirmDialog = document.getElementById('ilovepdf-dialog-aceptted');
	const btnCloseDialog = document.getElementById('ilovepdf-dialog-close');

	dialogElem.showModal();

	btnConfirmDialog.addEventListener('click', (e) => {
		e.preventDefault();
		dialogElem.close();
		dialogElem.remove();

		const formData = new FormData();
		const codeNonce = document.querySelector('.ilovepdf-settings__main #_wpnonce');

		formData.append('action', 'ilovepdf_restore_all_files');
		formData.append('_wpnonce', codeNonce?.value);

		const options = {
			method: 'POST',
			body: formData
		};

		fetch(ajaxurl, options)
			.then((response) => response.json())
			.then((response) => {
				const { success, data } = response;

				if (!success && typeof data === 'object') {
					for (const file of data.files_errors) {
						showAdminNotice(file.message, 'error');
					}
				} else if (typeof data === 'string') {
					showAdminNotice(data, 'error');
				}

				if (success) {
					if (data.files_restored) {
						showAdminNotice(data.files_restored);
					}

					if (data.files_errors) {
						for (const file of data.files_errors) {
							showAdminNotice(file.message, 'error');
						}
					}
				}
			})
			.catch((error) => {
				showAdminNotice(error.data, 'error');
				console.error(error);
			});
	});

	btnCloseDialog.addEventListener('click', (e) => {
		e.preventDefault();
		dialogElem.close();
		dialogElem.remove();
	});
});

btnClearBackup?.addEventListener('click', function (e) {
	e.preventDefault();

	const currentTarget = e.currentTarget;
	const contentDialog = sprintf(
		_x(
			'All files inside %1$s folder will be deleted. Do you want to continue?',
			'body content dialog box',
			'ilove-pdf'
		),
		'wp-content/uploads/ilovepdf/backup'
	);
	const dialogComponent = createDialogComponent(contentDialog);

	currentTarget.insertAdjacentHTML('afterend', dialogComponent);

	const dialogElem = document.getElementById('ipdf-restore-dialog');
	const btnConfirmDialog = document.getElementById('ilovepdf-dialog-aceptted');
	const btnCloseDialog = document.getElementById('ilovepdf-dialog-close');

	dialogElem.showModal();

	btnConfirmDialog.addEventListener('click', (e) => {
		e.preventDefault();
		dialogElem.close();
		dialogElem.remove();

		const formData = new FormData();
		const codeNonce = document.querySelector('.ilovepdf-settings__main #_wpnonce');

		formData.append('action', 'ilovepdf_clear_backup');
		formData.append('_wpnonce', codeNonce?.value);

		const options = {
			method: 'POST',
			body: formData
		};

		fetch(ajaxurl, options)
			.then((response) => response.json())
			.then((response) => {
				const { success, data } = response;

				if (!success && typeof data === 'string') {
					showAdminNotice(data, 'error');
				}

				if (success) {
					showAdminNotice(data);
				}
			})
			.catch((error) => {
				showAdminNotice(error.data, 'error');
				console.error(error);
			});
	});

	btnCloseDialog.addEventListener('click', (e) => {
		e.preventDefault();
		dialogElem.close();
		dialogElem.remove();
	});
});

bodyContent?.addEventListener('click', function (event) {
	if (event.target.classList.contains('ipdf-btn--media-action-restore')) {
		event.preventDefault();

		const btnTrigger = event.target;
		btnTrigger.classList.remove('ipdf-btn--media-action-restore-active');

		const statusContainer = getStatusContainer(btnTrigger);

		const statusSuccess = statusContainer.querySelector('.ipdf-item-status-restored');
		const statusFail = statusContainer.querySelector('.ipdf-item-status-fail');
		const loading = statusContainer.querySelector('.ipdf-item-status-processing-restore');

		const contentDialog = _x(
			'The changes applied by all the tools will be lost. Do you want to continue?',
			'body content dialog box',
			'ilove-pdf'
		);
		const dialogComponent = createDialogComponent(contentDialog);

		statusContainer.insertAdjacentHTML('afterend', dialogComponent);

		const dialogElem = document.getElementById('ipdf-restore-dialog');
		const btnConfirmDialog = document.getElementById('ilovepdf-dialog-aceptted');
		const btnCloseDialog = document.getElementById('ilovepdf-dialog-close');

		dialogElem.showModal();

		btnConfirmDialog.addEventListener('click', (e) => {
			e.preventDefault();
			dialogElem.close();
			dialogElem.remove();

			removeAllStatus(btnTrigger);
			loading?.classList.add('ipdf-item-status-active');

			const postId = btnTrigger.getAttribute('data-post-id');
			const action = btnTrigger.getAttribute('data-action');
			const codeNonce = btnTrigger.getAttribute('data-nonce');

			const formData = new FormData();
			formData.append('action', action);
			formData.append('post_id', postId);
			formData.append('_wpnonce', codeNonce);

			const options = {
				method: 'POST',
				body: formData
			};

			fetch(ajaxurl, options)
				.then((response) => response.json())
				.then((response) => {
					const { success, data } = response;

					if (!success && typeof data === 'string') {
						showAdminNotice(data, 'error');
						statusFail?.classList.add('ipdf-item-status-active');
						btnTrigger.classList.add('ipdf-btn--media-action-restore-active');
						loading?.classList.remove('ipdf-item-status-active');
					}

					if (success && typeof data === 'string') {
						showAdminNotice(data);
						statusSuccess?.classList.add('ipdf-item-status-active');
						btnTrigger.classList.remove('ipdf-btn--media-action-restore-active');
						loading?.classList.remove('ipdf-item-status-active');

						setTimeout(() => {
							location.reload();
						}, 3000);
					}
				})
				.catch((error) => {
					statusFail?.classList.add('ipdf-item-status-active');
					btnTrigger.classList.add('ipdf-btn--media-action-restore-active');
					loading?.classList.remove('ipdf-item-status-active');
					showAdminNotice(error.data, 'error');
					console.error(error);
				});
		});

		btnCloseDialog.addEventListener('click', (e) => {
			e.preventDefault();
			dialogElem.close();
			dialogElem.remove();
			loading?.classList.remove('ipdf-item-status-active');
			btnTrigger.classList.add('ipdf-btn--media-action-restore-active');
		});
	}
});
