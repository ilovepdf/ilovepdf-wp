import { isMosaicActive } from '../common/mosaic';
import { getPreview } from '../common/preview';

/**
 * Sets the image URL in the input field and preview image source.
 *
 * @param {string} url - The URL of the image to set.
 * @returns {void}
 */
export const setImageUrl = (url) => {
	document.getElementById('ipdf_option_mode_image_url').value = url;
	document
		.querySelector('#ipdf-element-watermark img:not(.ipdf-element-clone)')
		.setAttribute('src', url);

	if (isMosaicActive()) {
		const { images } = getPreview('', true);
		images.forEach((image) => {
			image.setAttribute('src', url);
		});
	}
};

/**
 * Opens a WordPress media uploader frame for selecting/uploading watermark images.
 *
 * If a frame is provided, opens the existing frame. Otherwise creates a new media frame
 * configured to select single images (jpeg/png) for use as watermarks. When an image
 * is selected, updates the image URL input field and preview image source.
 *
 * @param {wp.media.view.MediaFrame} [frame] - Optional existing media frame to reuse
 * @returns {void}
 */
export const uploadImage = (frame) => {
	if (frame) {
		frame.open();
		return;
	}

	// Create a new media frame
	frame = wp.media({
		title: 'Select or upload an image',
		button: {
			text: 'Select for Watermark'
		},
		multiple: false,
		library: {
			type: 'image/jpeg,image/png'
		}
	});

	frame.on('select', function () {
		const attachment = frame.state().get('selection').first().toJSON();
		if (!attachment.url) {
			return;
		}

		setImageUrl(attachment.url);
	});

	frame.open();
};
