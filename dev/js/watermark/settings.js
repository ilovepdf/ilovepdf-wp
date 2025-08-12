import { setImageUrl, uploadImage } from './mode_image/media';
import {
	updateFontSize,
	updateFontStyle,
	updateTextColor,
	updatedFontFamily
} from './mode_text/toolbar';
import { updateElementCoordinates, updateTransform, updateTooltipValue } from './common/transform';
import { generateMosaic, isMosaicActive } from './common/mosaic';
import { POSITION_ACCEPTED, resetPosition } from './common/position';
import { getPreview } from './common/preview';
import { updateModeWatermark } from './common/modeWatermark';

document.addEventListener('DOMContentLoaded', function () {
	if (!document.body.classList.contains('ilovepdf_page_ipdf-watermark-admin-page')) return;

	const fieldModeWatermark = document.querySelector(
		'.ilovepdf-settings__main form input[name="ipdf_option_mode_watermark"][checked="checked"]'
	);
	const fieldFontFamily = document.querySelector(
		'.ilovepdf-settings__main form #ipdf_option_font_family'
	);
	updateModeWatermark(fieldModeWatermark.value);
	updatedFontFamily(getPreview(), fieldFontFamily);

	const formWatermark = document.querySelector(
		'.ilovepdf-settings__main .ilovepdf-settings__main__view--watermark form'
	);
	const btnUploadMedia = document.querySelector('.ipdf-btn--upload-file');
	let modeSelected = 'text';
	const WpFrame = null;

	formWatermark.addEventListener('input', function (event) {
		const { target } = event;
		const targetName = target.name;
		const targetId = target.id;
		const targetType = target.type;

		if (targetType === 'range') {
			const labelElement = target.parentNode.previousElementSibling;
			const tooltip = labelElement.querySelector('.ipdf-value-selected');
			updateTooltipValue(target, tooltip);
		}

		if (targetName === 'ipdf_option_mode_watermark') {
			modeSelected = target.value;
			updateModeWatermark(modeSelected);
		}

		if (targetName === 'ipdf_option_mode_image') {
			setImageUrl(target.value);
		}

		if (targetName === 'ipdf_option_font_family') {
			if (isMosaicActive()) {
				const { texts } = getPreview('', true);
				texts.forEach((element) => {
					updatedFontFamily(element, target.value);
				});
			}

			updatedFontFamily(getPreview(), target.value);
		}

		if (targetName === 'ipdf_option_font_style') {
			if (isMosaicActive()) {
				const { texts } = getPreview('', true);
				texts.forEach((element) => {
					updateFontStyle(element, targetId, target.value);
				});
			}

			updateFontStyle(getPreview(), targetId, target.value);
		}

		if (targetName === 'ipdf_option_font_size') {
			if (isMosaicActive()) {
				const { texts } = getPreview('', true);
				texts.forEach((element) => {
					updateFontSize(element, target.value);
				});
			}

			updateFontSize(getPreview(), target.value);
		}

		if (targetName === 'ipdf_option_transparency') {
			if (isMosaicActive()) {
				const { texts, images } = getPreview('', true);
				const elements = new Set([...texts, ...images]);
				elements.forEach((element) => {
					element.style.opacity = `${target.value}%`;
				});
			}

			getPreview().style.opacity = `${target.value}%`;
			getPreview('image').style.opacity = `${target.value}%`;
		}

		if (targetName === 'ipdf_option_rotation') {
			const rotateValue = `rotate(calc(${target.value} * -1deg))`;

			if (isMosaicActive()) {
				const { texts, images } = getPreview('', true);
				const elements = new Set([...texts, ...images]);
				elements.forEach((element) => {
					element.style.transform = updateTransform(
						element,
						'rotate',
						rotateValue,
					);
				});
			}

			const previewText = getPreview();
			const previewImage = getPreview('image');

			previewText.style.transform = updateTransform(
				previewText,
				'rotate',
				rotateValue,
			);

			previewImage.style.transform = updateTransform(
				previewImage,
				'rotate',
				rotateValue,
			);
		}

		if (targetName === 'ipdf_option_position') {
			const positionSelected = target.value;
			const positionAcceptedIndex = POSITION_ACCEPTED.indexOf(positionSelected);

			if (isMosaicActive()) return;

			if (positionAcceptedIndex === -1 && isMosaicActive()) return;

			resetPosition(getPreview());
			resetPosition(getPreview('image'));

			updateElementCoordinates(getPreview(), positionSelected);
			updateElementCoordinates(getPreview('image'), positionSelected);
		}

		if (targetName === 'ipdf_option_mosaic') {
			const positionContainer = document.querySelector(
				'.ilovepdf_option-settings-position-container'
			);
			const originalTextElement = getPreview();
			const originalImageElement = getPreview('image');

			if (target.checked) {
				positionContainer.classList.add('ilovepdf_option-settings-position-mode-mosaic');
				generateMosaic(originalTextElement);
				generateMosaic(originalImageElement);

				return;
			}

			positionContainer.classList.remove('ilovepdf_option-settings-position-mode-mosaic');
			document
				.querySelectorAll('#ipdf-element-watermark .ipdf-element-clone')
				.forEach((item) => {
					item.remove();
				});

			if (modeSelected === 'text') {
				originalTextElement.hidden = false;
				originalImageElement.hidden = true;
				return;
			}

			if (modeSelected === 'image') {
				originalTextElement.hidden = true;
				originalImageElement.hidden = false;
				return;
			}
		}
	});

	btnUploadMedia.addEventListener('click', function (event) {
		event.preventDefault();
		uploadImage(WpFrame);
	});

	updateTextColor(getPreview());
});
