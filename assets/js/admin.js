/**
 * Admin field behaviour: media pickers, colour pickers and sortable repeaters.
 */
(function ($) {
	'use strict';

	function initColorPickers(context) {
		$(context).find('.tpa-color-field').each(function () {
			if (!$(this).closest('.wp-picker-container').length) {
				$(this).wpColorPicker();
			}
		});
	}

	$(document).on('click', '.tpa-media__choose', function (event) {
		event.preventDefault();
		var $field = $(this).closest('.tpa-media');
		var multiple = String($field.data('multiple')) === '1';
		var frame = wp.media({
			title: multiple ? 'Select images' : 'Select image',
			button: { text: multiple ? 'Use images' : 'Use image' },
			library: { type: 'image' },
			multiple: multiple ? 'add' : false
		});

		frame.on('select', function () {
			var items = frame.state().get('selection').toJSON();
			var $preview = $field.find('.tpa-media__preview').empty();
			$field.find('.tpa-media__input').val(items.map(function (item) {
				return item.id;
			}).join(','));
			items.forEach(function (item) {
				var src = item.sizes && item.sizes.thumbnail ? item.sizes.thumbnail.url : item.url;
				$preview.append($('<img>', { src: src, alt: '' }));
			});
			$field.toggleClass('has-value', items.length > 0);
		});

		frame.open();
	});

	$(document).on('click', '.tpa-media__remove', function (event) {
		event.preventDefault();
		var $field = $(this).closest('.tpa-media');
		$field.find('.tpa-media__input').val('');
		$field.find('.tpa-media__preview').empty();
		$field.removeClass('has-value');
	});

	$(document).on('click', '.tpa-repeater__add', function (event) {
		event.preventDefault();
		var $repeater = $(this).closest('.tpa-repeater');
		var index = parseInt($repeater.attr('data-next'), 10) || 0;
		var html = $repeater.children('.tpa-repeater__tpl').html().replace(/__INDEX__/g, String(index));
		var $row = $(html);

		$repeater.attr('data-next', index + 1);
		$repeater.children('.tpa-repeater__rows').append($row);
		initColorPickers($row);
		$row.find('input, select').first().trigger('focus');
	});

	$(document).on('click', '.tpa-repeater__remove', function (event) {
		event.preventDefault();
		$(this).closest('.tpa-repeater__row').remove();
	});

	$(function () {
		initColorPickers(document);
		$('.tpa-repeater__rows').sortable({
			handle: '.tpa-repeater__handle',
			axis: 'y',
			placeholder: 'tpa-repeater__placeholder',
			forcePlaceholderSize: true
		});
	});
})(jQuery);
