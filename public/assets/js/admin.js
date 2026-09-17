var origin = window.location.origin
moment.locale('fr');
jQuery(document).ready(function($) {
	//Effacer les alerts
	window.setTimeout(function() {
    $(".inner-notif").fadeTo(500, 0).slideUp(500, function(){
		$(this).remove(); 
    });
	}, 7000);
	//Datepicker
	if ($(".datepicker").html() !== undefined) {
		if (jQuery().datepicker) {
			$('.datepicker').datepicker({
				autoclose: true
			});
		}
	}
	//DataTable
	var t = $(".dataTable");
	if (t.html() !== undefined) {
		if (jQuery().dataTable !== undefined) {
			$(".dataTable").DataTable({
				"language": {
					url: '/assets/json/datatable/fr-FR.json',
				},
				paging: false,
				fixedHeader: true
			});
		}
	}
	//Lorsque la zone de titre des posts change
	$("#titre").change(function () {
		let c = $(this), title = c.val(), titre = $.trim(title), post = $("#idpost").val(), lien = $("#lien").val(), idpost = (post !== undefined)?post:false;
		if (titre.length > 1) {
			if (lien.length == 0) {
				genlink(titre,idpost);
			}
		}
	});
	// Lorsque la zone de lien des posts change
	$("#lien").change(function () {
		let c = $(this), title = c.val(), titre = $.trim(title), post = $("#idpost").val(), idpost = (post !== undefined)?post:false;
		if (titre.length > 1) {
			genlink(titre,idpost);
		}
	});
	//Lorsque la zone de titre des catégories change
	$("#catitre").change(function () {
		let c = $(this), title = c.val(), titre = $.trim(title), link = $("#catlink").val(), cat = $("#catid").val(), catid = (cat !== undefined)?cat:false;
		if (titre.length > 1) {
			if (link.length == 0) {
				$("#catlink").removeAttr("disabled");
				gencatlink(titre, catid);
			}
		}
	});
	// Lorsque la zone de lien des catégories change
	$("#catlink").change(function () {
		let c = $(this), title = c.val(), titre = $.trim(title), cat = $("#catid").val(), catid = (cat !== undefined)?cat:false;
		if (titre.length > 1) {
			$("#catlink").removeAttr("readonly");
			gencatlink(titre,catid);
		}
	});
	// Traduires les contenus
	$(".setTranslate").click(function (e) {
		let c = $(this), id = c.data("id"), type = c.data("type"), link = c.data("link"), userid = $.cookie('jxbR63gi');
		let dlg = $.dialog({
			title: "Choisissez une langue",
			type: "purple",
			typeAnimated: true,
			content: function() {
				let self = this;
				return $.get(ajaxUrl+"getlanguages/"+id+"/"+type, function(data) {
					if (data.error == false) {
						let cnt = `<div class="text-center">`, lang = data.langs;
						$.each(lang, function(k, v) {
							let src = `/assets/images/lang/${v.code}_rect.png`, flag, tp, tid;
							if (type == "post") {
								tp = v.post;
								tid = tp.idpost;
							} else if (type == "slider") {
								tp = v.slider;
								tid = tp.idslider;
							} else {
								tp = v.cat;
								tid = tp.cat_id;
							}
							if (tp) {
								let href = (tp.user == userid || userid == 1)?link + `.html?id=${tid}&lang=${v.idlang}&original_id=${id}`:'javascript:void(0)';
								if (k != 0) {
									flag = `&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="${href}"><img src="${src}"></a>`;
								} else {
									flag = `<a href="${href}"><img src="${src}"></a>`;
								}
							} else {
								if (k != 0) {
									flag = `&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="${link}.html?lang=${v.idlang}&original_id=${id}"><img src="${src}"></a>`;
								} else {
									flag = `<a href="${link}.html?lang=${v.idlang}&original_id=${id}"><img src="${src}"></a>`;
								}
							}
							cnt += flag;
						});
						cnt += "</div>";
						self.setContent(cnt);
					} else {
						self.close();
					}
				}, "JSON");
			},
			onContentReady: function (){
				setTimeout(function () {
					dlg.close();
				}, 8000)
			}
		});
	});
	// Visibilité des options de titre
	$(".tr-title").mouseover(function (){
		var c = $(this), v = c.find(".action-td");
		v.css('visibility', 'visible');
	});
	$(".tr-title").mouseout(function (){
		var c = $(this), v = c.find(".action-td");
		v.css('visibility', 'hidden');
	});
	// Visibilité des options de titre
	$(".td-title").mouseover(function (){
		var c = $(this), v = c.find(".action-post");
		v.css('visibility', 'visible');
	});
	$(".td-title").mouseout(function (){
		var c = $(this), v = c.find(".action-post");
		v.css('visibility', 'hidden');
	});
	//Enrgistrer un article comme souscriptible
	$(".setsubscribe").click(function(){
		let c = $(this), vl;
		if (c.is(":checked")) {
			vl = 1;
		} else {
			vl = 0;
		}
		c.next("input").val(vl);
	});
	//Enrgistrer un article comme inscriptible
	$(".setregister").click(function(){
		let c = $(this), vl;
		if (c.is(":checked")) {
			vl = 1;
		} else {
			vl = 0;
		}
		c.next("input").val(vl);
	});
	/**
	 * Confirmation avant suppression
	 */
	$('body').on('click', '.del', function(e) {
		var c = $(this), msg = c.data("msg"), link = c.attr("href");
		if (msg !== undefined) {
			e.preventDefault();
			$.confirm({
				title: 'A votre attention!',
				type: "red",
				typeAnimated: true,
				content: msg,
				buttons: {
					OK: {
						text: "Oui",
						btnClass: "btn-blue",
						action: function (a) {
							window.location.href = link;
						}
					},
					NO: {
						text: "Non",
						btnClass: "btn-red",
						action: function (a) {}
					}
				}
			})
		}
	});
	//Voir le modal
	var sm = $(".showmodal");
	if (sm !== undefined && sm.length !== 0) {
		sm.click(function (e){
			e.preventDefault();
			var c = $(this), content = c.data("content"), title = c.data("title"), wd = c.data("width");
			$.dialog({
				title: title,
				type: 'blue',
				typeAnimated: true,
				columnClass: (wd !== undefined)?wd:"small",
				content: content
			})
		})
	}
	//Enlever l'image à la une
	$("#img_preview").on("click", ".rem-laune", function (){
		let c = $(this), p = c.parent(), input = p.next("input"), idpost = c.data("idpost"), userid = c.data('userid');
		p.empty();
		input.val("");
		if (idpost !== undefined) {
			let  dlg = loading();
			$.get(ajaxUrl+"remove-laune/"+idpost, function(data) {
				dlg.close();
				if (data.error == true) {
					info("danger", data.ermsg);
				}
			}, "JSON");
		} else if (userid !== undefined) {
			let dlg = loading();
			$.get(ajaxUrl+"remove-user-avatar/"+userid, function(data) {
				dlg.close();
				if (data.error == true) {
					info("danger", data.ermsg);
				}
			}, "JSON");
		}
	});
	//Enlever les images mise en avant
	$(".product-illustration").on("click",".remove-laune", function (e) {
		e.preventDefault();
		const c = $(this), p = c.parent(), idpost = $('#idpost').val();
		$.confirm({
			title: 'A votre attention',
			type: 'red',
			typeAnimated: true,
			content: `Voulez-vous vraiment supprimer cette image de produit?`,
			buttons: {
				yes: {
					text: 'Oui',
					btnClass: 'btn-danger',
					action: () => {
						const key = p.next('input').attr('id')
						if (idpost) {
							$.getScript('/ajax/remove-product-img/' + key + '/' + idpost, function(data) {
								if (!data.error) {
									p.html(`
										<a
											href="javascript:void(0)"
											data-backdrop="static"
											data-keyboard="false"
											data-toggle="modal"
											data-target="#showFileList"
											data-input="img1"
											data-preview="image1"
											class="load-images"><i class="fa fa-plus fa-2x"></i>
										</a>
									`)
									p.next('input').val('');
								} else {
									info('danger', data.ermsg);
								}
							}, 'JSON');
						} else {
							p.html(`
								<a
									href="javascript:void(0)"
									data-backdrop="static"
									data-keyboard="false"
									data-toggle="modal"
									data-target="#showFileList"
									data-input="img1"
									data-preview="image1"
									class="load-images"><i class="fa fa-plus fa-2x"></i>
								</a>
							`)
							p.next('input').val('');
						}
					}
				},
				no: {
					text: 'Non',
					role: 'cancel',
					btnClass: 'btn-primary'
				}
			}
		});
	});
	//Voir les mots de passe
	$(".viewpass button").click(function () {
		let c = $(this), icon = c.find("i"), input = c.parent().prev(), at = input.attr("type");
		if (at == "password") {
			input.attr("type","text");
			icon.removeClass('fa-eye');
			icon.addClass('fa-eye-slash');
		} else {
			input.attr("type","password");
			icon.removeClass('fa-eye-slash');
			icon.addClass('fa-eye');
		}
	});
	//Voir les mots de passe 2
	$(".toggle-view-password").click(function () {
		const c = $(this);
		const icon = c.find("i");
		const input = c.prev();
		const at = input.attr("type");
		if (at == "text") {
			input.attr("type","password");
			icon.removeClass('fa-eye');
			icon.addClass('fa-eye-slash');
		} else {
			input.attr("type","text");
			icon.removeClass('fa-eye-slash');
			icon.addClass('fa-eye');
		}
	});
	// Sélectionner tous les fichiers pour un dossier
	$("#checkAll").click(function() {
		const c = $(this)
		if (c.is(':checked')) {
			$('.select-this').prop('checked', true)
		} else {
			$('.select-this').prop('checked', false)
		}
	})
	/**
	 * Image à la une
	 */
	$('.load-images').click(function(){
		const c = $(this), input = c.data('input'), preview = c.data('preview');
		// Afficher l'Iframe des fichiers
		$('#iframeFiles').html(`<iframe src="/admin/media/iframe?type=1&src=tempFile&id=tempFileId&input=${input}&preview=${preview}" frameborder="0" width="768" height="500"></iframe>`)
		window.addEventListener('insertLaune', onInsertLaune, false);
		function onInsertLaune(event) {
			console.log(event.origin);
		}
		// En voulant fermer la fenêtre
		$('#showFileList').on('hide.bs.modal', function (event) {
		  // $("#tempFile").val('')
		})
		// Insérer l'image à la place indiquée
		$('#insertBtn').click(function(){
			const file = $('#tempFile').val(), id = $('#tempFileId').val(), fileInput = $('#fileInput').val(), filePreview = $('#filePreview').val();
			if (file.length > 0) {
				if (fileInput.length > 0) {
					const reg = /img([0-9]+)/i
					if (reg.test(fileInput)) {
						$('#' + fileInput).val(file);
					} else {
						$('#' + fileInput).val(id);
					}
					$('#' + filePreview).html(`
						<img src="/uploads/150x150/${file}">
						<span class="remove-laune text-danger" data-id="${id}"><i class="fa fa-times-circle fa-2x"></i></span>
					`);
				}
			}
			$('#showFileList').modal('hide')
		})
	})
	// Supprimer l'image à la une une
	$('#launePreview').on('click', '.remove-laune', function(){
		const c = $(this), id = c.data('id'), parent = c.parent(), post = $('#idpost').val(), cat = c.data('cat');
		$.confirm({
			title: 'A votre attention',
			type: 'red',
			typeAnimated: true,
			content: 'Voulez-vous vraiment supprimer l\'image à la une',
			buttons: {
				okText: {
					text: 'Oui',
					btnClass: 'btn btn-danger',
					action: function() {
						if (post !== undefined) {
							const load = loading()
							$.get('/ajax/remove-laune/' + post, function(data) {
								load.close()
								if (data.error) {
									errorDialog(data.ermsg)
								} else {
									parent.html('')
									$('#laune').val('')
								}
							}, 'JSON')
						} else if (cat !== undefined) {
							/*const original = window.location.href.indexOf('original=');
							console.log(original)*/
							const load = loading()
							$.post('/ajax/remove-category-laune', {cat}, function(data) {
								load.close()
								if (data.error) {
									errorDialog(data.ermsg)
								} else {
									parent.html('')
									$('#laune').val('')
								}
							}, 'JSON')
						} else {
							parent.html('')
							$('#laune').val('')
						}
					}
				},
				noText: {
					text: 'Non',
					role: 'cancel',
					btnClass: 'btn btn-primary'
				}
			}
		})
	})
	/**
	 * Insérer un fichier
	 */
	$('.load-file').click(function(){
		const c = $(this), input = c.data('input'), preview = c.data('preview'), remove = c.data('remove');
		// Afficher l'Iframe des fichiers
		$('#iframeFiles').html(`<iframe src="/admin/media/iframe?type=2&src=tempFile&id=tempFileId&input=${input}&preview=${preview}" frameborder="0" width="768" height="500"></iframe>`)
		// En voulant fermer la fenêtre
		$('#showFileList').on('hide.bs.modal', function (event) {
		  // $("#tempFile").val('')
		})
		// Insérer le fichier à la place indiquée
		$('#insertBtn').click(function(){
			const file = $('#tempFile').val(), id = $('#tempFileId').val(), fileInput = $('#fileInput').val(), filePreview = $('#filePreview').val();
			if (file.length > 0) {
				if (fileInput == 'event_file') {
					$('#' + fileInput).val(file);
				} else {
					$('#' + fileInput).val(id);
				}
				$('#' + remove).removeClass('d-none').attr('data-id', id);
				$('#' + filePreview).html(`
					<span>${origin}/uploads/${file}</span>
				`);
			}
			$('#showFileList').modal('hide')
		})
	})
	// Supprimer le fichier uploader
	$('#loadFile').on('click', '.remove-file', function(){
		const c = $(this), id = c.data('id'), preview = c.prev('a').find('.input'), idpost = $('#idpost').val();
		$.confirm({
			title: 'A votre attention',
			type: 'red',
			typeAnimated: true,
			content: 'Voulez-vous vraiment supprimer ce fichier?',
			buttons: {
				okText: {
					text: 'Oui',
					btnClass: 'btn btn-danger',
					action: function() {
						if (idpost !== undefined) {
							const loading = wait()
							$.get('/ajax/remove-post-file/' + idpost, function(data) {
								loading.close()
								if (data.error) {
									errorDialog(data.ermsg)
								} else {
									preview.html('')
									$('.file-input').val('')
									c.addClass('d-none')
								}
							}, 'JSON')
						} else {
							preview.html('')
							$('.file-input').val('')
							c.addClass('d-none')
						}
					}
				},
				noText: {
					text: 'Non',
					role: 'cancel',
					btnClass: 'btn btn-primary'
				}
			}
		})
	})
	//Vérification constante de la session après 15 sec
	/*setInterval(function () {
		$.get(ajaxUrl, function(data) {
			if (data.error == true) {
				let hash = encodeURIComponent(window.location.href);
				window.location.href = url+"/account/lockscreen.html?redir="+hash;
			}
		}, "JSON");
	}, 15000);*/
	// Enregistrer l'état du mode maintenance
	$('#maintenanceState').change(function(e) {
		e.preventDefault();
		$.get('/ajax/set-maintenance');
	});
	// Liste des utilisateurs enregistrés
	const users = $("#listUsers");
	if (users.html() !== undefined) {
		if (jQuery().dataTable !== undefined) {
			const tab = users.data('banned');
			const editText = users.data('edit-text');
			const groupText = users.data('group-text');
			const permText = users.data('perm-text');
			const banText = users.data('ban-text');
			const restoreText = users.data('restore-text');
			users.dataTable({
				ajax: `/ajax/get-users/${tab}`,
				dataSrc: 'data',
				order: [],
				columns: [
					{data: 'nb'},
					{data: 'nom'},
					{data: 'prenoms'},
					{data: 'sexe'},
					{data: (data, type) => {
						let action;
						if (tab == 0) {
							action = `
								<a href="/admin/users/add?id=${data.id}" class="btn btn-primary btn-xs" title="${editText}"><i class="fas fa-edit"></i></a>&nbsp;
								<a href="/admin/users/user-to-group?id=${data.id}" class="btn btn-info btn-xs" title="${groupText}"><i class="fas fa-users"></i></a>&nbsp;
								<a href="/admin/users/perm-to-user?id=${data.id}" class="btn btn-success btn-xs" title="${permText}"><i class="fas fa-at"></i></a>&nbsp;
								<a href="/admin/users?ban=${data.id}" class="btn btn-danger btn-xs del" data-msg="Voulez-vous vraiment bannir cet utilisateur?" title="${banText}"><i class="fas fa-times"></i></a>
							`;
						} else {
							action = `<a href="/admin/users?unban=${data.id}" class="btn btn-info btn-xs" title="${restoreText}"><i class="fas fa-sync"></i></a>`;
						}
						return action;
					}},
				],
				"language": {
				processing:     "Traitement en cours...",
				search:         "Rechercher&nbsp;:",
				lengthMenu:    "Afficher _MENU_ &eacute;l&eacute;ments",
				info:           "Affichage de l'&eacute;lement _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
				infoEmpty:      "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
				infoFiltered:   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
				infoPostFix:    "",
				loadingRecords: "Chargement en cours...",
				zeroRecords:    "Aucun &eacute;l&eacute;ment &agrave; afficher",
				emptyTable:     "Aucune donnée disponible",
				paginate: {
					first:      "Premier",
					previous:   "Pr&eacute;c&eacute;dent",
					next:       "Suivant",
					last:       "Dernier"
				},
				aria: {
					sortAscending:  ": activer pour trier la colonne par ordre croissant",
					sortDescending: ": activer pour trier la colonne par ordre décroissant"
				}
				},
				paging: true,
				fixedHeader: true
			});
		}
	}
	// Liste des compagnies
	const companies = $("#listCompanies");
	if (companies.html() !== undefined) {
		companies.DataTable({
			ajax: `/ajax/list-companies`,
			dataSrc: 'data',
			order: [],
			columns: [
				{data: 'id'},
				{data: 'name'},
				{data: (data) => {
					return `<a href="/admin/add-company?id=${data.id}" class="text-primary">Modifier</a>`;
				}},
			],
			language: {
				url: '/assets/json/datatable/fr-FR.json',
			},
			paging: true,
			fixedHeader: true
		})
	}
	// Cliquer pour lancer
	$(".click-to-run").on('click', function () {
		const href = $(this).data('href');
		if (!href) {
			return;
		}
		window.location.href = href;
	});
});
//Générer un lien por les articles
function genlink(text,id) {
	$("#loadInput input").attr("disabled", "disabled");
	$(".input-wait").removeClass("d-none");
	$.post("/ajax/build-post-url", {text: text, idpost: id}, function(data) {
		if (data && data.error == false) {
			$("#loadInput input").removeAttr("disabled");
			$(".input-wait").addClass("d-none");
			$("#loadInput input").val(data.link);
		}
	}, "JSON");
}
//Générer un lien por les catégories
function gencatlink(text,id) {
	$("#loadInput input").attr("disabled", "disabled");
	$(".input-wait").removeClass("hidden");
	$.post("/ajax/build-cat-url", {text: text, catid: id}, function(data) {
		if (data && data.error == false) {
			$("#loadInput input").removeAttr("disabled");
			$(".input-wait").addClass("hidden");
			$("#loadInput input").val(data.link);
		}
	}, "JSON");
}
//Afficher un message de dialogue
function info (type,msg) {
	let tp;
	switch (type) {
		case "danger":
			titre = "Erreur"
			tp = "red";
			break;
		case "success":
			titre = "Opération effectuée"
			tp = "green";
			break;
		case "info":
			titre = "Information"
			tp = "blue";
			break;
		default:
			titre = "Information";
			tp = "blue";
			type = "info";
	}
	var d = $.alert({
		title: titre,
		type: tp,
		typeAnimated: true,
		content: msg,
		buttons: {
			ok: {
				text: 'OK',
				btnClass: 'btn-'+type
			}
		}
	});
	return d;
}
//En cours de chargement
function loading () {
	return $.dialog({
		title: "Traitement en cours...",
		typeAnimated: true,
		type: 'blue',
		closeIcon: false,
		content: "<p class='text-center'><img src='/assets/images/ajaxloader.gif'></p>"
	});
}
//Générer un mot alétoire
function generate() {
	var ok = "23456789azertyupqsdfghjkmwxcvbn";
	var mot = "";
	var longueur = 6;
	for (var i = 0; i < longueur; i++) {
		var wpos = Math.round(Math.random()*ok.length);
		mot += ok.substring(wpos,wpos+1);
	}
	return mot;
}
//Générer un mot de passe aléatoire
function pass_gen(cible) {
	var ok = "azertyupqsdfghjkmwxcvbn23456789AZERTYUPQSDFGHJKMWXCVBN&!?][@~+-{}()";
	var pass = "";
	var longueur = 6;
	for (var i = 0; i < longueur; i++) {
		var wpos = Math.round(Math.random()*ok.length);
		pass += ok.substring(wpos,wpos+1);
	}

	$("#"+cible).val(pass);
}