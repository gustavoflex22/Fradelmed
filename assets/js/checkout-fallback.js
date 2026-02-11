(function ($) {
  // Only run when WooCommerce checkout params are available.
  if (typeof wc_checkout_params === "undefined") {
    return;
  }

  var $body = $(document.body);
  var fallbackTimer = null;
  var sawCheckoutAjax = false;
  var isSubmitting = false;

  function forceShipToDifferentClosed() {
    var $checkbox = $("#ship-to-different-address-checkbox");
    if (!$checkbox.length) {
      return;
    }

    if ($checkbox.prop("checked")) {
      $checkbox.prop("checked", false).trigger("change");
    }
  }

  function isCheckoutAjax(url) {
    return typeof url === "string" && url.indexOf("wc-ajax=checkout") !== -1;
  }

  // Detect if the native WooCommerce checkout request was triggered.
  $(document).ajaxSend(function (_evt, _xhr, settings) {
    if (settings && isCheckoutAjax(settings.url)) {
      sawCheckoutAjax = true;
    }
  });

  function clearNotices() {
    $(".woocommerce-NoticeGroup, .woocommerce-error, .woocommerce-message").remove();
  }

  function renderMessages(messagesHtml) {
    if (!messagesHtml) {
      return;
    }

    clearNotices();

    var $group = $(
      '<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout"></div>'
    ).html(messagesHtml);

    var $form = $("form.checkout");
    if ($form.length) {
      $form.prepend($group);
      $body.trigger("checkout_error");

      var top = Math.max(0, ($group.offset() || { top: 0 }).top - 24);
      $("html, body").animate({ scrollTop: top }, 200);
    }
  }

  function manualCheckoutSubmit() {
    if (isSubmitting) {
      return;
    }

    var $form = $("form.checkout");
    if (!$form.length) {
      return;
    }

    if (hasCheckoutRecaptcha()) {
      return;
    }

    isSubmitting = true;
    $form.addClass("processing");

    $.ajax({
      type: "POST",
      url: wc_checkout_params.checkout_url,
      data: $form.serialize(),
      dataType: "json",
    })
      .done(function (result) {
        if (result && result.result === "success" && result.redirect) {
          window.location.href = result.redirect;
          return;
        }

        renderMessages(result && result.messages);
      })
      .fail(function () {
        renderMessages(
          '<ul class="woocommerce-error" role="alert"><li>Erro ao finalizar o pedido. Tente novamente.</li></ul>'
        );
      })
      .always(function () {
        isSubmitting = false;
        $form.removeClass("processing");
      });
  }

  function hasCheckoutRecaptcha() {
    if (typeof window.grecaptcha !== "undefined") {
      return true;
    }

    return !!document.querySelector('[data-sitekey], .g-recaptcha, .grecaptcha-badge');
  }

  function scheduleFallback() {
    sawCheckoutAjax = false;

    if (fallbackTimer) {
      window.clearTimeout(fallbackTimer);
    }

    // If no native checkout AJAX happens shortly after submit/click, force it.
    if (!hasCheckoutRecaptcha()) {
      fallbackTimer = window.setTimeout(function () {
        if (!sawCheckoutAjax) {
          manualCheckoutSubmit();
        }
      }, 1500);
    }
  }

  $body.on("click", "#place_order", scheduleFallback);
  $body.on("submit", "form.checkout", scheduleFallback);
  $(forceShipToDifferentClosed);
})(jQuery);
