function parseParams(src) {
  var pairs = src.split("&");
  var params = {};
  for (var i = 0; i < pairs.length; i++) {
    var nameVal = pairs[i].split("=");
    params[nameVal[0].toLowerCase()] = nameVal[1].toLowerCase();
  }
  return params;
}

var Blacklist = {
  checkPhone: function(phone, listener) {
    listener.onStart();
    if (this.previous) {
      this.previousAjaxListener.done = function() {};
      this.previousAjaxListener.error = function() {};
      this.previousAjaxListener.complete = function() {};
      this.previous.abort();
    }
    var ajaxListener = this.previousAjaxListener = {
      done: function(data) {
        if (data.ok) {
          listener.onData(data.result);
        }
        else {
          listener.onError("", data.error);
        }
      },
      error: function(jq, status, error) {
        if (error == "abort") return;
        listener.onError(status, error);
      },
      complete: function() {
        listener.onComplete();
      }
    };
    this.previous = $.getJSON("/check/" + phone, function(d) {ajaxListener.done(d)})
      .error(function(j,s,e) {ajaxListener.error(j,s,e)})
      .complete(function() {ajaxListener.complete()});
  }
};
var Field, Label, Form;

var State = {
  Enter: function() {
    Label.html("Введите номер телефона для проверки").removeClass();
  },
  Searching: function() {
    Label.html("Проверяю...").removeClass().addClass("search");
  },
  Complete: function() {
    if (Label.hasClass("search")) {
      Label.html("Неизвестная ошибка").removeClass().addClass("error");
    }
  },
  Ok: function() {
    Label.html("Телефон в черном списке не обнаружен").removeClass().addClass("ok");
  },
  Error: function(e) {
    Label.html(e).removeClass().addClass("error");
  },
  Found: function() {
    Label.html("Телефон в черном списке.<br> Нажмите <b>Enter</b> чтобы узнать больше").removeClass().addClass("found");
  }
};

var Widget = {

  init: function() {
    var to = null;
    var prevPhone = "";
    Field.keyup(function() {
      if (Widget.getPhone() == prevPhone) return;
      prevPhone = Widget.getPhone();
      //State.Enter();
      clearTimeout(to);
      to = setTimeout(function() {
        Widget.search();
      }, 100);
    });
    Form.on("submit", function(e) {
      if (!Widget.hasPhone()) {
        e.preventDefault();
        return false;
      }
      Form.attr("action", "/" + Widget.getPhone());
    })
  },


  hasPhone: function() {
    var p = this.getPhone();
    return p.length > 5;
  },

  getPhone: function() {
    return $.trim(Field.val()).replace(/[^0-9]/g, "");
  },

  search: function() {
    if (!this.hasPhone()) return;
    Blacklist.checkPhone(this.getPhone(), this);
  },

  onStart: function() {
    State.Searching();
  },

  onComplete: function() {
    State.Complete();
  },

  onData: function(data) {
    if (data.found) {
      this.onFound();
    }
    else {
      this.onNotFound();
    }
  },

  onFound: function() {
    State.Found();
  },

  onNotFound: function() {
    State.Ok();
  },

  onError: function(e1, e2) {
    State.Error(e1 + "<br>" + e2)
  }
};

$(function() {
  Field = $("#mnezvonil-info-field");
  Label = $("#mnezvonil-info-information");
  Form = $("form");
  Widget.init();
});