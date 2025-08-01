import { getStatusContainer, getFormData } from '../common/DOMElements';
import { showAdminNotice } from '../components';

document.addEventListener('DOMContentLoaded', function () {
	const bodyContent = document.querySelector('#wpbody-content');

	bodyContent?.addEventListener('click', function (event) {
		if (event.target.classList.contains('ipdf-btn--media-action-compress')) {
			event.preventDefault();

			const btnTrigger = event.target;
			btnTrigger.classList.add('ipdf-btn--media-action-trigger');

			const statusContainer = getStatusContainer(btnTrigger);

			const statusNotCompressed = statusContainer.querySelector(
				'.ipdf-item-status-not-compressed'
			);
			const statusSuccess = statusContainer.querySelector('.ipdf-item-status-compressed');
			const statusFail = statusContainer.querySelector('.ipdf-item-status-fail');
			statusFail.classList.remove('ipdf-item-status-active');

			// TODO: revisar que la respuesta tenga un true en caso de que el archivo tenga un backup y se pueda restaurar.
			//const btnRestoreFile = statusContainer.querySelector('.ipdf-btn--media-action-restore');

			const loading = statusContainer.querySelector('.ipdf-item-status-compressing');
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
									statusSuccess.querySelector('span').textContent =
										data.data.percentage;
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
		}
	});
});
