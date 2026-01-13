// Checkout page script
(function() {
  'use strict';

  function formatPrice(n) {
    return '₱' + Number(n).toLocaleString();
  }

  function readCart() {
    try {
      return (window.Cart && window.Cart._read) ? window.Cart._read() : JSON.parse(localStorage.getItem('corefivegadgets_cart_v1') || '[]');
    } catch (e) {
      return [];
    }
  }

  function writeCart(cart) {
    localStorage.setItem('corefivegadgets_cart_v1', JSON.stringify(cart));
  }

  function render() {
    const itemsEl = document.getElementById('checkoutItems');
    const summaryList = document.getElementById('summaryList');
    const subtotalEl = document.getElementById('subtotal');
    const totalEl = document.getElementById('total');
    const cardSummary = document.querySelector('.card-summary');

    if (!itemsEl || !summaryList || !subtotalEl || !totalEl) return;

    const cart = readCart();
    itemsEl.innerHTML = '';
    summaryList.innerHTML = '';

    if (!cart.length) {
      itemsEl.innerHTML = '<div class="list-group-item text-center py-4 muted">No products available in your cart.</div>';
      if (cardSummary) cardSummary.style.display = 'none';
      return;
    }

    if (cardSummary) cardSummary.style.display = 'block';

    let subtotal = 0;
    cart.forEach((item, idx) => {
      const lineTotal = (item.price || 0) * (item.qty || 1);
      subtotal += lineTotal;

      const row = document.createElement('div');
      row.className = 'list-group-item d-flex gap-3 py-3 align-items-center';
      row.innerHTML = `
        <img src="${item.image || 'https://via.placeholder.com/160'}" alt="${item.title || 'Product'}" class="item-img item-img-lg">
        <div class="d-flex flex-column flex-grow-1">
          <div class="d-flex w-100 justify-content-between">
            <h6 class="mb-1">${item.title || 'Product'}</h6>
            <small class="text-muted">${formatPrice(item.price || 0)}</small>
          </div>
          <p class="mb-1 muted">${item.description || ''}</p>
          <div class="d-flex align-items-center gap-2">
            <label class="mb-0">Qty</label>
            <input data-idx="${idx}" type="number" class="form-control form-control-sm qty-input" value="${item.qty || 1}" min="1">
            <button data-idx="${idx}" class="btn btn-sm btn-outline-danger ms-2 btn-remove">Remove</button>
          </div>
        </div>
      `;
      itemsEl.appendChild(row);

      const li = document.createElement('li');
      li.className = 'd-flex justify-content-between';
      li.innerHTML = `<span>${item.title} ×${item.qty || 1}</span><strong>${formatPrice(lineTotal)}</strong>`;
      summaryList.appendChild(li);
    });

    subtotalEl.textContent = formatPrice(subtotal);

    const shippingOption = document.getElementById('shippingOption');
    const shippingFee = shippingOption ? Number(shippingOption.options[shippingOption.selectedIndex].dataset.fee || 0) : 0;
    document.getElementById('shippingFee').textContent = formatPrice(shippingFee);
    totalEl.textContent = formatPrice(subtotal + shippingFee);

    // Attach handlers
    itemsEl.querySelectorAll('.qty-input').forEach(input => {
      input.addEventListener('change', () => {
        const i = Number(input.getAttribute('data-idx'));
        const val = Math.max(1, Number(input.value) || 1);
        const cart = readCart();
        if (cart[i]) {
          cart[i].qty = val;
          writeCart(cart);
          render();
        }
      });
    });

    itemsEl.querySelectorAll('.btn-remove').forEach(btn => {
      btn.addEventListener('click', () => {
        const i = Number(btn.getAttribute('data-idx'));
        const cart = readCart();
        cart.splice(i, 1);
        writeCart(cart);
        render();
      });
    });

    if (shippingOption) {
      shippingOption.addEventListener('change', render);
    }
  }

  function placeOrder() {
    const cart = readCart();
    if (!cart.length) {
      if (window.Toast && window.Toast.show) {
        window.Toast.show('Your cart is empty.');
      }
      return;
    }

    const addressInput = document.getElementById('shippingAddress');
    const address = addressInput ? addressInput.value.trim() : '';
    
    // Validate shipping address
    if (!address) {
      if (addressInput) {
        addressInput.classList.add('is-invalid');
        addressInput.focus();
      }
      return;
    }
    
    // Remove invalid class if address is provided
    if (addressInput) {
      addressInput.classList.remove('is-invalid');
    }

    const shippingOption = document.getElementById('shippingOption');
    const shippingMethod = shippingOption ? shippingOption.value : 'standard';
    const shippingFee = shippingOption ? Number(shippingOption.options[shippingOption.selectedIndex].dataset.fee || 0) : 0;
    const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked')?.value || 'card';
    const notes = document.getElementById('orderNotes').value || '';

    const subtotal = cart.reduce((s, i) => s + (i.price || 0) * (i.qty || 1), 0);
    const total = subtotal + shippingFee;

    // Map cart items to match backend expectations
    const items = cart.map(item => ({
      product_id: item.id,
      quantity: item.qty || 1,
      price: item.price || 0
    }));

    const payload = {
      items: items,
      subtotal,
      shipping_fee: shippingFee,
      shipping_method: shippingMethod,
      shipping_address: address,
      payment_method: paymentMethod,
      order_notes: notes,
      total
    };

    fetch('/orders', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
      body: JSON.stringify(payload)
    })
      .then(res => res.json())
      .then(data => {
        writeCart([]);
        const orderId = data?.id;
        const msgEl = document.getElementById('orderSuccessMessage');
        const idEl = document.getElementById('orderSuccessOrderId');
        if (msgEl) msgEl.textContent = 'Thank you — your order has been placed.';
        if (idEl) idEl.textContent = orderId ? `Order ID: ${orderId}` : '';

        const modal = document.getElementById('orderSuccessModal');
        if (modal) {
          const bsModal = new bootstrap.Modal(modal);
          bsModal.show();
          setTimeout(() => {
            window.location.href = '/';
          }, 1800);
        } else {
          if (window.Toast && window.Toast.show) {
            window.Toast.show('Order placed successfully!');
          }
          setTimeout(() => {
            window.location.href = '/';
          }, 1200);
        }
      })
      .catch(err => {
        console.error('Order error:', err);
        if (window.Toast && window.Toast.show) {
          window.Toast.show('Error placing order. Please check your information and try again.');
        } else {
          alert('Error placing order. Please check your information and try again.');
        }
      });
  }

  document.addEventListener('DOMContentLoaded', () => {
    render();

    const placeOrderBtn = document.getElementById('placeOrderBtn');
    if (placeOrderBtn) {
      placeOrderBtn.addEventListener('click', placeOrder);
    }

    // Remove validation error when user starts typing in shipping address
    const shippingAddress = document.getElementById('shippingAddress');
    if (shippingAddress) {
      shippingAddress.addEventListener('input', () => {
        shippingAddress.classList.remove('is-invalid');
      });
    }
  });
})();
