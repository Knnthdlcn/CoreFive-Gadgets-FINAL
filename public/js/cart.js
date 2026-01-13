// Simple cart manager using localStorage with user-specific storage
(function() {
  'use strict';
  
  // Generate user-specific storage key
  function getStorageKey() {
    // If authenticated, use userId; otherwise use session/guest ID
    if (window.isAuthenticated && window.userId) {
      return 'corefivegadgets_cart_user_' + window.userId;
    } else {
      // For guest users, use a temporary session-based key
      const sessionKey = 'corefivegadgets_guest_id';
      let guestId = sessionStorage.getItem(sessionKey);
      if (!guestId) {
        guestId = 'guest_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        sessionStorage.setItem(sessionKey, guestId);
      }
      return 'corefivegadgets_cart_' + guestId;
    }
  }

  function readCart() {
    try {
      return JSON.parse(localStorage.getItem(getStorageKey()) || '[]');
    } catch (e) {
      return [];
    }
  }

  function writeCart(cart) {
    localStorage.setItem(getStorageKey(), JSON.stringify(cart));
  }

  function formatPrice(n) {
    return '₱' + Number(n).toLocaleString();
  }

  function updateBadge(animate = false) {
    const cart = readCart();
    const badge = document.getElementById('cartBadge');
    if (badge) {
      const count = cart.length; // Count individual items, not quantities
      badge.textContent = count; // Update count immediately
      if (count > 0) {
        badge.style.display = 'inline-block';
        // Only trigger animation if explicitly requested
        if (animate) {
          badge.classList.remove('pop');
          void badge.offsetWidth; // Force reflow to restart animation
          badge.classList.add('pop');
        }
      } else {
        badge.style.display = 'none';
      }
    }
  }

  function render() {
    const container = document.getElementById('cartItems');
    const summary = document.querySelector('.card-summary');
    if (!container) return;

    const cart = readCart();
    container.innerHTML = '';

    if (!cart.length) {
      container.innerHTML = '<div class="list-group-item text-center py-4 muted">Your cart is empty.</div>';
      // Always show summary, just with ₱0
    }

    if (summary) summary.style.display = 'block';

    // Update badge (no animation on render)
    updateBadge(false);

    cart.forEach((item, idx) => {
      const el = document.createElement('div');
      el.className = 'list-group-item';
      el.innerHTML = `
        <div class="cart-item-row">
          <img src="${item.image || ''}" alt="${item.title || 'Product'}" class="cart-item-img">
          <div class="cart-item-body">
            <div class="d-flex w-100 justify-content-between align-items-start">
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
        </div>
      `;
      container.appendChild(el);
    });

    // Attach handlers
    container.querySelectorAll('.qty-input').forEach(input => {
      input.addEventListener('change', function() {
        const i = Number(this.getAttribute('data-idx'));
        const val = Math.max(1, Number(this.value) || 1);
        const cart = readCart();
        if (cart[i]) {
          cart[i].qty = val;
          writeCart(cart);
          render();
        }
      });
    });

    container.querySelectorAll('.btn-remove').forEach(btn => {
      btn.addEventListener('click', function() {
        const i = Number(this.getAttribute('data-idx'));
        const cart = readCart();
        cart.splice(i, 1);
        writeCart(cart);
        render();
      });
    });

    // Update order summary
    let total = 0;
    const list = document.getElementById('summaryList');
    if (list) list.innerHTML = '';

    cart.forEach(item => {
      total += (item.price || 0) * (item.qty || 1);
      if (list) {
        const li = document.createElement('li');
        li.className = 'd-flex justify-content-between';
        li.innerHTML = `<span>${item.title || 'Product'} ×${item.qty || 1}</span><strong>${formatPrice((item.price || 0) * (item.qty || 1))}</strong>`;
        list.appendChild(li);
      }
    });

    const subtotalEl = document.getElementById('cartSubtotal');
    if (subtotalEl) subtotalEl.textContent = formatPrice(total);

    const totalEl = document.getElementById('cartTotal');
    if (totalEl) totalEl.textContent = formatPrice(total);

    const checkoutBtn = document.getElementById('checkoutSummaryBtn');
    if (checkoutBtn) {
      if (total <= 0) {
        checkoutBtn.classList.add('disabled');
        checkoutBtn.setAttribute('aria-disabled', 'true');
      } else {
        checkoutBtn.classList.remove('disabled');
        checkoutBtn.removeAttribute('aria-disabled');
      }
    }

    // Update cart badge
    updateBadge();
  }

  // Public API
  window.Cart = {
    addToCart: function(product) {
      let cart = readCart();
      let idx = -1;
      if (product && product.id) {
        idx = cart.findIndex(i => i.id == product.id);
      }
      if (idx > -1) {
        cart[idx].qty = (cart[idx].qty || 1) + (product.qty || 1);
      } else {
        cart.push({
          id: product.id,
          title: product.title,
          price: product.price,
          image: product.image,
          description: product.description,
          qty: product.qty || 1
        });
      }
      writeCart(cart);
      render();
      
      // Update badge with animation
      updateBadge(true);
    },
    clear: function() {
      writeCart([]);
      render();
    },
    _read: readCart
  };

  // Init
  document.addEventListener('DOMContentLoaded', function() {
    render();
    updateBadge(false);
  });
})();
