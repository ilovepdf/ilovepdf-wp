import { _x } from '@wordpress/i18n';
import { getFormData, getActionsContainer } from '../DOMElements';
import { createDialogComponent, showAdminNotice } from '../../components';

/**
 * Remove all status elements from the status container.
 * @param {HTMLElement} btnTrigger - The button element that triggered the action.
 * @since 3.0.0
 */
const removeAllStatus = (btnTrigger) => {
	const statusContainer = getActionsContainer(btnTrigger);
	const allStatus = statusContainer.querySelectorAll('.ipdf-item-status');

	allStatus?.forEach((status) => {
		status.classList.remove('ipdf-item-status-active');
	});
};

/**
 * Restore a file based on the button trigger.
 * @param {HTMLElement} btnTrigger - The button element that triggered the action.
 * @since 3.0.0
 */
export const restoreFile = (btnTrigger) => {
	btnTrigger.classList.remove('ipdf-btn--media-action-restore-active');

	const statusContainer = getActionsContainer(btnTrigger);

	const statusSuccess = statusContainer.querySelector('.ipdf-item-status-restored');
	const statusFail = statusContainer.querySelector('.ipdf-item-status-fail');
	const loading = statusContainer.querySelector('.ipdf-item-status-processing-restore');

	const titleDialog = _x(
		'Are you sure you want to restore this file?',
		'body title dialog box',
		'ilove-pdf'
	);
	const contentDialog = _x(
		'This will undo all changes made to the file. Do you want to continue?',
		'body content dialog box',
		'ilove-pdf'
	);
	const buttonActionText = _x('Restore', 'button action', 'ilove-pdf');
	const dialogComponent = createDialogComponent(contentDialog, titleDialog, buttonActionText);

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

		const formData = getFormData(btnTrigger);

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
};
