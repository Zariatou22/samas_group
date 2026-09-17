jQuery(document).ready(function($) {
	var currentTab = $(".nav-link.active").attr('href').split('#')[1];
	// Vérifier les paramètres en URL
	var tp = ''
	var query = new URLSearchParams(window.location.search)
	if (query.get('type')) {
		tp = '?type=' + query.get('type')
	}
	var tempFile = window.parent.document.getElementById('tempFile')
	var tempFileId = window.parent.document.getElementById('tempFileId')
	var fileInput = window.parent.document.getElementById('fileInput')
	var filePreview = window.parent.document.getElementById('filePreview')
	loadFiles(tp, query, tempFile, tempFileId)
	function loadFiles(tp, query, tempFile, tempFileId) {
		$.get('/uploader/gets' + tp, function(data) {
			if (!data.error) {
				const files = data.data.data
				tempFile.value = ''
				tempFileId.value = ''
				fileInput.value = ''
				filePreview.value = ''
				loadData(files, data.data.last)
			} else {
				$("#loadingCard").hide()
				const fContent = $('#myFiles #myFileList').html();
				if (fContent !== undefined && fContent.length == 0) {
					$("#myfiles").append(`
						<div class="file-loading-error"><div class="error-content"><i class="fa fa-folder-open-o fa-5x text-muted"></i></div></div>
					`)
				}
				$('#loadMore').removeClass('disabled')
				$('#loadMore').text('Charger davanatage')
			}
		}, "JSON");
	}
	function loadData(files, last){
		let card = ``
		$.each(files, function(key, val) {
			const img = `
				<div class="col-sm-4">
					<div class="card mb-3 media-card" data-link="${val.link}" data-src="${val.full}" data-id="${val.id}" title="${val.title}" data-title="${val.title}" data-alt="${val.alt}" data-desc="${val.description}" data-isimage="${val.is_image}" data-type="${val.type}">
						<img src="${val.preview}" alt="${val.title}" class="card-img">
						<div class="card-body">
							<p class="card-title">${val.title}</p>
						</div>
					</div>
				</div>			
			`
			card += img
		});
		$("#loadingCard").remove()
		$("#myFileList").append(card)
		$('#loadMore').data('last', last)
		$('#loadMore').removeClass('disabled')
		$('#loadMore').text('Charger davanatage')
		$(".card-img").click(function(e) {
			e.preventDefault()
			let link = '', iconize = false;
			const c = $(this), parent = c.parent(), id = parent.data('id'), src = parent.data('src'), title = parent.data('title'), alt = parent.data('alt'), desc = parent.data('desc'), isi = parseInt(parent.data('isimage'), 10), orgLink = parent.data('link');
			$(".container-fluid").find('.card').removeClass('border-primary')
			$(".container-fluid").find('.card').find('.card-options').remove()
			parent.addClass('border-primary')
			if (query.get('src')) {
				parent.find('.card-body').append(`<div class="card-options bg-primary text-white">Sélectionné</div>`)
				tempFile.value = orgLink
				tempFileId.value = id
				fileInput.value = query.get('input')
				filePreview.value = query.get('preview')
			} else {
				let select = '';
				if (isi == 1) {
					select = `
						<select class="form-control file-option">
							<option value="full">Taille réelle</option>
							<option value="1024x768">Grande taille(1024x768)</option>
							<option value="800x600">Taille optimale(800x600)</option>
							<option value="500x200">Taille moyenne(500x200)</option>
							<option value="150x150">Petite moyenne(150x150)</option>
						</select>
					`
				} else {
					select = `
						<select class="form-control file-option">
							<option value="0">Sans icône</option>
							<option value="1">Avec icône</option>
						</select>
					`
				}
				parent.find('.card-body').append(`<div class="card-options bg-primary text-white">${select}</div>`)
				const tp = parent.data('type').split('/')[1]
				window.parent.postMessage({
				  mceAction: 'setOne',
				  data: {
				  	url: src,
				  	link: orgLink,
				  	title: title,
				  	alt: alt,
				  	desc: desc,
				  	iconize: iconize,
				  	icon: tp,
				  	isImage: (isi == 1)?true:false
				  }
				}, '*');
				// Lorsque le sélect change
				$('.file-option').change(function() {
					const c = $(this), taille = c.val();
					if (taille == 'full' || taille == '0' || taille == '1') {
						link = src;
						if (taille == '1') {
							iconize = true
						}
					} else {
						link = '/uploads/' + taille + '/' + parent.data('link')
					}
					window.parent.postMessage({
					  mceAction: 'setOne',
					  data: {
					  	url: link,
					  	link: orgLink,
					  	title: title,
					  	alt: alt,
					  	desc: desc,
					  	icon: tp,
					  	iconize: iconize,
					  	isImage: (isi == 1)?true:false
					  }
					}, '*');
				})
			}
		});
	}
	// Charger plus
	$("#loadMore").click(function(e) {
		e.preventDefault()
		const c = $(this), last = c.data('last')
		c.html(`<i class="fa fa-spin fa-spinner"></i>`)
		c.addClass('disabled')
		tp += '&start=' + last
		loadFiles(tp, query, tempFile, tempFileId)
		//$("#loadMore").scrollTop($("#myFileList")[0].scrollHeight)
	})
	// Lorsqu'on clique sur un onglet
	/*$(".nav-link").click(function(e) {
		const c = $(this)
		const href = c.attr('href')
		const link = href.split('#')[1]
		if (link !== currentTab && link == "mediaList") {
			$("#loadingCard").show()
			$("#myFileList").html('');
			loadFiles(tp, query, tempFile, tempFileId);
		}
		if (currentTab != link) {
			currentTab = link;
		}
	})*/
});