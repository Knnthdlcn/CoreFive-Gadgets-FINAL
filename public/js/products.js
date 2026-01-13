// Products page - wire Add to Cart and View behavior
(function() {
  'use strict';

  function parsePrice(text) {
    if (!text) return 0;
    const t = text.replace(/[₱$,\s]/g, '').replace(/,/g, '');
    const n = Number(t);
    return isNaN(n) ? 0 : n;
  }

  function productFromCard(card) {
    // First try to get data from data-product attribute (for view button)
    const viewBtn = card.querySelector('.view-product');
    if (viewBtn && viewBtn.dataset.product) {
      try {
        const productData = JSON.parse(viewBtn.dataset.product);
        return {
          id: productData.product_id || productData.id,
          title: productData.product_name || productData.title,
          price: parseFloat(productData.price),
          image: productData.image_url || productData.image_path || productData.image,
          description: productData.description || '',
          qty: 1
        };
      } catch (e) {
        console.warn('Error parsing product data:', e);
      }
    }

    // Fallback to extracting from card elements
    const titleEl = card.querySelector('h5') || card.querySelector('.card-title');
    const title = titleEl ? titleEl.textContent.trim() : 'Product';
    
    const priceEl = card.querySelector('.card-text');
    const price = priceEl ? parsePrice(priceEl.textContent) : 0;
    
    const img = card.querySelector('img.card-img-top')?.src || '';
    const alt = card.querySelector('img.card-img-top')?.alt || title;
    
    const addBtn = card.querySelector('.add-to-cart');
    const id = addBtn?.getAttribute('data-product-id') || title;

    return { id, title, price, image: img, description: alt, qty: 1 };
  }

  function showProduct(product) {
    const modal = document.getElementById('productViewModal');
    if (!modal) return;

    document.getElementById('productModalTitle').textContent = product.title || '';
    document.getElementById('pvImage').src = product.image_url || product.image || '';
    document.getElementById('pvPrice').textContent = product.price ? ('₱' + product.price.toLocaleString()) : '';
    document.getElementById('pvDesc').textContent = product.description || '';

    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();

    const addBtn = document.getElementById('pvAddBtn');
    addBtn.onclick = () => {
      if (window.isAuthenticated === false) {
        showLoginModal();
        return;
      }
      if (window.Cart && window.Cart.addToCart) {
        window.Cart.addToCart(product);
        if (window.Toast && window.Toast.show) {
          window.Toast.show(product.title + ' added to cart');
        }
        bsModal.hide();
      }
    };

    const buyBtn = document.getElementById('pvBuyBtn');
    if (buyBtn) {
      buyBtn.onclick = (e) => {
        e.preventDefault();
        if (window.isAuthenticated === false) {
          showLoginModal();
          return;
        }
        if (window.Cart) {
          if (window.Cart.clear) window.Cart.clear();
          if (window.Cart.addToCart) window.Cart.addToCart(product);
        }
        window.location.href = '/checkout';
        bsModal.hide();
      };
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    // Wire product cards
    document.querySelectorAll('.card.h-100').forEach(card => {
      const product = productFromCard(card);

      // Wire image click to view product
      const imgWrapper = card.querySelector('.card-img-wrapper.view-product');
      if (imgWrapper) {
        imgWrapper.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();
          showProduct(product);
        });
      }

      // Wire buy now button
      const buyNowBtn = card.querySelector('.buy-now');
      if (buyNowBtn) {
        buyNowBtn.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();
          if (window.isAuthenticated === false) {
            showLoginModal();
            return;
          }
          if (window.Cart) {
            // Clear cart and add only this item for Buy Now
            if (window.Cart.clear) window.Cart.clear();
            if (window.Cart.addToCart) window.Cart.addToCart(product);
          }
          window.location.href = '/checkout';
        });
      }

      // Wire add to cart
      const addBtn = card.querySelector('.add-to-cart');
      if (addBtn) {
        addBtn.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();
          if (window.isAuthenticated === false) {
            showLoginModal();
            return;
          }
          if (window.Cart && window.Cart.addToCart) {
            window.Cart.addToCart(product);
            if (window.Toast && window.Toast.show) {
              window.Toast.show(product.title + ' added to cart');
            }
          }
        });
      }
    });
  });
})();
