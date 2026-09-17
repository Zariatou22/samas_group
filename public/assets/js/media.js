var url = window.location.origin, ajaxUrl = url+"/ajax/";
$(function() {
	// Mise à jour d'un fichier
	$(".edit-file").click(function(e) {
		const c = $(this)
		editFile(c)
	});
	// Mise à jour en cliquant sur le bouton global
	$(".edit-selected").click(function() {
		const t = $(".mg-files").find('.thumbnail-selected').get(0)
		if (t !== undefined) {
			const fid = t.getAttribute('data-tid')
			if (fid !== undefined) {
				const c = $('#file' + fid).find('.edit-file')
				editFile(c)
			}
		}
	})
	// Supprimer un fichier d'un dossier
	$('.remove-from-folder').click(function() {
		fid = checkSelectedItem()
		if (fid) {
			const c = $(this), folder = c.data('fold');
			removeFromFolder(fid, folder)
		}
	})
	// Supprimer le fichier du dossier à partir de lui-même
	$('.rem-from-folder').click(function() {
		const c = $(this), fid = c.data('fid'), folder = $(".change-folder").data('fold');
		removeFromFolder(fid, folder)
	})
	// Changer de dossier à un fichier depuis la barre des menus
	$(".change-folder").click(function() {
		fid = checkSelectedItem()
		if (fid) {
			const c = $(this), folder = c.data('fold');
			listDirectories(fid, folder)
		}
	})
	// Changer de dossier à un fichier sur lui-même
	$('.change-dir').click(function() {
		const c = $(this), fid = c.data('fid'), folder = $(".change-folder").data('fold');
		listDirectories(fid, folder)
	})
	// Supprimer définitivement un fichier
	$('.delete-file').click(function() {
		fid = checkSelectedItem()
		if (fid) {
			const c = $(this), folder = c.data('fold');
			deleteFile(fid, folder)
		}
	})
	// Supprimer définitivement un fichier depuis lui-même
	$(".del-file").click(function() {
		const c = $(this), file = c.data('fid'), folder = $(".change-folder").data('fold');
		deleteFile(file, folder)
	})
});

