<?php
/**
 * Schema-driven admin form fields shared by the settings page and meta boxes.
 *
 * A field definition is an array with a `type` (text, url, number, textarea, checkbox,
 * select, icon, color, colors, image, gallery, repeater), a `label` and optional
 * `description`, `choices`, `fields` (repeater) and `default`.
 */

defined( 'ABSPATH' ) || exit;

final class TPA_Fields {

	public static function row( array $field, string $name, $value ): void {
		$wide = in_array( $field['type'], array( 'repeater', 'gallery', 'textarea' ), true );
		printf( '<div class="tpa-field%s">', $wide ? ' tpa-field--wide' : '' );
		if ( 'checkbox' !== $field['type'] ) {
			printf(
				'<label class="tpa-field__label" for="%1$s">%2$s</label>',
				esc_attr( self::id( $name ) ),
				esc_html( $field['label'] )
			);
		}
		self::render( $field, $name, $value );
		echo '</div>';
	}

	public static function render( array $field, string $name, $value ): void {
		$id   = self::id( $name );
		$type = $field['type'];

		switch ( $type ) {
			case 'repeater':
				self::repeater( $field, $name, is_array( $value ) ? $value : array() );
				break;

			case 'textarea':
				printf(
					'<textarea id="%1$s" name="%2$s" rows="3" class="large-text">%3$s</textarea>',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_textarea( (string) $value )
				);
				break;

			case 'checkbox':
				printf(
					'<label class="tpa-field__check"><input type="checkbox" id="%1$s" name="%2$s" value="1" %3$s> %4$s</label>',
					esc_attr( $id ),
					esc_attr( $name ),
					checked( '1', (string) $value, false ),
					esc_html( $field['label'] )
				);
				break;

			case 'select':
			case 'icon':
				$choices = 'icon' === $type ? TPA_Icons::choices() : $field['choices'];
				printf( '<select id="%1$s" name="%2$s">', esc_attr( $id ), esc_attr( $name ) );
				foreach ( $choices as $key => $label ) {
					printf(
						'<option value="%1$s" %2$s>%3$s</option>',
						esc_attr( $key ),
						selected( (string) $value, (string) $key, false ),
						esc_html( $label )
					);
				}
				echo '</select>';
				break;

			case 'color':
				printf(
					'<input type="text" class="tpa-color-field" id="%1$s" name="%2$s" value="%3$s" data-default-color="%4$s">',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( (string) $value ),
					esc_attr( $field['default'] ?? '' )
				);
				break;

			case 'image':
			case 'gallery':
				self::media( $field, $id, $name, (string) $value, 'gallery' === $type );
				break;

			case 'number':
				printf(
					'<input type="number" class="small-text" id="%1$s" name="%2$s" value="%3$s" step="%4$s" min="0">',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( (string) $value ),
					esc_attr( $field['step'] ?? '1' )
				);
				break;

			default:
				printf(
					'<input type="%1$s" class="regular-text" id="%2$s" name="%3$s" value="%4$s" placeholder="%5$s">',
					'url' === $type ? 'url' : 'text',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( (string) $value ),
					esc_attr( $field['placeholder'] ?? '' )
				);
		}

		if ( ! empty( $field['description'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $field['description'] ) );
		}
	}

	/**
	 * @return mixed Sanitized value.
	 */
	public static function sanitize( array $field, $value ) {
		switch ( $field['type'] ) {
			case 'repeater':
				return self::sanitize_repeater( $field, $value );
			case 'url':
				return esc_url_raw( trim( (string) $value ) );
			case 'number':
				return is_numeric( $value ) ? $value + 0 : 0;
			case 'textarea':
				return sanitize_textarea_field( (string) $value );
			case 'checkbox':
				return empty( $value ) ? '' : '1';
			case 'select':
				return array_key_exists( (string) $value, $field['choices'] ) ? (string) $value : (string) array_key_first( $field['choices'] );
			case 'icon':
				return array_key_exists( (string) $value, TPA_Icons::choices() ) ? (string) $value : 'tag';
			case 'color':
				return (string) sanitize_hex_color( (string) $value );
			case 'colors':
				return implode( ', ', self::parse_colors( (string) $value ) );
			case 'image':
				return absint( $value );
			case 'gallery':
				return implode( ',', array_filter( array_map( 'absint', explode( ',', (string) $value ) ) ) );
			default:
				return sanitize_text_field( (string) $value );
		}
	}

