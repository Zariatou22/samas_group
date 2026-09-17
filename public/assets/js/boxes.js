function errorDialog(msg) {
	if (jQuery().confirm != undefined) {
		return $.alert({
			title: 'Erreur',
			type: 'red',
			typeAnimated: true,
			icon: 'fa fa-warning',
			content: msg
		})
	}
}
function wait() {
	return $.dialog({
		title: 'En cours d\'exécution',
		type: 'blue',
		typeAnimated: true,
		closeIcon: false,
		content: 'Veuillez patienter!'
	})
}