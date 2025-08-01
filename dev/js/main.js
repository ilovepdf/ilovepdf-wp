import { pulseAnimation } from './utils';
import './common/backup';
import './compress';
import './watermark';
import '../scss/app.scss';

const btnsSaveChanges = document.querySelectorAll(
	'.ilovepdf-settings__main .ilovepdf-settings__main__section form .ipdf-input-submit'
);

document
	.querySelector('.ilovepdf-settings__main .ilovepdf-settings__main__section form')
	?.addEventListener('change', function () {
		btnsSaveChanges.forEach((btn) => {
			pulseAnimation(btn);
		});
	});

(function ($) {
	'use strict';

	// trigger on File Single Edit page
	$('.ilovepdf--meta-box-container .link-restore, .compat-field-iLovePDF-tools .link-restore').on(
		'click',
		function (e) {
			var elem = $(this);
			const hrefUrl = elem[0].href;

			e.preventDefault();

			$('.ilovepdf--meta-box-container, .compat-field-iLovePDF-tools .field').append(
				dialogComponent
			);

			const dialogElem = document.getElementById('ipdf-restore-dialog');
			const btnConfirmDialog = document.getElementById('ilovepdf-dialog-aceptted');
			const btnCloseDialog = document.getElementById('ilovepdf-dialog-close');

			dialogElem.showModal();

			btnConfirmDialog.addEventListener('click', (e) => {
				e.preventDefault();
				dialogElem.close();
				location.href = hrefUrl;
			});

			btnCloseDialog.addEventListener('click', (e) => {
				e.preventDefault();
				dialogElem.close();
			});
		}
	);
})(jQuery);
