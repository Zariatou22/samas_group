jQuery(document).ready(function($) {
	tinymce.init({
		selector:'#content',
		language: 'fr_FR',
		language_url: '/assets/js/tinymce/langs/fr_FR',
		min_height: 400,
		relative_urls: false,
		image_title: true,
		images_upload_url: '/uploader/upload-from-editor',
		automatic_uploads: true,
		images_reuse_filename: true,
		block_unsupported_drop: true,
		plugins: [ 'code', 'lists', 'autosave', 'autolink', 'media', 'image', 'imagetools', 'table', 'save', 'tabfocus', 'hr', 'link', 'emoticons', 'paste', 'help', 'importcss', 'insertdatetime', 'searchreplace', 'textpattern', 'toc', 'wordcount', 'template', 'charmap', 'fullscreen' ],
		toolbar1: 'undo redo | formatselect | bold italic underline forecolor backcolor | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent',
		toolbar2: 'link charmap fullpage fullscreen emoticons | image mediatheque',
		external_plugins: {
			"mediatheque" : '/assets/js/plugins/tiny/tiny.js'
		},
		file_picker_types: 'file image media',
		images_file_types: 'jpeg,jpg,jpe,png,gif,webp,svg',
		images_upload_base_path: '/uploads',
		file_picker_callback: function(callback, value, meta) {
			var input = document.createElement('input');
	        input.setAttribute('type', 'file');
		    // Provide file and text for the link dialog
		    if (meta.filetype == 'file') {
		    	input.setAttribute('accept', 'application/pdf');
		      	// callback('mypage.html', {text: 'My text'});
		    }

		    // Provide image and alt text for the image dialog
		    if (meta.filetype == 'image') {
		    	input.setAttribute('accept', 'image/*');
		      	// callback('myimage.jpg', {alt: 'My alt text'});
		    }

		    // Provide alternative source and posted for the media dialog
		    if (meta.filetype == 'media') {
		    	input.setAttribute('accept', 'video/mp4');
		      	// callback('movie.mp4', {source2: 'alt.ogg', poster: 'image.jpg'});
		    }

	        input.onchange = function () {
	          var file = this.files[0];

	          var name = file.name.split('.')[0];

	          var reader = new FileReader();
	          reader.onload = function () {
	            var id = name;
	            var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
	            var base64 = reader.result.split(',')[1];
	            var blobInfo = blobCache.create(id, file, base64);
	            blobCache.add(blobInfo);

	            /* call the callback and populate the Title field with the file name */
	            callback(blobInfo.blobUri(), { title: file.name, alt: file.name });
	          };
	          reader.readAsDataURL(file);
	        };

	        input.click();
		}
	});
});