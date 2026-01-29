import { _x } from '@wordpress/i18n';
import { createDialogComponent, showAdminNotice } from '../../components';
import { getRestoreBackupLoading } from '../DOMElements';

const btnRestoreAll = document.getElementById('ilovepdf_restore_all');

btnRestoreAll?.addEventListener('click', function (e) {
	e.preventDefault();

	const loadingIndicator = getRestoreBackupLoading();
	loadingIndicator.style.display = 'block';

	const currentTarget = e.currentTarget;
	const titleDialog = _x(
		'Are you sure you want to restore all files?',
		'title dialog box',
		'ilove-pdf'
	);
	const contentDialog = _x(
		'This will undo all changes. Do you want to continue?',
		'body content dialog box',
		'ilove-pdf'
	);
	const buttonActionText = _x('Restore files', 'button dialog box', 'ilove-pdf');
	const dialogComponent = createDialogComponent(contentDialog, titleDialog, buttonActionText);

	currentTarget.insertAdjacentHTML('afterend', dialogComponent);

	const dialogElem = document.getElementById('ipdf-restore-dialog');
	const btnConfirmDialog = document.getElementById('ilovepdf-dialog-aceptted');
	const btnCloseDialog = document.getElementById('ilovepdf-dialog-close');
	const backupFolderSizeElem = document.getElementById('ipdf-backup-folder-size');
	const btnClearBackup = document.getElementById('ilovepdf_clear_backup');

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
						this.disabled = true;
						if (btnClearBackup) {
							btnClearBackup.disabled = true;
						}

						if (backupFolderSizeElem) {
							const contentOld = backupFolderSizeElem.textContent;
							const contentSplit = contentOld.split(':');
							backupFolderSizeElem.textContent = contentSplit[0] + ': 0';
						}
					}

					if (data.files_errors) {
						for (const file of data.files_errors) {
							showAdminNotice(file.message, 'error');
						}
					}
				}
				loadingIndicator.style.display = 'none';
			})
			.catch((error) => {
				showAdminNotice(error.data, 'error');
				loadingIndicator.style.display = 'none';
				console.error(error);
			});
	});

	btnCloseDialog.addEventListener('click', (e) => {
		e.preventDefault();
		dialogElem.close();
		dialogElem.remove();
		loadingIndicator.style.display = 'none';
	});
});
