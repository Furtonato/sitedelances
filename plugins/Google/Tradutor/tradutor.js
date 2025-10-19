
function googleTranslateElementInit() {
	new google.translate.TranslateElement({
		pageLanguage: 'pt',

		//autoDisplay: false,
		//includedLanguages: 'de,es,fr,en,it',
		layout: google.translate.TranslateElement.InlineLayout.SIMPLE
	}, 'google_translate_element');
}

function ChangeLang(a) {
	var b, elemento = "";
	if(document.createEvent) {
		var c = document.createEvent("HTMLEvents");
		c.initEvent("click", true, true)
	}
	if(a=='Português') {
		$(".goog-te-banner-frame:eq(0)").contents().find("button[id*='restore']").trigger('click');

	} else {
		$(".goog-te-menu-frame:eq(0)").contents().find(".goog-te-menu2-item").each(function() {
			if($(this).find('span.text').html() == 'Inglês'){
				$(this).attr('class', 'goog-te-menu2-item-selected');
			}
		});
	}
}