function deleteFile(file, folder) {
	$.confirm({
	    title: 'À votre attention',
	    type: 'red',
	    typeAnimated: true,
	    icon: 'fas fa-exclamation-circle',
	    content: 'Vous êtes sur le point de supprimer définitivement un fichier du serveur. Cette action est irréversible. Continuer tout de même?',
	    buttons: {
	        ok: {
	        	text: 'Oui',
	        	btnClass: 'btn-danger',
	        	action: function () {
	        		const load = wait()
	        		$.get('/uploader/delete-media/' + file, function(data) {
	        			load.close()
	        			if (!data.error) {
	        				window.location.reload()
	        			} else {
	        				errorDialog(data.ermsg)
	        			}
	        		}, "JSON");
	        	}
	        },
	        cancel: {
	        	text: 'Annuler'
	        }
	    }
	});
}
function removeFromFolder(file, folder) {
	$.confirm({
	    title: 'À votre attention',
	    type: 'red',
	    typeAnimated: true,
	    icon: 'fas fa-exclamation-circle',
	    content: 'Êtes-vous sûr de bien vouloir retirer ce fichier du répertoire courant?',
	    buttons: {
	        ok: {
	        	text: 'Oui',
	        	btnClass: 'btn-danger',
	        	action: function () {
	        		$.post('/ajax/remove-file-from-folder', {file: file, folder: folder}, function(data) {
	        			if (!data.error) {
	        				window.location.reload()
	        			} else {
	        				errorDialog(data.ermsg)
	        			}
	        		}, "JSON");
	        	}
	        },
	        cancel: {
	        	text: 'Annuler'
	        }
	    }
	});
}
function listDirectories(fid, folder) {
	const load = wait()
	$.get('/ajax/get-directories?excludes='+folder, function(data) {
		load.close()
		if (data.length > 0) {
			changeDir(fid, data)
		}
	}, "JSON")
}
function editFile(c) {
	const boxTitle = c.data('box-title')
	const okText = c.data('box-oktext')
	const cancelText = c.data('box-canceltext')
	const src = c.data('src')
	const id = c.data('id')
	const type = c.data('type')
	const isImage = c.data('isimage')
	const title = c.data('title')
	const alt = c.data('alt')
	const description = c.data('description')
	const parent = $("#file" + id)
	$.confirm({
	    title: boxTitle,
	    type: 'blue',
	    typeAnimated: true,
	    icon: 'fa fa-info-circle',
	    content: `
	    <form action="" class="formName">
		    <div class="form-group">
			    <label>Texte alternatif</label>
			    <input type="text" class="alt form-control" value="${alt}" required />
		    </div>
		    <div class="form-group">
			    <label>Titre</label>
			    <input type="text" class="titre form-control" value="${title}" required />
		    </div>
		    <div class="form-group">
			    <label>Description</label>
			    <input type="text" class="description form-control" value="${description}" required />
		    </div>
	    </form>`,
	    buttons: {
	        formSubmit: {
	            text: okText,
	            btnClass: 'btn-blue',
	            action: function () {
	                const alt = this.$content.find('.alt').val();
	                const titre = this.$content.find('.titre').val();
	                const description = this.$content.find('.description').val();
	                let err = '';
	                if(!alt){
	                    err = 'Champ texte alternatif non fourni'
	                } else if (!titre){
	                    err = 'Veuillez renseigner le titre de l\'image'
	                } else if (!description){
	                    err = 'La description est vide'
	                }
	                if (err.length > 0) {
	                	errorDialog(err)
	                	return false;
	                } else {
	                	const d = wait()
	                	$.post('/uploader/update-file', {alt: alt, title: titre, description: description, id: id}, function(response, textStatus, xhr) {
	                		d.close()
	                		if (response.error) {
	                			errorDialog(response.ermsg)
	                		} else {
	                			c.data('alt', alt)
	                			c.data('title', titre)
	                			c.data('description', description)
	                			const df = parent.find(".file-title").html(titre)
	                		}
	                	}, "JSON");
	                }
	            }
	        },
	        cancel: {
	            text: cancelText,
	            btnClass: 'btn-danger'
	        },
	    },
	    onContentReady: function () {
	        const jc = this;
	        this.$content.find('form').on('submit', function (e) {
	            e.preventDefault();
	            jc.$$formSubmit.trigger('click');
	        });
	    }
	});
}
function changeDir(id, dirs) {
	let options = ''
	for (d of dirs) {
		options += `<option value="${d.id}">${d.nom}</option>`
	}
	$.confirm({
	    title: 'Permuter un répertoire',
	    type: 'blue',
	    typeAnimated: true,
	    icon: 'fas fa-edit',
	    content: `
	    	<form action="" class="formName">
	    		<div class="form-group">
	    			<label>Choisissez un dossier</label>
	    			<select class="form-control dir">${options}</select>
	    		</div>
	    	</form>
	    `,
	    buttons: {
	        formSubmit: {
	            text: 'Valider',
	            btnClass: 'btn-blue',
	            action: function () {
	                const dir = this.$content.find('.dir').val();
	                if(!dir){
	                    $.alert('Le dossier de destination ne peut être vide!');
	                    return false;
	                }
	                const load = wait()
	                $.post('/ajax/change-directory', {file: id, folder: dir}, function(data) {
	                	load.close()
	                	if (!data.error) {
	                		window.location.reload();
	                	} else {
	                		errorDialog(data.ermsg)
	                	}
	                }, "JSON");
	            }
	        },
	        cancel: {
	            text: 'Annuler'
	        },
	    },
	    onContentReady: function () {
	        var jc = this;
	        this.$content.find('form').on('submit', function (e) {
	            e.preventDefault();
	            jc.$$formSubmit.trigger('click');
	        });
	    }
	});
}
function checkSelectedItem() {
	let d = false
	const t = $(".mg-files").find('.thumbnail-selected').get(0)
	if (t !== undefined) {
		const fid = t.getAttribute('data-tid')
		if (fid !== undefined) {
			d = fid
		}
	}
	return d
}