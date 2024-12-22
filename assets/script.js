$(document).ready(function () {
  $('#userForm').on('submit', function (e) {
    if (!$('#agree').is(':checked')) {
      alert('Anda harus setuju dengan syarat dan ketentuan.');
      e.preventDefault();
    }
  });

  $('.add-to-cart').on('click', function () {
    const productId = $(this).data('id');
    const productName = $(this).data('name');
    const productPrice = $(this).data('price');
    const imagePath = $(this).data('image');
    const product = {
      id: productId,
      name: productName,
      price: productPrice,
      image: imagePath,
    };

    let cart = [];
    const cartCookie = getCookie('cart');
    if (cartCookie) {
      cart = JSON.parse(cartCookie);
    }

    cart.push(product);
    setCookie('cart', JSON.stringify(cart), 7);
    toastr.success('Product added to cart');
  });

  $('.cart-icon').on('click', function () {
    const cartCookie = getCookie('cart');
    let cart = [];
    if (cartCookie) {
      cart = JSON.parse(cartCookie);
    }

    let cartHtml = '<h2>My Cart</h2>';
    if (cart.length > 0) {
      cartHtml += '<div class="cards">';
      cart.forEach(function (product, index) {
        cartHtml += '<div class="card">';
        cartHtml += '<h3>' + product.name + '</h3>';
        cartHtml +=
          '<p>Price: Rp' + number_format(product.price, 0, ',', '.') + '</p>';
        cartHtml +=
          '<div class="image-wrapper"> <img src="' +
          product.image +
          '" alt="' +
          product.name +
          '"></div>';
        cartHtml +=
          '<button class="remove-button" data-index="' +
          index +
          '">Remove</button>';
        cartHtml += '</div>';
      });
      cartHtml += '</div>';
    } else {
      cartHtml += '<p>Your cart is empty.</p>';
    }

    cartHtml +=
      '<button class="back-button"><i class="fas fa-arrow-left"></i>Back to Products</button>';
    $('#main-content').html(cartHtml);

    $('.back-button').on('click', function () {
      location.reload();
    });

    $('.remove-button').on('click', function () {
      const index = $(this).data('index');
      cart.splice(index, 1);
      setCookie('cart', JSON.stringify(cart), 7);
      $(this).parent().remove();
      if (cart.length === 0) {
        $('#main-content').html(
          '<h2>My Cart</h2><p>Your cart is empty.</p><button class="back-button"><i class="fas fa-arrow-left"></i>Back to Products</button>'
        );
        $('.back-button').on('click', function () {
          location.reload();
        });
      }
    });
  });

  function setCookie(name, value, days) {
    const d = new Date();
    d.setTime(d.getTime() + days * 24 * 60 * 60 * 1000);
    const expires = 'expires=' + d.toUTCString();
    document.cookie = name + '=' + value + ';' + expires + ';path=/';
  }

  function getCookie(name) {
    const nameEQ = name + '=';
    const ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) == ' ') c = c.substring(1, c.length);
      if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
  }

  function number_format(number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    const n = !isFinite(+number) ? 0 : +number;
    const prec = !isFinite(+decimals) ? 0 : Math.abs(decimals);
    const sep = typeof thousands_sep === 'undefined' ? ',' : thousands_sep;
    const dec = typeof dec_point === 'undefined' ? '.' : dec_point;
    let s = '';
    const toFixedFix = function (n, prec) {
      const k = Math.pow(10, prec);
      return '' + (Math.round(n * k) / k).toFixed(prec);
    };
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
      s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
      s[1] = s[1] || '';
      s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
  }

  const productModal = $('#product-modal');

  // Open Add Product Modal
  $('#add-product-button').on('click', function () {
    $('#product-form').trigger('reset');
    $('#product-form [name="action"]').val('create');
    $('#image-preview').hide();
    productModal.fadeIn();
  });

  $('.edit-product').on('click', function () {
    const productId = $(this).data('id');
    $.ajax({
      url: '',
      type: 'GET',
      data: { id: productId },
      success: function (response) {
        const product = JSON.parse(response);
        $('#product-form [name="action"]').val('update');
        $('#product-form [name="id"]').val(product.id);
        $('#product-form [name="name"]').val(product.name);
        $('#product-form [name="price"]').val(product.price);
        $('#product-form [name="description"]').val(product.description);
        if (product.image) {
          $('#image-preview').attr('src', product.image).show();
        } else {
          $('#image-preview').hide();
        }
        productModal.fadeIn();
      },
    });
  });

  $('#product-form').on('submit', function (event) {
    event.preventDefault();
    const formData = new FormData(this);
    $.ajax({
      url: '',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        const result = JSON.parse(response);
        console.log(result);

        toastr[result.status === 200 ? 'success' : 'error'](result.message);
        if (result.status === 200) location.href = '';
      },
    });
  });

  const deleteModal = $('#delete-modal');
  let deleteProductId = null;

  $('.delete-product').on('click', function () {
    deleteProductId = $(this).data('id');
    $('#delete-form [name="id"]').val(deleteProductId);
    deleteModal.fadeIn();
  });

  $('#delete-form').on('submit', function (event) {
    event.preventDefault();
    const formData = $(this).serialize();
    console.log(formData);

    $.ajax({
      url: '',
      type: 'POST',
      data: formData,
      success: function (response) {
        const result = JSON.parse(response);
        toastr[result.status === 200 ? 'success' : 'error'](result.message);
        if (result.status === 200) location.reload();
      },
    });
  });

  $('#cancel-delete, .modal-close').on('click', function () {
    deleteModal.fadeOut();
  });

  $('#image').on('change', function () {
    const [file] = this.files;
    if (file) {
      $('#image-preview').attr('src', URL.createObjectURL(file)).show();
    }
  });

  const userModal = $('#user-modal');
  let deleteUserId = null;

  // Open Add User Modal
  $('#add-user-button').on('click', function () {
    $('#user-form').trigger('reset');
    $('#user-form [name="action"]').val('create');
    userModal.fadeIn();
  });

  $('.edit-user').on('click', function () {
    const userId = $(this).data('id');
    $.ajax({
      url: '',
      type: 'GET',
      data: { id: userId },
      success: function (response) {
        const user = JSON.parse(response);
        $('#user-form [name="action"]').val('update');
        $('#user-form [name="id"]').val(user.id);
        $('#user-form [name="username"]').val(user.username);
        $('#user-form [name="gender"]').val(user.gender);
        $('#user-form [name="city"]').val(user.city);
        $('#user-form [name="ip"]').val(user.ip_address);
        $('#user-form [name="browser"]').val(user.browser);
        userModal.fadeIn();
      },
    });
  });

  $('#user-form').on('submit', function (event) {
    event.preventDefault();
    $.ajax({
      url: '',
      type: 'POST',
      data: $(this).serialize(),
      success: function (response) {
        const result = JSON.parse(response);
        toastr[result.status === 200 ? 'success' : 'error'](result.message);
        if (result.status === 200) location.reload();
      },
    });
  });

  $('.delete-user').on('click', function () {
    deleteUserId = $(this).data('id');
    deleteModal.fadeIn();
  });

  $('#confirm-delete').on('click', function () {
    $.ajax({
      url: '',
      type: 'POST',
      data: {
        action: 'delete',
        id: deleteUserId,
        csrf_token: '<?php echo $csrfToken; ?>',
      },
      success: function (response) {
        const result = JSON.parse(response);
        toastr[result.status === 200 ? 'success' : 'error'](result.message);
        if (result.status === 200) location.reload();
      },
    });
  });

  $('.modal-close, #cancel-delete').on('click', function () {
    $('.modal').fadeOut();
  });
});
