<?php 
add_action( 'admin_menu', 'twe_add_metabox' );
// or add_action( 'add_meta_boxes', 'twe_add_metabox' );
// or add_action( 'add_meta_boxes_product', 'twe_add_metabox' );

function twe_add_metabox() {
	add_meta_box(
		'twe_metabox', // metabox ID
		'Meta Box', // title
		'twe_metabox_callback', // callback function
		'product', // post type or post types in array
		'normal', // position (normal, side, advanced)
		'default' // priority (default, low, high, core)
	);
}

// it is a callback function which actually displays the content of the meta box
function twe_metabox_callback( $post ) {
	$date_product = get_post_meta( $post->ID, 'date_product', true );
	$type_product = get_post_meta( $post->ID, 'type_product', true );
	wp_nonce_field( 'somerandomstr', '_twenonce' );
	?>

	<table class="table">
		<tbody>
			<tr>
				<th><label for="date_product">Date Product</label></th>  
				<td><input type="date" id="date_product" name="date_product" value="<?php echo esc_attr( $date_product ); ?>" class="date-product"></td>
			</tr>
			<tr>
				<th><label for="type_product">Type Product</label></th>
				<td>
					<select id="type_product" name="type_product">
						<option value="">Select</option>
						<option value="rare" <?php  echo selected( 'rare', $type_product, false ); ?> >Rare</option>
						<option value="frequent" <?php echo selected( 'frequent', $type_product, false ); ?> >Frequent</option>
						<option value="unusual" <?php echo selected( 'unusual', $type_product, false ); ?> >Unusual</option>
					</select>
				</td>
			</tr>
			<tr>
				<th></th>
				<td>
					<button type="submit" name="clear_meta_field">Clear Meta</button>
					<button type="submit" name="submit_post">Submit</button>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}

add_action( 'save_post', 'twe_save_meta', 10, 2 );
// or add_action( 'save_post_product', 'twe_save_meta', 10, 2 );

function twe_save_meta( $post_id, $post ) {

	// nonce check
	if ( ! isset( $_POST[ '_twenonce' ] ) || ! wp_verify_nonce( $_POST[ '_twenonce' ], 'somerandomstr' ) ) {
		return $post_id;
	}

	// check current user permissions
	$post_type = get_post_type_object( $post->post_type );

	if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
		return $post_id;
	}

	// Do not save the data if autosave
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
		return $post_id;
	}

	// define your own post type here
	if( 'product' !== $post->post_type ) {
		return $post_id;
	}

	if( isset( $_POST[ 'date_product' ] ) ) {
		update_post_meta( $post_id, 'date_product', sanitize_text_field( $_POST[ 'date_product' ] ) );
	} else {
		delete_post_meta( $post_id, 'date_product' );
	}
	if( isset( $_POST[ 'type_product' ] ) ) {
		update_post_meta( $post_id, 'type_product', sanitize_text_field( $_POST[ 'type_product' ] ) );
	} else {
		delete_post_meta( $post_id, 'type_product' );
	}

	return $post_id;

}


add_action( 'add_meta_boxes', 'listing_image_add_metabox' );
function listing_image_add_metabox () {
	add_meta_box( 'listingimagediv', __( 'Listing Image', 'text-domain' ), 'listing_image_metabox', 'product', 'side', 'low');
}

function listing_image_metabox ( $post ) {
	global $content_width, $_wp_additional_image_sizes;

	$image_id = get_post_meta( $post->ID, '_listing_image_id', true );

	$old_content_width = $content_width;
	$content_width = 254;

	if ( $image_id && get_post( $image_id ) ) {

		if ( ! isset( $_wp_additional_image_sizes['post-thumbnail'] ) ) {
			$thumbnail_html = wp_get_attachment_image( $image_id, array( $content_width, $content_width ) );
		} else {
			$thumbnail_html = wp_get_attachment_image( $image_id, 'post-thumbnail' );
		}

		if ( ! empty( $thumbnail_html ) ) {
			$content = $thumbnail_html;
			$content .= '<p class="hide-if-no-js"><a href="javascript:;" id="remove_listing_image_button" >' . esc_html__( 'Remove listing image', 'text-domain' ) . '</a></p>';
			$content .= '<input type="hidden" id="upload_listing_image" name="_listing_cover_image" value="' . esc_attr( $image_id ) . '" />';
		}

		$content_width = $old_content_width;
	} else {

		$content = '<img src="" style="width:' . esc_attr( $content_width ) . 'px;height:auto;border:0;display:none;" />';
		$content .= '<p class="hide-if-no-js"><a title="' . esc_attr__( 'Set listing image', 'text-domain' ) . '" href="javascript:;" id="upload_listing_image_button" id="set-listing-image" data-uploader_title="' . esc_attr__( 'Choose an image', 'text-domain' ) . '" data-uploader_button_text="' . esc_attr__( 'Set listing image', 'text-domain' ) . '">' . esc_html__( 'Set listing image', 'text-domain' ) . '</a></p>';
		$content .= '<input type="hidden" id="upload_listing_image" name="_listing_cover_image" value="" />';

	}

	echo $content;
}

add_action( 'save_post', 'listing_image_save', 10, 1 );
function listing_image_save ( $post_id ) {
	if( isset( $_POST['_listing_cover_image'] ) ) {
		$image_id = (int) $_POST['_listing_cover_image'];
		update_post_meta( $post_id, '_listing_image_id', $image_id );
		$set_thumb = set_post_thumbnail( $post_id, $image_id );
	}
}

function mon_uploader_submission_imgs_one(){
	$type_product  = $_POST['type_product'];
	$product_title = $_POST['product_title'];
	$product_price = $_POST['product_price'];
	$attachment_id = media_handle_upload( 'product_img', 0 );

    $post_id = wp_insert_post( array(
    	'post_title' => $product_title,
    	'post_type' => 'product',
    	'post_status' => 'publish',
    ));

    $date_product = get_the_date( 'Y-m-d', $post_id );
    update_post_meta( $post_id, 'type_product', $type_product );
	update_post_meta( $post_id, 'date_product', $date_product );
	update_post_meta( $post_id, '_listing_image_id', $attachment_id );
    set_post_thumbnail( $post_id, $attachment_id );
    $product = wc_get_product( $post_id );
	$product->set_price( $product_price );
	$product->set_regular_price( $product_price );
    $product->save();

    if( is_wp_error( $post_id ) ) {
    	$data_out['error'] = $result->get_error_message();
    } else {
    	$data_out['success'] = 'Form sent';
    }

    echo json_encode($data_out);
    wp_die();
}
add_action('wp_ajax_mon_uploader_submission_imgs_one', 'mon_uploader_submission_imgs_one');
add_action('wp_ajax_nopriv_mon_uploader_submission_imgs_one', 'mon_uploader_submission_imgs_one');

// function twe_submit_meta_product(){
// 	do_action('save_post');
//     wp_die();
// }
// add_action('wp_ajax_twe_submit_meta_product', 'twe_submit_meta_product');
// add_action('wp_ajax_nopriv_twe_submit_meta_product', 'twe_submit_meta_product');

