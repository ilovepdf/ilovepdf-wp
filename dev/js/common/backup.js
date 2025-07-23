import { _x, sprintf } from '@wordpress/i18n';
import { createDialogComponent, showAdminNotice } from '../components';

const btnRestoreAll = document.getElementById('ilovepdf_restore_all');
const btnClearBackup = document.getElementById('ilovepdf_clear_backup');

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
