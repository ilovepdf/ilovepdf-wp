import './common/backup';
import { restoreFile } from './common/backup/restoreFile';
import { compressFile } from './compress';
import { applyWatermark } from './watermark';
import {
	addAnimationToBtnSaveChanges,
	getStatusContainer,
} from './common/DOMElements';

import '../scss/app.scss';

window.addEventListener('load', function () {
	addAnimationToBtnSaveChanges();

	document.addEventListener('click', function (event) {
		const btnTrigger = event.target;

		if (btnTrigger.classList.contains('ipdf-btn--media-action-compress')) {
			event.preventDefault();

			btnTrigger.classList.add('ipdf-btn--media-action-trigger');
			const statusContainer = getStatusContainer(btnTrigger);

			compressFile(statusContainer, btnTrigger);
		}

		if (btnTrigger.classList.contains('ipdf-btn--media-action-watermark')) {
			event.preventDefault();

			btnTrigger.classList.add('ipdf-btn--media-action-trigger');
			const statusContainer = getStatusContainer(btnTrigger);

			applyWatermark(statusContainer, btnTrigger);
		}

		if (event.target.classList.contains('ipdf-btn--media-action-restore')) {
			event.preventDefault();

			restoreFile(btnTrigger);
		}
	});
});
