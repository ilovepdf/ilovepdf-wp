import { isMosaicActive } from './mosaic';
import { getPreview } from './preview';

/**
 * Updates the watermark mode UI elements based on the selected mode (text or image).
 *
 * @param {string} mode - The watermark mode to set ('text' or 'image').
 * @returns {void}
 */
export const updateModeWatermark = (mode) => {
	const settingsItems = document.querySelectorAll('.ilovepdf_option-settings-item');
	const elementSelected = document.querySelector(`.ilovepdf_option-settings-${mode}`);

	settingsItems?.forEach((item) => {
		item.classList.remove('ipdf-option-selected');
	});

	elementSelected.classList.add('ipdf-option-selected');

	if (isMosaicActive()) {
		const { texts, images } = getPreview('', true);

		switch (mode) {
			case 'text':
				texts.forEach((text) => {
					text.hidden = false;
				});
				images.forEach((image) => {
					image.hidden = true;
				});
				break;
			case 'image':
				texts.forEach((text) => {
					text.hidden = true;
				});
				images.forEach((image) => {
					image.hidden = false;
				});
				break;
		}

		return;
	}

	const previewText = getPreview();
	const previewImage = getPreview('image');

	switch (mode) {
		case 'text':
			previewText.hidden = false;
			previewImage.hidden = true;
			break;
		case 'image':
			previewText.hidden = true;
			previewImage.hidden = false;
			break;
	}
};
