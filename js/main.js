$(function() {
    $(".main input").each(function() {
      this.focus();
    });

    $(".single-result summary").each(function() {
      $(this).
        attr("title", "Щелкните чтобы увидеть текст целиком").
        click(function() {
          $(this).slideUp();
          $(this).next(".full").slideDown();
        })
    })
});