console.log('hello everybody')

var clipboard = new ClipboardJS('.azure-uploads__link');

clipboard.on('success', function(e) {
	console.log(e);
});