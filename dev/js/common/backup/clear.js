import { _x } from '@wordpress/i18n';
import { createDialogComponent, showAdminNotice } from '../../components';
import { getRestoreBackupLoading } from '../DOMElements';

const btnClearBackup = document.getElementById('ilovepdf_clear_backup');

btnClearBackup?.addEventListener('click', function (e) {
	e.preventDefault();

	const loadingIndicator = getRestoreBackupLoading();
	loadingIndicator.style.display = 'block';

	const currentTarget = e.currentTarget;
	const titleDialog = _x(
		'Are you sure you want to delete all backups?',
		'title dialog box',
		'ilove-pdf'
	);
	const contentDialog = _x(
		'This will permanently delete all backups. Continue?',
		'body content dialog box',
		'ilove-pdf'
	);
	const buttonActionText = _x('Clear backups', 'button dialog box', 'ilove-pdf');
	const dialogComponent = createDialogComponent(contentDialog, titleDialog, buttonActionText);

	currentTarget.insertAdjacentHTML('afterend', dialogComponent);

	const dialogElem = document.getElementById('ipdf-restore-dialog');
	const btnConfirmDialog = document.getElementById('ilovepdf-dialog-aceptted');
	const btnCloseDialog = document.getElementById('ilovepdf-dialog-close');
	const backupFolderSizeElem = document.getElementById('ipdf-backup-folder-size');
	const btnRestoreAll = document.getElementById('ilovepdf_restore_all');

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
					this.disabled = true;
					if (btnRestoreAll) {
						btnRestoreAll.disabled = true;
					}

					if (backupFolderSizeElem) {
						const contentOld = backupFolderSizeElem.textContent;
						const contentSplit = contentOld.split(':');
						backupFolderSizeElem.textContent = contentSplit[0] + ': 0';
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
