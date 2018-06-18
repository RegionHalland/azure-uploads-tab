import Clipboard from 'clipboard'

(function() {
	
	if (!Clipboard || !document.querySelector('.aut__copy-btn')) {
		return
	}
	
	const cb = new Clipboard('.aut__copy-btn')

	cb.on('success', function(e) {
		e.trigger.innerHTML = 'Kopierad till urklipp!'

		setTimeout(() => {
			e.trigger.innerHTML = 'Kopiera URL'
		}, 2000)
	})

})()
