jQuery(document).ready(function($) {
	// Recherche des chaines de caractère
	$("#startExtractString").click(function(e) {
		e.preventDefault()
		const c = $(this)
		c.removeClass('btn-primary')
		c.addClass('btn-light')
		c.find('.fas').addClass('fa-spin')
		c.addClass('disabled')
		$.get('/ajax/translate-string', function(data) {
			if (!data.error) {
				c.removeClass('btn-light')
				c.addClass('btn-primary')
				c.find('.fas').removeClass('fa-spin')
				c.removeClass('disabled')
				c.after('<p class="alert alert-info inner-notif mt-2">' + data.data + '</p>')
			}
		}, "JSON");
	});
	// En cours de traduction
	$("#transForm").keyup(function (e){
		e.stopPropagation();
		var c = $(this), text = c.html(), sid = c.data("id");
		if (text.length > 0) {
			$("#trans_"+sid).html(text);
		} else {
			$("#trans_"+sid).html("");
		}
		$("#setTrans").removeClass('btn-default disabled').addClass('btn-primary');
	});
	// Commancer une traduction
	$(".translate-string").click(function(e) {
		e.preventDefault()
		const c = $(this), key = c.data("key"), id = c.data("id");
		translate(key, id)
	});
	//Enregistrer les modifications
	$("#setTrans").click(function(e) {
		e.preventDefault();
		var c = $(this), str = $(".translated"), obj = [], flag = $("#transForm").data("flag");
		c.removeClass('btn-primary').addClass('btn-default disabled');
		c.find("span").addClass("fa fa-spinner fa-pulse");
		$.each(str, function(index, val) {
			var i = $("#trans_"+index), trans = $.trim(i.html()), org = $.trim(i.data("original"));
			if (trans.length > 0 && trans != "&nbsp;") {
				obj.push(org + "-:-" + trans);
			}
		});
		$.post("/ajax/translate?flag="+flag, {data: obj}, function(data, textStatus, xhr) {
			if (!data.error) {
				//c.removeClass('btn-default disabled').addClass('btn-primary');
				c.find("span").removeClass("fas fa-spinner fa-pulse");
			}
		});
	});
	// Activer ou désactiver la langue
	$(".toggle-lang").click(function () {
		var c = $(this), id = c.data('lang');
		$.get('/ajax/toggle-language?id=' + id, function(data) {
			if (data.error) {
				console.log(data.ermsg)
			}
		}, 'JSON');
	})
});

function translate(str, sid) {
	var ot = $("#trans_"+sid).html(), curpos = ot.length;
	$("#transForm").prev().html(str);
	$("#transForm").attr("contenteditable", true).focus();
	$("#transForm").data("id", sid);
	$("#transForm").html("");
	if (ot != "&nbsp;") {
		var that = $("#transForm");
		that.focus()
		that.html(ot);
		setTimeout(function(){ that.selectionStart = that.selectionEnd = ot.length; }, 0);
	}
}