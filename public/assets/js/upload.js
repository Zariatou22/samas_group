var query = new URLSearchParams(window.location.search)
$(function() {
	const uploader = new plupload.Uploader({
		runtimes : 'html5,flash,silverlight,html4',
		browse_button : 'pickfiles',
		container: document.getElementById('uploader'),
		drop_element: document.getElementById('dropArea'),
		url : '/uploader',
		views: {
	        list: true,
	        thumbs: true, // Show thumbs
	        active: 'thumbs'
	    },
		flash_swf_url : '/assets/plupload/Moxie.swf',
		silverlight_xap_url : '/assets/plupload/Moxie.xap',
		
		filters : {
			max_file_size : '10mb',
			mime_types: [
				{title : "Image files", extensions : "jpg,jpeg,gif,png"},
				{title : "Document", extensions : "pdf"},
				{title : "Zip files", extensions : "zip"}
			]
		},

		init: {
			PostInit: function() {
				document.getElementById('filelist').innerHTML = '';
			},

			FilesAdded: function(up, files) {
				plupload.each(files, function(file) {
					document.getElementById('filelist').innerHTML += '<div id="' + file.id + '" class="file">' + file.name + ' (' + plupload.formatSize(file.size) + ') <div class="progressbar"><div class="progress"></div></div></div>';
				});
				$("#dropArea").removeClass('dragon')
				uploader.start();
				uploader.refresh();
			},

			UploadProgress: function(up, file) {
				document.getElementById(file.id).querySelector('.progress').style.width = file.percent + '%'
			},

			FileUploaded: function(up, file, data) {
				const res = $.parseJSON(data.response)
				let optionEdit = ''
				if (!res.error) {
					const dts = res.data;
					const size = dts.file_size;
					const tp = dts.file_type.split('/')[1];
					const img = `<div class="file-details float-left">
									<img src="${dts.path}" class="upload-preview">
									<span class="upload-filename">${dts.raw_name}</span>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Type:${tp}&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Taille:${size}Ko
								</div>`
					const options = `<div class="float-right"><a href="javascript:void(0)" class="btn btn-secondary btn-sm insert-file" data-title="${dts.raw_name}" data-alt="${dts.raw_name}" data-description="${dts.raw_name}" data-id="${dts.id}" data-fileid="${file.id}" data-isimage="${dts.is_image}" data-type="${dts.file_type}" data-src="${dts.file_name}"><i class="fa fa-link"></i></a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" class="del btn btn-danger btn-sm" data-msg="Voulez-vous vraiment supprimer ce fichier? Notez que cette action est irreversible" data-id="${dts.id}" data-fileid="${file.id}"><i class="fa fa-times-circle"></i></a></div>`
					$("#" + file.id).html(img)
					$("#" + file.id).prepend(options)
				} else {
					errorDialog(data.ermsg)
				}
			},

			Error: function(up, err) {
				document.getElementById('console').appendChild(document.createTextNode("\nError #" + err.code + ": " + err.message));
			}
		}
	})
	// En survolant la zone de DragNdrop
	$("#dropArea").on("dragover", function () {
		const c = $(this);
		c.addClass('dragon')
	})
	// Lorsqu'on quitte la zone de DragNdrop
	$("#dropArea").on("dragleave", function () {
		const c = $(this);
		c.removeClass('dragon')
	})
	// Insérer le fichier
	$('#filelist').on('click', '.insert-file', function(e) {
		e.preventDefault()
		const c = $(this), id = c.data('id'), src = c.data('src'), tp = c.data('type'), is_image = c.data('isimage'), title = c.data('title'), alt = c.data('alt'), description = c.data('description');
		// Enlever la sélection partout
		$('#filelist .insert-file').removeClass('btn-primary').addClass('btn-secondary')
		$('#filelist .insert-file').find('.fa').removeClass('fa-check').addClass('fa-link')
		// Ajouter la sélection à l'actuel
		c.removeClass('btn-secondary').addClass('btn-primary')
		c.find('.fa').removeClass('fa-link').addClass('fa-check')
		// Les champs d'accueil
		const tempFile = window.parent.document.getElementById(query.get('src'))
		const tempFileId = window.parent.document.getElementById(query.get('id'))
		const fileInput = window.parent.document.getElementById('fileInput')
		const filePreview = window.parent.document.getElementById('filePreview')
		if (query.get('src') && query.get('id')) {
			// Au cas où c'est pour usage hors éditeur
			if (query.get('type') == 1) {
				if (is_image == 1) {
					tempFile.value = src
					tempFileId.value = id
					fileInput.value = query.get('input')
					filePreview.value = query.get('preview')
				} else {
					// Enlever la sélection partout
					$('#filelist .insert-file').removeClass('btn-primary').addClass('btn-secondary')
					$('#filelist .insert-file').find('.fa').removeClass('fa-check').addClass('fa-link')
					errorDialog('Ceci n\'est pas une image')
				}
			} else {
				tempFile.value = src
				tempFileId.value = id
			}
		} else {
			// Usage dans l'éditeur
			window.parent.postMessage({
			  mceAction: 'setOne',
			  data: {
			  	url: (is_image)?'/uploads/800x600/' + src : '/uploads/' + src,
			  	link: src,
			  	title: title,
			  	alt: alt,
			  	desc: description,
			  	iconize: false,
			  	icon: tp,
			  	isImage: (is_image == 1)?true:false
			  }
			}, '*');
		}
	})
	// Edition du ficier uploadé
	$("#filelist").on("click", ".edit-file", function(e) {
		e.preventDefault()
		const c = $(this);
		const id = c.data("id")
		const alt = c.data("alt")
		const title = c.data("title")
		const description = c.data("description")
		$.confirm({
		    title: 'Mise à jour des informations',
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
		            text: 'Enregistrer',
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
		                			const idf = c.data('fileid');
		                			const df = $("#"+ idf).find("span.upload-filename").html(titre)
		                		}
		                	}, "JSON");
		                }
		            }
		        },
		        cancel: {
		            text: 'Annuler',
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
	});
	// Suppression du fichier
	$("#filelist").on("click", ".del", function(e) {
		e.preventDefault()
		const c = $(this)
		const msg = c.data('msg')
		const id = c.data('id')
		const fileid = c.data('fileid')
		$.confirm({
			title: 'A votre attention',
			icon: 'fa fa-warning',
			type: 'red',
			typeAnimated: true,
			content: msg,
			buttons: {
				okBtn: {
					text: 'Supprimer',
					btnClass: 'btn-danger',
					action: function() {
						const d = wait()
						$.get('/uploader/delete-media/' + id, function(resp, textStatus, xhr) {
							d.close()
							if (resp.error) {
								errorDialog(resp.ermsg)
							} else {
								$("#" + fileid).remove()
							}
						}, "JSON");
					}
				},
				cancelBtn: {
					text: 'Annuler',
					btnClass: 'btn-blue'
				}
			}
		});
	});
	uploader.init()
});

function errorDialog(msg) {
	return $.alert({
		title: 'Erreur',
		type: 'red',
		typeAnimated: true,
		icon: 'fa fa-warning',
		content: msg
	})
}
function wait() {
	return $.dialog({
		title: 'En cours d\'exécution',
		content: 'Veuillez patienter!'
	})
}