	/**
	 * @return string[] Valid hex colours from a comma-separated list.
	 */
	public static function parse_colors( string $value ): array {
		$colors = array_map( 'trim', explode( ',', $value ) );
		return array_values( array_filter( array_map( 'sanitize_hex_color', $colors ) ) );
	}

	private static function sanitize_repeater( array $field, $rows ): array {
		if ( ! is_array( $rows ) ) {
			return array();
		}
		$clean = array();
		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$item = array();
			foreach ( $field['fields'] as $key => $sub_field ) {
				$item[ $key ] = self::sanitize( $sub_field, $row[ $key ] ?? '' );
			}
			if ( '' === implode( '', array_map( 'strval', $item ) ) ) {
				continue;
			}
			$clean[] = $item;
		}
		return $clean;
	}

	private static function repeater( array $field, string $name, array $rows ): void {
		printf( '<div class="tpa-repeater" data-next="%d">', count( $rows ) );
		echo '<div class="tpa-repeater__rows">';
		foreach ( array_values( $rows ) as $index => $row ) {
			self::repeater_row( $field['fields'], $name, (string) $index, $row );
		}
		echo '</div>';

		echo '<script type="text/html" class="tpa-repeater__tpl">';
		self::repeater_row( $field['fields'], $name, '__INDEX__', array() );
		echo '</script>';

		printf(
			'<button type="button" class="button tpa-repeater__add">%s</button>',
			esc_html( $field['add_label'] ?? __( 'Add item', 'toposel-assignment' ) )
		);
		echo '</div>';
	}

	private static function repeater_row( array $fields, string $name, string $index, array $row ): void {
		echo '<div class="tpa-repeater__row">';
		printf(
			'<span class="tpa-repeater__handle dashicons dashicons-menu" title="%s"></span>',
			esc_attr__( 'Drag to reorder', 'toposel-assignment' )
		);
		echo '<div class="tpa-repeater__fields">';
		foreach ( $fields as $key => $sub_field ) {
			self::row( $sub_field, sprintf( '%s[%s][%s]', $name, $index, $key ), $row[ $key ] ?? ( $sub_field['default'] ?? '' ) );
		}
		echo '</div>';
		printf(
			'<button type="button" class="button-link button-link-delete tpa-repeater__remove">%s</button>',
			esc_html__( 'Remove', 'toposel-assignment' )
		);
		echo '</div>';
	}

	private static function media( array $field, string $id, string $name, string $value, bool $multiple ): void {
		$ids = array_filter( array_map( 'absint', explode( ',', $value ) ) );
		printf(
			'<div class="tpa-media%1$s" data-multiple="%2$d">',
			$ids ? ' has-value' : '',
			$multiple ? 1 : 0
		);
		printf(
			'<input type="hidden" class="tpa-media__input" id="%1$s" name="%2$s" value="%3$s">',
			esc_attr( $id ),
			esc_attr( $name ),
			esc_attr( implode( ',', $ids ) )
		);
		echo '<div class="tpa-media__preview">';
		foreach ( $ids as $attachment_id ) {
			echo wp_get_attachment_image( $attachment_id, 'thumbnail' );
		}
		echo '</div>';
		printf(
			'<button type="button" class="button tpa-media__choose">%1$s</button> <button type="button" class="button-link tpa-media__remove">%2$s</button>',
			esc_html( $multiple ? __( 'Choose images', 'toposel-assignment' ) : __( 'Choose image', 'toposel-assignment' ) ),
			esc_html__( 'Remove', 'toposel-assignment' )
		);
		echo '</div>';
	}

	private static function id( string $name ): string {
		return 'tpa-' . trim( preg_replace( '/[^a-z0-9_]+/i', '-', $name ), '-' );
	}
}
