<?php
/**
 * Template Name: Create Product
 */

get_header();
?>
<div class="t-create-p">
	<div class="container">
		<form class="t-create-p-form">
			<div class="t-create-p-form__product-title">
				<input type="text" name="product_title" placeholder="Title Product" required>
			</div>

			<div class="t-create-p-form__product-price">
				<input type="number" name="product_price" placeholder="Price Product" required>
			</div>

			<div class="t-create-p-form__img">
				<label for="product_img"></label>
				<input type="file" id="product_img" name="product_img" required>
				<div class="t-create-p-form__remove">
					Close
				</div>
			</div>

			<div class="t-create-p-form__type">
				<select id="type_product" name="type_product">
					<option value="">Select</option>
					<option value="rare">Rare</option>
					<option value="frequent">Frequent</option>
					<option value="unusual">Unusual</option>
				</select>
			</div>

			<div class="t-create-p-form__submit">
				<input type="submit" name="submit" value="Create">
			</div>
		</form>
	</div>
</div>
<?php
get_footer();