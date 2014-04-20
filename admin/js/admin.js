var Site = {
    approve: function(phone) {
        return "../approve/" + phone;
    },

    approveAll: function(post) {
        return "../approve/post?post=" + encodeURIComponent (post);
    },

    reject: function(phone, reason) {
        return "../approve/" + phone + "?proof=" + encodeURIComponent (reason);
    },

    invalid: function(phone) {
        return this.reject(phone, "Некорректно собранный телефон");
    },

    contact: function(phone) {
        return this.reject(phone, "Контактный телефон");
    },

    remove: function(proof) {
        return "../reject/proof?proof=" + proof;
    },

    exists: function(proof) {
        return "../exists/proof?proof=" + proof;
    },

    change_url: function(proof, url) {
        return "../change_url/proof?proof=" + proof + "&url=" + url;
    },

    restore: function(proof) {
        return "../approve/proof?proof=" + proof;
    },

    deleteWithoutProofs: "delete-without-proofs"

};

function doRemoteAction($this, $parent) {
    return function doAction(url, callback) {
        $this.html("Подождите");
        $.getJSON(url, function(res) {
            if (res.ok) {
                $this.addClass("done");
                $this.html("Готово!");
                if (callback) callback();
            }
            else {
                $this.addClass("error");
                $this.html(res.error);
            }
            $parent.addClass("done");
        });
    }
}

$(function() {
    $(".proof-link").each(function() {
        var $this = $(this);
        var $parent = $this.parents("section");
        var $post = $this.attr("href");
        var $proof = $this.attr("id");
        var $sectionsWithSamePost = $(".proof-link[href='" + $post + "']").parents("section");
        var $buttons = $(".buttons", $sectionsWithSamePost);
        var doAction = doRemoteAction($buttons, $sectionsWithSamePost);
        var doActionProof = doRemoteAction($(".buttons", $this), $this);
        $(".approve-all", $this).click(function() {
            $this.hide();
            doAction(Site.approveAll($post), function() {

            });
            return false;

        });
        $(".restore", $this).click(function() {
            doActionProof(Site.restore($proof));
            return false;
        });
        $(".remove", $this).click(function() {
            doActionProof(Site.remove($proof));
            return false;
        });
        $(".exists", $this).click(function() {
          doActionProof(Site.exists($proof));
          return false;
        });
        $(".change_url", $this).click(function() {
          var newUrl = prompt("Введите новый URL", $post);
          if (newUrl)
            doActionProof(Site.change_url($proof, encodeURIComponent(newUrl)));
          return false;
        });


    });
    $(".buttons").each(function() {
        var $this = $(this);
        var $parent = $this.parents("section");
        var id = $parent.attr("id");
        var doAction = doRemoteAction($this, $parent);
        $(".approve", $this).click(function() {
            doAction(Site.approve(id));
        });
        $(".invalid", $this).click(function() {
            doAction(Site.invalid(id));
        });
        $(".contact", $this).click(function() {
            doAction(Site.contact(id));
        });
        $(".reject", $this).click(function() {
            var reason = prompt("Причина по которой это не мошеннический телефон (ссылка или фраза)");
            if (reason != null && reason != "") {
                doAction(Site.reject(id, reason));
            }
        });
        $(".delete-without-proofs").click(function() {
            doAction(Site.deleteWithoutProofs);
        })

    });

    $("input[name=proof_of_good]").click(function() {
        $("input[name=resolution][value=good]").attr("checked", "checked");
    })
});