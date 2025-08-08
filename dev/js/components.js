import { _x } from '@wordpress/i18n';

/**
 * Create the component for the dialog box.
 *
 * @param {string} content content of the dialog box.
 * @returns {string} HTML component of the dialog box.
 */
export const createDialogComponent = (content) => {
	const dialogComponent = `<dialog id="ipdf-restore-dialog" class="ipdf-restore-dialog"><h2 class="ilovepdf-title-dialog">${_x(
		'Attention!',
		'title dialog box',
		'ilove-pdf'
	)}</h2>
                <p class="ilovepdf-content-dialog">${content}</p>
                <div class="ilovepdf-btn-groups">
                    <button id="ilovepdf-dialog-aceptted" class="ipdf-btn ipdf-btn--primary">${_x(
						'Yes',
						'button dialog box',
						'ilove-pdf'
					)}</button>
                    <button id="ilovepdf-dialog-close" class="ipdf-btn ipdf-btn--secondary">${_x(
						'Close',
						'button dialog box',
						'ilove-pdf'
					)}</button>
                </div>
            </dialog>`;

	return dialogComponent;
};

/**
 * Show admin notice on dashboard.
 *
 * @param {string} message the message to show.
 * @param {string} type the type of message. Default: success
 * @returns {string} HTML component of the admin notice.
 */
export const showAdminNotice = (message, type = 'success') => {
	const notice = sprintf(
		'<div class="ipdf-notice ilovepdf-base__layout-flex notice notice-%s is-dismissible"><figure class="ipdf-logo ilovepdf-base__layout-flex ilovepdf-base__layout-items--center"><img src="%s" alt="logo ilovepdf" /></figure><p>%s</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>',
		type,
		IlovePdfData.logoUrl,
		message
	);

	const container = document.querySelector(
		'#wpwrap #wpcontent #wpbody #wpbody-content > h1, #wpwrap #wpcontent #wpbody #wpbody-content > h2, #wpwrap #wpcontent #wpbody #wpbody-content'
	);
	container?.insertAdjacentHTML('beforebegin', notice);

	setTimeout(() => {
		window.scrollTo({ top: 0, behavior: 'smooth' });
	}, 500);

	if (container) {
		const btnsCloseNotice = document.querySelectorAll('.is-dismissible .notice-dismiss');

		if (btnsCloseNotice) {
			btnsCloseNotice.forEach((btn) => {
				btn.addEventListener('click', function (e) {
					e.preventDefault();
					e.currentTarget.parentNode.remove();
				});
			});
		}
	}
};
