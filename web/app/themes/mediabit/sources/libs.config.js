module.exports = {
	js: {
		vendor : [
		"node_modules/jquery/dist/jquery.min.js",
		"node_modules/bootstrap/dist/js/bootstrap.bundle.js",
		"./js/vendor/splide.min.js",
		"./js/vendor/splide-bs-addapt.js"
	
		],
		theme : [
			'./js/custom.js',
		],
		contact: [
			"./js/vendor/choices.js",
			"./js/vendor/cf7-steps-addapt.js",
			"./js/vendor/cf7-choices-addapt.js",
			"./js/vendor/cf7-selectbox-addapt.js"
		]
	},
	css: {
		theme: ["./scss/theme.scss"],
	}
};