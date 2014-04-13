$(function() {
    $(".main input[name=phone]").each(function() {
      var form = $(this).parents("form");
      $(this).focus(function() {
        form.addClass("focus");
      }).blur(function() {
        form.removeClass("focus");
      });
      this.focus();

    });

    $(".single-result .summary").each(function() {
      $(this).next(".full").hide();
      $(this).
        attr("title", "Щелкните чтобы увидеть текст целиком").
        click(function() {
          $(this).slideUp();
          $(this).next(".full").slideDown();
        })
    })
});