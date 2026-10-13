(function ($) {
	function nextIndex($wrap) {
		let max = -1;
		$wrap.find('input[name], textarea[name]').each(function () {
			const name = $(this).attr('name') || '';
			const m = name.match(/\[(\d+)\]/);
			if (m) max = Math.max(max, parseInt(m[1], 10));
		});
		return max + 1;
	}

	function addRow(type) {
		const tpl = document.getElementById('tpa_tpl_' + type);
		if (!tpl) return;
		const $repeater = $('.tpa-repeater[data-repeater="' + type + '"]');
		const idx = nextIndex($repeater) || 0;
		const html = tpl.innerHTML.replaceAll('__i__', String(idx));
		$repeater.find('.tpa-repeater-rows').append(html);
	}

	$(document).on('click', '.tpa-add', function (e) {
		e.preventDefault();
		const type = $(this).data('add');
		if (!type) return;
		addRow(type);
	});

	$(document).on('click', '.tpa-remove-row', function (e) {
		e.preventDefault();
		$(this).closest('.tpa-row').remove();
	});

	// Generic image picker.
	$(function () {
		let frame = null;

		$(document).on('click', '.tpa-image-pick', function (e) {
			e.preventDefault();
			const targetId = $(this).data('target');
			const previewId = $(this).data('preview');
			if (!targetId || !previewId) return;

			if (!frame) {
				frame = wp.media({
					title: 'Select image',
					button: { text: 'Use this image' },
					multiple: false,
				});
			}

			frame.off('select');
			frame.on('select', function () {
				const attachment = frame.state().get('selection').first().toJSON();
				$('#' + targetId).val(attachment.id);
				const src =
					(attachment.sizes && attachment.sizes.medium && attachment.sizes.medium.url) ||
					(attachment.sizes && attachment.sizes.large && attachment.sizes.large.url) ||
					attachment.url;
				$('#' + previewId).empty().append($('<img alt="">').attr('src', src));
			});

			frame.open();
		});

		$(document).on('click', '.tpa-image-clear', function (e) {
			e.preventDefault();
			const targetId = $(this).data('target');
			const previewId = $(this).data('preview');
			if (!targetId || !previewId) return;
			$('#' + targetId).val('0');
			const $preview = $('#' + previewId).empty();
			const fallback = $preview.data('default');
			if (fallback) {
				$preview.append(
					$('<img alt="">').attr('src', fallback),
					$('<p class="description"></p>').text('Default image')
				);
			}
		});
	});
})(jQuery);

