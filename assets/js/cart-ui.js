// Mini-cart toggle for WooCommerce drawer
(function () {
  const isCheckoutPage = () =>
    document.body && document.body.classList.contains('woocommerce-checkout');
  const isCartPage = () =>
    document.body && document.body.classList.contains('woocommerce-cart');
  let checkoutUpdateTimer = null;
  let cartUpdateTimer = null;
  let isCheckoutUpdating = false;
  let checkoutUpdateQueued = false;
  let checkoutUpdateSafetyTimer = null;
  let isCheckoutSubmitting = false;
  let checkoutAjaxSent = false;
  let checkoutSubmittingSafetyTimer = null;

  function triggerCheckoutUpdate(delay = 120, payload = null) {
    if (!isCheckoutPage()) return;
    if (isCheckoutSubmitting) return;

    const runUpdate = () => {
      if (window.jQuery && isCheckoutUpdating) {
        checkoutUpdateQueued = true;
        return;
      }
      if (window.jQuery) {
        window.jQuery(document.body).trigger('update_checkout', payload || {});
      } else {
        document.body.dispatchEvent(new Event('update_checkout'));
      }
    };

    if (delay <= 0) {
      runUpdate();
      return;
    }

    if (checkoutUpdateTimer) {
      window.clearTimeout(checkoutUpdateTimer);
    }
    checkoutUpdateTimer = window.setTimeout(runUpdate, delay);
  }

  function triggerCartUpdate(updateButton, delay = 180) {
    if (!isCartPage() || !updateButton) return;

    const runUpdate = () => {
      updateButton.disabled = false;
      updateButton.click();
    };

    if (delay <= 0) {
      runUpdate();
      return;
    }

    if (cartUpdateTimer) {
      window.clearTimeout(cartUpdateTimer);
    }
    cartUpdateTimer = window.setTimeout(runUpdate, delay);
  }

  function bindCheckoutUpdateLifecycle() {
    if (!isCheckoutPage() || !window.jQuery || !document.body) return;
    if (document.body.dataset.checkoutLifecycleBound === 'true') return;
    document.body.dataset.checkoutLifecycleBound = 'true';

    const checkoutForm = document.querySelector('form.checkout');
    if (checkoutForm) {
      // Native HTML5 validation blocks submit on hidden required fields.
      checkoutForm.noValidate = true;
      checkoutForm.addEventListener('submit', () => {
        isCheckoutSubmitting = true;
      });
    }

    window.jQuery(document.body).on('update_checkout', () => {
      isCheckoutUpdating = true;
      if (checkoutUpdateSafetyTimer) {
        window.clearTimeout(checkoutUpdateSafetyTimer);
      }
      // Safety valve: if WooCommerce never fires updated_checkout, release the lock.
      checkoutUpdateSafetyTimer = window.setTimeout(() => {
        isCheckoutUpdating = false;
      }, 12000);
    });

    window.jQuery(document.body).on('updated_checkout checkout_error', () => {
      isCheckoutUpdating = false;
      isCheckoutSubmitting = false;
      if (checkoutUpdateSafetyTimer) {
        window.clearTimeout(checkoutUpdateSafetyTimer);
        checkoutUpdateSafetyTimer = null;
      }
      if (!checkoutUpdateQueued) return;
      checkoutUpdateQueued = false;
      triggerCheckoutUpdate(100);
    });
  }

  function bindCheckoutAjaxTracking() {
    if (!isCheckoutPage() || !window.jQuery || !document.body) return;
    if (document.body.dataset.checkoutAjaxTrackingBound === 'true') return;
    document.body.dataset.checkoutAjaxTrackingBound = 'true';

    window.jQuery(document).ajaxSend((event, jqxhr, settings) => {
      if (!settings || !settings.url) return;
      if (settings.url.indexOf('wc-ajax=checkout') !== -1) {
        checkoutAjaxSent = true;
      }
    });
  }

  function openMiniCart() {
    const wrap = document.getElementById('miniCart');
    if (wrap) wrap.classList.add('is-open');
  }

  function closeMiniCart() {
    const wrap = document.getElementById('miniCart');
    if (wrap) wrap.classList.remove('is-open');
  }

  function initMiniCart() {
    const btn = document.getElementById('headerCartBtn');
    const wrap = document.getElementById('miniCart');
    if (wrap) {
      wrap.addEventListener('click', (e) => {
        if (e.target.hasAttribute('data-close')) {
          closeMiniCart();
        }
      });

      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          closeMiniCart();
        }
      });
    }

    const shouldOpenMiniCart = btn && btn.getAttribute('data-open-mini-cart') === 'true';
    if (!btn || !wrap || !shouldOpenMiniCart) return;

    btn.addEventListener('click', (e) => {
      e.preventDefault();
      openMiniCart();
    });
  }

  function initCartPopup() {
    document.addEventListener('click', (e) => {
      const closeTarget = e.target.closest('[data-cart-popup-close]');
      if (!closeTarget) return;
      const wrapper = closeTarget.closest('.woocommerce-message');
      if (wrapper) {
        wrapper.remove();
      }
    });

    document.addEventListener('keydown', (e) => {
      if (e.key !== 'Escape') return;
      const wrapper = document.querySelector('body.single-product .woocommerce-message');
      if (wrapper) {
        wrapper.remove();
      }
    });
  }

  function initCheckoutCoupon() {
    const couponRow = document.querySelector('.checkout-coupon');
    if (!couponRow || couponRow.dataset.bound === 'true') return;

    const input = couponRow.querySelector('.checkout-coupon__input');
    const button = couponRow.querySelector('.checkout-coupon__apply');
    const form = document.querySelector('form.checkout_coupon');
    const formInput = form ? form.querySelector('#coupon_code') : null;
    if (!input || !button || !form || !formInput) return;

    couponRow.dataset.bound = 'true';

    const applyCoupon = () => {
      if (couponRow.classList.contains('is-loading')) return;

      const code = input.value.trim();
      if (!code) {
        input.focus();
        return;
      }

      const existingError = couponRow.querySelector('.coupon-error-notice');
      if (existingError) existingError.remove();

      input.classList.remove('has-error');
      input.removeAttribute('aria-invalid');
      input.removeAttribute('aria-describedby');

      if (window.jQuery && window.wc_checkout_params) {
        couponRow.classList.add('is-loading');
        button.disabled = true;
        formInput.value = code;

        window.jQuery.ajax({
          type: 'POST',
          url: window.wc_checkout_params.wc_ajax_url
            .toString()
            .replace('%%endpoint%%', 'apply_coupon'),
          data: {
            security: window.wc_checkout_params.apply_coupon_nonce,
            coupon_code: code,
            billing_email: window.jQuery('form.checkout')
              .find('input[name="billing_email"]')
              .val(),
          },
          success: function (response) {
            window.jQuery('.woocommerce-error, .woocommerce-message, .is-error, .is-success, .checkout-inline-error-message')
              .remove();

            const hasError = response && (response.indexOf('woocommerce-error') !== -1 || response.indexOf('is-error') !== -1);
            if (hasError) {
              const message = window.jQuery(response).text().trim();
              if (message) {
                input.classList.add('has-error');
                input.setAttribute('aria-invalid', 'true');
                input.setAttribute('aria-describedby', 'checkout-coupon-error');

                const error = document.createElement('span');
                error.className = 'coupon-error-notice';
                error.id = 'checkout-coupon-error';
                error.setAttribute('role', 'alert');
                error.textContent = message;
                input.parentNode.appendChild(error);
              }
            } else if (response) {
              window.jQuery('form.checkout').before(response);
              input.value = '';
              window.jQuery(document.body).trigger('applied_coupon_in_checkout', [code]);
            }

            triggerCheckoutUpdate(0, { update_shipping_method: false });
          },
          complete: function () {
            couponRow.classList.remove('is-loading');
            button.disabled = false;
          },
          dataType: 'html',
        });
      } else {
        formInput.value = code;
        form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
      }
    };

    button.addEventListener('click', (e) => {
      e.preventDefault();
      applyCoupon();
    });

    input.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        applyCoupon();
      }
    });
  }

  function clearCheckoutCouponInput() {
    const input = document.querySelector('.checkout-coupon__input');
    if (!input) return;

    input.value = '';
    input.classList.remove('has-error');
    input.removeAttribute('aria-invalid');
    input.removeAttribute('aria-describedby');

    const error = input.parentNode.querySelector('.coupon-error-notice');
    if (error) error.remove();
  }

  function initQtyStepper() {
    if (!document.body || document.body.dataset.qtyStepperBound === 'true') return;

    document.body.dataset.qtyStepperBound = 'true';

    const syncCheckoutQtyButtons = () => {
      if (!isCheckoutPage()) return;

      document.querySelectorAll('.checkout-item__controls .quantity').forEach((wrapper) => {
        const input = wrapper.querySelector('input.qty');
        const minusButton = wrapper.querySelector('.qty-button--minus');
        if (!input || !minusButton) return;

        const current = parseFloat(input.value) || 0;
        minusButton.disabled = current <= 1;
      });
    };

    document.addEventListener('click', (e) => {
      const removeButton = e.target.closest('.checkout-item__remove');
      if (removeButton) {
        e.preventDefault();
        const row = removeButton.closest('tr');
        const input = row ? row.querySelector('input.qty') : null;
        if (input) {
          input.value = 0;
          input.dispatchEvent(new Event('input', { bubbles: true }));
          input.dispatchEvent(new Event('change', { bubbles: true }));
        }
        triggerCheckoutUpdate();
        return;
      }

      const button = e.target.closest('.qty-button');
      if (!button) return;

      const wrapper = button.closest('.quantity');
      const input = wrapper ? wrapper.querySelector('input.qty') : null;
      if (!input) return;

      const step = parseFloat(input.getAttribute('step')) || 1;
      const min = parseFloat(input.getAttribute('min'));
      const max = parseFloat(input.getAttribute('max'));
      const current = parseFloat(input.value) || 0;
      const isPlus = button.classList.contains('qty-button--plus');
      const decimals = (step.toString().split('.')[1] || '').length;
      const isCheckout = isCheckoutPage();
      const minValue = !Number.isNaN(min) ? min : 0;
      const safeMin = isCheckout ? Math.max(1, minValue) : minValue;

      if (isCheckout && !isPlus && current <= 1) {
        syncCheckoutQtyButtons();
        return;
      }

      let nextValue = isPlus ? current + step : current - step;

      if (!Number.isNaN(safeMin)) {
        nextValue = Math.max(nextValue, safeMin);
      }

      if (!Number.isNaN(max) && max > 0) {
        nextValue = Math.min(nextValue, max);
      }

      input.value = decimals ? nextValue.toFixed(decimals) : nextValue;
      input.dispatchEvent(new Event('input', { bubbles: true }));
      input.dispatchEvent(new Event('change', { bubbles: true }));

      if (isCartPage()) {
        const form = button.closest('form.woocommerce-cart-form');
        const updateButton = form ? form.querySelector('button[name="update_cart"]') : null;
        triggerCartUpdate(updateButton);
      }

      syncCheckoutQtyButtons();
      triggerCheckoutUpdate();
    });

    document.addEventListener('change', (e) => {
      if (isCheckoutPage() && e.target && e.target.matches('.woocommerce-checkout input.qty')) {
        syncCheckoutQtyButtons();
        triggerCheckoutUpdate();
      }

      if (isCartPage() && e.target && e.target.matches('.woocommerce-cart input.qty')) {
        const form = e.target.closest('form.woocommerce-cart-form');
        const updateButton = form ? form.querySelector('button[name="update_cart"]') : null;
        triggerCartUpdate(updateButton);
      }
    });

    if (window.jQuery) {
      window.jQuery(document.body).on('updated_checkout', syncCheckoutQtyButtons);
    }

    syncCheckoutQtyButtons();
  }

  function initCepAutoFill() {
    const fieldsMap = [
      {
        scope: 'billing',
        postcode: '#billing_postcode',
        address1: '#billing_address_1',
        address2: '#billing_address_2',
        city: '#billing_city',
        state: '#billing_state',
        neighborhood: '#billing_neighborhood',
        country: '#billing_country',
      },
      {
        scope: 'shipping',
        postcode: '#shipping_postcode',
        address1: '#shipping_address_1',
        address2: '#shipping_address_2',
        city: '#shipping_city',
        state: '#shipping_state',
        neighborhood: '#shipping_neighborhood',
        country: '#shipping_country',
      },
      {
        scope: 'calculator',
        postcode: '#calc_shipping_postcode',
        address1: '#calc_shipping_address_1',
        city: '#calc_shipping_city',
        state: '#calc_shipping_state',
        neighborhood: '#calc_shipping_neighborhood',
        country: '#calc_shipping_country',
      },
    ];

    const cepCache = new Map();
    const pendingCep = new Map();

    const sanitizeCep = (value) => (value || '').replace(/\D/g, '');

    const setValue = (selector, value, options = {}) => {
      if (!selector || !value) return;
      const field = document.querySelector(selector);
      if (!field) return;
      const shouldOverwrite = options.overwrite === true;
      if (!shouldOverwrite && field.value && field.value.trim()) return;
      field.value = value;
      field.dispatchEvent(new Event('input', { bubbles: true }));
      field.dispatchEvent(new Event('change', { bubbles: true }));
    };

    const clearValue = (selector) => {
      if (!selector) return;
      const field = document.querySelector(selector);
      if (!field || !field.value) return;
      field.value = '';
      field.dispatchEvent(new Event('input', { bubbles: true }));
      field.dispatchEvent(new Event('change', { bubbles: true }));
    };

    const setStateValue = (selector, value) => {
      if (!selector || !value) return;
      const field = document.querySelector(selector);
      if (!field) return;
      if (field.tagName === 'SELECT') {
        const optionExists = Array.from(field.options).some((option) => option.value === value);
        if (!optionExists) return;
      }
      field.value = value;
      field.dispatchEvent(new Event('input', { bubbles: true }));
      field.dispatchEvent(new Event('change', { bubbles: true }));
    };

    const fetchCep = (cep) => {
      if (cepCache.has(cep)) {
        return Promise.resolve(cepCache.get(cep));
      }
      if (pendingCep.has(cep)) {
        return pendingCep.get(cep);
      }

      const request = fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then((response) => (response.ok ? response.json() : null))
        .then((data) => {
          if (!data || data.erro) return null;
          cepCache.set(cep, data);
          return data;
        })
        .catch(() => null)
        .finally(() => {
          pendingCep.delete(cep);
        });

      pendingCep.set(cep, request);
      return request;
    };

    const bindCepField = (fields) => {
      const input = document.querySelector(fields.postcode);
      if (!input || input.dataset.cepBound === 'true') return;
      input.dataset.cepBound = 'true';

      const handleCepLookup = () => {
        const cep = sanitizeCep(input.value);
        if (cep.length !== 8) return;
        if (input.dataset.cepLookup === cep) return;

        input.dataset.cepLookup = cep;
        input.setAttribute('aria-busy', 'true');

        fetchCep(cep).then((data) => {
          input.removeAttribute('aria-busy');
          if (!data) {
            delete input.dataset.cepLookup;
            return;
          }

          if (sanitizeCep(input.value) !== cep) return;

          setValue(fields.country, 'BR', { overwrite: true });
          setValue(fields.address1, data.logradouro, { overwrite: true });
          setValue(fields.address2, data.complemento, { overwrite: true });
          setValue(fields.neighborhood, data.bairro, { overwrite: true });
          setValue(fields.city, data.localidade, { overwrite: true });
          setStateValue(fields.state, data.uf);

          triggerCheckoutUpdate();
        });
      };

      input.addEventListener('blur', handleCepLookup);
      input.addEventListener('input', () => {
        const currentCep = sanitizeCep(input.value);
        if (!currentCep) {
          clearValue(fields.address1);
          clearValue(fields.address2);
          clearValue(fields.neighborhood);
          clearValue(fields.city);
          clearValue(fields.state);
          delete input.dataset.cepLookup;
          triggerCheckoutUpdate();
          return;
        }

        if (currentCep.length === 8) {
          handleCepLookup();
        }
      });
    };

    fieldsMap.forEach(bindCepField);
  }

  function initAddressAutocomplete() {
    const configs = [
      {
        scope: 'billing',
        address1: '#billing_address_1',
        postcode: '#billing_postcode',
        address2: '#billing_address_2',
        city: '#billing_city',
        state: '#billing_state',
        country: '#billing_country',
        neighborhood: '#billing_neighborhood',
      },
      {
        scope: 'shipping',
        address1: '#shipping_address_1',
        postcode: '#shipping_postcode',
        address2: '#shipping_address_2',
        city: '#shipping_city',
        state: '#shipping_state',
        country: '#shipping_country',
        neighborhood: '#shipping_neighborhood',
      },
    ];

    const cache = new Map();
    const pending = new Map();
    const minLength = 3;
    const maxItems = 6;

    const normalize = (value) => (value || '').toString().trim().replace(/\s+/g, ' ');

    const setValue = (selector, value, options = {}) => {
      if (!selector || !value) return;
      const field = document.querySelector(selector);
      if (!field) return;
      const shouldOverwrite = options.overwrite === true;
      if (!shouldOverwrite && field.value && field.value.trim()) return;
      field.value = value;
      field.dispatchEvent(new Event('input', { bubbles: true }));
      field.dispatchEvent(new Event('change', { bubbles: true }));
    };

    const setStateValue = (selector, value) => {
      if (!selector || !value) return;
      const field = document.querySelector(selector);
      if (!field) return;
      if (field.tagName === 'SELECT') {
        const optionExists = Array.from(field.options).some((option) => option.value === value);
        if (!optionExists) return;
      }
      field.value = value;
      field.dispatchEvent(new Event('input', { bubbles: true }));
      field.dispatchEvent(new Event('change', { bubbles: true }));
    };

    const fetchAddress = (uf, city, query) => {
      const key = `${uf}|${city}|${query}`.toLowerCase();
      if (cache.has(key)) return Promise.resolve(cache.get(key));
      if (pending.has(key)) return pending.get(key);

      const request = fetch(`https://viacep.com.br/ws/${encodeURIComponent(uf)}/${encodeURIComponent(city)}/${encodeURIComponent(query)}/json/`)
        .then((response) => (response.ok ? response.json() : []))
        .then((data) => {
          if (!Array.isArray(data)) return [];
          const items = data.filter((item) => item && !item.erro);
          cache.set(key, items);
          return items;
        })
        .catch(() => [])
        .finally(() => {
          pending.delete(key);
        });

      pending.set(key, request);
      return request;
    };

    const ensureList = (input, scope) => {
      const listId = `fm-address-suggest-${scope}`;
      let list = document.getElementById(listId);
      if (list) return list;
      list = document.createElement('div');
      list.id = listId;
      list.className = 'fm-address-suggest';
      list.setAttribute('role', 'listbox');
      list.hidden = true;
      input.insertAdjacentElement('afterend', list);
      input.setAttribute('aria-controls', listId);
      input.setAttribute('aria-autocomplete', 'list');
      return list;
    };

    const clearList = (list) => {
      if (!list) return;
      list.innerHTML = '';
      list.hidden = true;
    };

    const renderList = (list, items, fields) => {
      if (!list) return;
      list.innerHTML = '';
      if (!items || items.length === 0) {
        list.hidden = true;
        return;
      }

      items.slice(0, maxItems).forEach((item) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'fm-address-suggest__item';

        const main = document.createElement('span');
        main.className = 'fm-address-suggest__main';
        const bairro = item.bairro ? ` - ${item.bairro}` : '';
        main.textContent = `${item.logradouro || ''}${bairro}, ${item.localidade || ''}/${item.uf || ''}`;

        const meta = document.createElement('span');
        meta.className = 'fm-address-suggest__meta';
        meta.textContent = item.cep || '';

        button.appendChild(main);
        button.appendChild(meta);

        button.addEventListener('click', () => {
          const input = document.querySelector(fields.address1);
          if (input) {
            input.dataset.addrSuggestSelecting = 'true';
            window.setTimeout(() => {
              delete input.dataset.addrSuggestSelecting;
            }, 0);
          }
          setValue(fields.address1, item.logradouro || '', { overwrite: true });
          setValue(fields.neighborhood, item.bairro || '', { overwrite: true });
          setValue(fields.city, item.localidade || '', { overwrite: true });
          setStateValue(fields.state, item.uf || '');
          setValue(fields.postcode, item.cep || '', { overwrite: true });
          setValue(fields.country, 'BR', { overwrite: true });
          clearList(list);

          if (document.body && document.body.classList.contains('woocommerce-checkout')) {
            triggerCheckoutUpdate(80);
          }
        });

        list.appendChild(button);
      });

      list.hidden = false;
    };

    const bindAddressInput = (fields) => {
      const input = document.querySelector(fields.address1);
      if (!input || input.dataset.addrSuggestBound === 'true') return;

      input.dataset.addrSuggestBound = 'true';
      const list = ensureList(input, fields.scope);
      let timer = null;

      const hideListLater = () => {
        window.clearTimeout(timer);
        timer = window.setTimeout(() => clearList(list), 150);
      };

      const handleLookup = () => {
        const query = normalize(input.value);
        if (query.length < minLength) {
          clearList(list);
          return;
        }

        const cityField = document.querySelector(fields.city);
        const stateField = document.querySelector(fields.state);
        const city = normalize(cityField ? cityField.value : '');
        const uf = normalize(stateField ? stateField.value : '').toUpperCase();

        if (!city || !uf) {
          clearList(list);
          return;
        }

        fetchAddress(uf, city, query).then((items) => {
          if (normalize(input.value) !== query) return;
          renderList(list, items, fields);
        });
      };

      input.addEventListener('input', () => {
        if (input.dataset.addrSuggestSelecting === 'true') {
          clearList(list);
          return;
        }
        window.clearTimeout(timer);
        timer = window.setTimeout(handleLookup, 300);
      });

      input.addEventListener('focus', handleLookup);
      input.addEventListener('blur', hideListLater);

      input.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
          clearList(list);
        }
      });

      list.addEventListener('mousedown', (event) => {
        event.preventDefault();
      });
    };

    if (document.body && document.body.dataset.addrSuggestDocBound !== 'true') {
      document.body.dataset.addrSuggestDocBound = 'true';
      document.addEventListener('click', (event) => {
        const list = event.target.closest('.fm-address-suggest');
        const input = event.target.closest('input');
        if (list || input) return;
        document.querySelectorAll('.fm-address-suggest').forEach((panel) => clearList(panel));
      });
    }

    configs.forEach(bindAddressInput);
  }

  function initShipToDifferentAddress() {
    const checkbox = document.querySelector('#ship-to-different-address-checkbox');
    if (!checkbox || checkbox.dataset.shipToggleBound === 'true') return;
    checkbox.dataset.shipToggleBound = 'true';

    const container = document.querySelector('.shipping_address');
    const clearFields = () => {
      if (!container) return;
      container.querySelectorAll('input, select, textarea').forEach((field) => {
        if (field.type === 'checkbox' || field.type === 'radio') return;
        if (field.value === '') return;
        field.value = '';
        field.dispatchEvent(new Event('input', { bubbles: true }));
        field.dispatchEvent(new Event('change', { bubbles: true }));
      });
    };

    if (checkbox.checked) {
      checkbox.checked = false;
      if (window.jQuery) {
        window.jQuery(checkbox).trigger('change');
      } else {
        checkbox.dispatchEvent(new Event('change', { bubbles: true }));
      }
    }

    checkbox.addEventListener('change', () => {
      if (checkbox.checked) {
        clearFields();
      }
    });
  }

  function initCheckoutSubmitGuard() {
    if (!isCheckoutPage()) return;
    const form = document.querySelector('form.checkout');
    const button = document.querySelector('#place_order');
    if (!form || !button || button.dataset.submitGuardBound === 'true') return;
    button.dataset.submitGuardBound = 'true';

    // Some validation flows block the native submit; ensure WooCommerce sees it.
    button.addEventListener('click', (event) => {
      event.preventDefault();
      event.stopPropagation();
      checkoutAjaxSent = false;
      window.setTimeout(() => {
        if (window.jQuery) {
          window.jQuery(form).trigger('submit');
        } else if (form.requestSubmit) {
          form.requestSubmit();
        } else {
          form.submit();
        }
      }, 0);

      window.setTimeout(() => {
        if (checkoutAjaxSent) return;
        if (form.classList.contains('processing')) return;
        if (!window.jQuery || !window.wc_checkout_params) return;
        if (form.dataset.forceSubmitted === 'true') return;
        form.dataset.forceSubmitted = 'true';

        const $form = window.jQuery(form);
        const paymentMethod = $form.find('input[name="payment_method"]:checked').val();
        let proceed = true;
        if (paymentMethod) {
          proceed = $form.triggerHandler(`checkout_place_order_${paymentMethod}`) !== false;
        }
        if (proceed) {
          proceed = $form.triggerHandler('checkout_place_order') !== false;
        }
        if (!proceed) return;

        $form.addClass('processing');
        const endpoint = window.wc_checkout_params.wc_ajax_url
          .toString()
          .replace('%%endpoint%%', 'checkout');

        window.jQuery.ajax({
          type: 'POST',
          url: endpoint,
          data: $form.serialize(),
          dataType: 'json',
          success: (result) => {
            if (result && result.result === 'success' && result.redirect) {
              window.location.href = result.redirect;
              return;
            }
            if (result && result.messages) {
              const wrapper = document.createElement('div');
              wrapper.innerHTML = result.messages;
              form.prepend(wrapper);
            }
            window.jQuery(document.body).trigger('checkout_error');
          },
          error: () => {
            window.jQuery(document.body).trigger('checkout_error');
          },
          complete: () => {
            $form.removeClass('processing');
            delete form.dataset.forceSubmitted;
          },
        });
      }, 900);
    });
  }

  function init() {
    initMiniCart();
    initCartPopup();
    initCheckoutCoupon();
    initQtyStepper();
    initCepAutoFill();
    initAddressAutocomplete();
    initShipToDifferentAddress();
    initCheckoutSubmitGuard();
    bindCheckoutUpdateLifecycle();
    bindCheckoutAjaxTracking();

    if (window.jQuery) {
      window.jQuery(document.body).on('checkout_place_order', () => {
        isCheckoutSubmitting = true;
        if (checkoutSubmittingSafetyTimer) {
          window.clearTimeout(checkoutSubmittingSafetyTimer);
        }
        // Safety valve: never block submissions indefinitely.
        checkoutSubmittingSafetyTimer = window.setTimeout(() => {
          isCheckoutSubmitting = false;
        }, 8000);
      });
      window.jQuery(document.body).on('checkout_error', () => {
        isCheckoutSubmitting = false;
        if (checkoutSubmittingSafetyTimer) {
          window.clearTimeout(checkoutSubmittingSafetyTimer);
          checkoutSubmittingSafetyTimer = null;
        }

        const shell = document.querySelector('.fradel-checkout-shell');
        if (shell) {
          shell.classList.remove('fradel-step-2', 'fradel-step-3');
          shell.classList.add('fradel-step-1');
        }

        const notices = document.querySelector(
          '.woocommerce-NoticeGroup, .woocommerce-error, .woocommerce-notices-wrapper'
        );
        if (notices) {
          notices.scrollIntoView({ behavior: 'smooth', block: 'start' });
          // Surface the server validation message during debugging.
          console.error('checkout_error:', notices.textContent.trim());
        } else {
          window.scrollTo({ top: 0, behavior: 'smooth' });
        }
      });
      window.jQuery(document.body).on('applied_coupon_in_checkout', clearCheckoutCouponInput);
      window.jQuery(document.body).on('updated_checkout', initCheckoutCoupon);
      window.jQuery(document.body).on('updated_checkout', initCepAutoFill);
      window.jQuery(document.body).on('updated_checkout', initAddressAutocomplete);
      window.jQuery(document.body).on('updated_checkout', initShipToDifferentAddress);
      window.jQuery(document.body).on('updated_checkout', initCheckoutSubmitGuard);
      window.jQuery(document.body).on('updated_checkout', bindCheckoutUpdateLifecycle);
      window.jQuery(document.body).on('updated_checkout', bindCheckoutAjaxTracking);
    } else if (document.body) {
      document.body.addEventListener('applied_coupon_in_checkout', clearCheckoutCouponInput);
    }
  }

  document.addEventListener('DOMContentLoaded', init);
})();
