jQuery(document).ready(function($) {
    // Uploading files
    $('body').on('click', '.t-create-p-form__remove', function(event) {
        event.preventDefault();
        var parent = $(this).parent()
        parent.removeClass('done')
        parent.find('input').val('')
        parent.find('label').attr('style', '');
    });

    function encodeImgtoBase64(element) {
        var sc = $(element).closest('.t-create-p-form__img')
        var img = element.files[0];
        if (element.files[0].size > 2000000) {
            alert('Image size exceeds 2MB');
            return false;
        }
        var file = element.files[0];
        var fileType = file["type"];
        var validImageTypes = ["image/gif", "image/jpeg", "image/png"];
        if ($.inArray(fileType, validImageTypes) < 0) {
            // invalid file type code goes here.
            alert('The image must be in valid formats (gif, jpg, png, etc.)');
            return false;
        }
        sc.removeClass('done')
        sc.addClass('active')

        var reader = new FileReader();

        reader.onloadend = function() {
            setTimeout(function() {
                sc.removeClass('active')
                sc.addClass('done')
                sc.find('label').css('background-image', 'url(' + reader.result + ')');
            }, 450);
        }
        reader.readAsDataURL(img)
    }

    function encodeImgtoBase64_2(element) {
        var sc = $(element).closest('.t-create-p-form__img')
        var img = element.files[0];
        sc.removeClass('done')
        sc.addClass('active')

        var reader = new FileReader();

        reader.onloadend = function() {
            setTimeout(function() {
                sc.removeClass('active')
                sc.addClass('done')
                sc.find('label').css('background-image', 'url(' + reader.result + ')');
            }, 450);
        }
        reader.readAsDataURL(img)
    }

    // File type validation
    $(".t-create-product-form input[type='file']").on('change', function(event) {
        encodeImgtoBase64(this)
    });
    $('body').on('change', ".t-create-p-form__img input[type='file']", function(event) {
        if (this.files[0].size > 2000000) {
            alert('Image size exceeds 2MB');
            return false;
        }

        var file = this.files[0];
        var fileType = file["type"];
        var validImageTypes = ["image/gif", "image/jpeg", "image/png"];
        if ($.inArray(fileType, validImageTypes) < 0) {
            // invalid file type code goes here.
            alert('The image must be in valid formats (gif, jpg, png, etc.)');
            return false;
        }

        encodeImgtoBase64_2(this)
    });

    $('.t-create-p-form').on('submit', function(event) {
    	event.preventDefault();

    	var form = $(this)
        var product_title = $('.t-create-p-form__product-title input').val();
        var type_product = $('#type_product').val();
        var product_price = $('.t-create-p-form__product-price input').val();
        var product_img = $('#product_img')[0].files[0]
        console.log(product_price)

        var fd = new FormData();
        fd.append("action", "mon_uploader_submission_imgs_one");
        fd.append('product_title', product_title);
        fd.append('type_product', type_product);
        fd.append('product_price', product_price);
        fd.append('product_img', product_img);

        $.ajax({
            type: "POST",
            url: twe_object.ajax_url,
            data: fd,
            dataType: 'json',
            processData: false,
            contentType: false,
            beforeSend: function() {
                form.find('.t-create-p-form__img-state').remove()
            },
            success: function(data) {
            	console.log(data)
                if (data.success != '') {
                    form.append('<div class="t-create-p-form__img-state">Successfully</div>')
                }
            },
            error: function(data) {
                console.log(data)
            },
        });
    });
});