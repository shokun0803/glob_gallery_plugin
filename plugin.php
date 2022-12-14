<?php
/*
Plugin Name: glob gallery plugin
Version: 1.0.0
Author: shokun
License: GPL2
*/
/*  Copyright 2022 shokun
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
        published by the Free Software Foundation.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function image_lists( $atts ) {
	global $post, $wp_rewrite;
	extract( shortcode_atts( array(
		'outfile' => 4,
		'expiration' => 60 * 60 * 24,
		'directory' => wp_upload_dir()['basedir'] . '/*/*/{*.jpg,*.jpeg,*.png,*.webp,*.gif}',
		'img_size' => 'width="150"',
	), $atts ) );
	$origin = $img_size == 'origin'? 'origin' : '';
	$pattern = $directory;
	$paged = get_query_var( 'paged' );
	$transient = 'gallery_' . $post->ID . '_' . $paged;
	if( false === ( $ret = get_transient( $transient ) ) ) {
		$tag = array();
		$imgs = preg_grep( '/(\-[\d]+?x[\d]+?)\./', glob( $pattern, GLOB_BRACE ), PREG_GREP_INVERT );
		$totalpage = ceil( count( $imgs ) / $outfile );
		$imgs = ( $paged == 0 )? array_slice( $imgs, 0, $outfile ): array_slice( $imgs, ( $paged - 1 ) * $outfile, $outfile );
		$tag[] = "<figure class=\"is-layout-flex wp-block-gallery-{$post->ID} wp-block-gallery has-nested-images columns-default is-cropped\">";
		foreach( $imgs as $img ) {
			if( substr( $img, 0, 1 ) == '/' ) {
				$img_path = get_site_url() . str_replace( $_SERVER['DOCUMENT_ROOT'], '', $img );
				if( $origin == 'origin' ) {
					$img_size = getimagesize( $img )[3];
				}
			} else {
				$img_path = get_site_url() . '/' . $img;
				if( $origin == 'origin' ) {
					$img_size = '';
				}
			}
			$img_size = htmlspecialchars( $img_size, ENT_NOQUOTES );
			$tag[] = "<figure class=\"wp-block-image size-large\"><img src=\"{$img_path}\" {$img_size} alt=\"\" /></figure>";
		}
		$tag[] = "</figure>";
		$paginate_base = get_pagenum_link(1);
		if( strpos( $paginate_base, '?' ) || !$wp_rewrite->using_permalinks() ) {
			$paginate_format = '';
			$paginate_base = add_query_arg( 'paged','%#%' );
		}else{
			$paginate_format = ( substr( $paginate_base, -1, 1 ) == '/' ? '' : '/' ) . user_trailingslashit( 'page/%#%/', 'paged' );
			$paginate_base .= '%_%';
		}
		$tag[] = paginate_links(array(
			'base' => $paginate_base,
			'format' => $paginate_format,
			'total' => $totalpage,
			'mid_size' => 1,
			'current' => ( $paged ? $paged : 1 ),
			'prev_text' => '<',
			'next_text' => '>',
		));
		$ret = implode( "\n", $tag );

		set_transient( $transient, $ret, $expiration );
	}
	return $ret;
}

add_shortcode( 'image_lists', 'image_lists' );
