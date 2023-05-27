<?php
if (isset($_SESSION['language'])) {
	if ($_SESSION['language'] == "en") {

		$new_index = array("Begin Session", "Terms and Conditions", "Social Media", "Follow us on", "Saved Stories", "Followers", "Following", "Press on", "to get more information");
		$nav = array("Home", "Logout");
		$index = array("Information", "What do we do?", "We save the viewers on your stories (Instagram) so that you can consultate them.", "Extra Funcionalities?", "For now, we give you the hability to see who are the most frequent viewers (Top Stalkers).", "Paid Funcionalities?", "All funcionalities are free at the moment.", "Security?", "All user data is encrypted. Type of encryption: DEFAULT (<a target='_blank' href='https://stackoverflow.com/questions/22393143/password-default-vs-password-bcrypt'>BCRYPT vs DEFAULT</a>).");
		$home = array("Saved Stories", "You haven't got any saved stories yet.", "Now, whenever you post an Instagram Story, it will show here together with the viewer list.", "Order by:", "Date", "Views", "Saw", "stories");
		$search = "Search Viewers...";

	} else if ($_SESSION['language'] == "pt") {

		$new_index = array("Iniciar Sessão", "Termos e Condições", "Redes Sociais", "Segue-nos no", "Histórias Guardadas", "Seguidores", "A seguir", "Carrega no", "para obteres mais informações");

		$nav = array("Início", "Sair");
		$index = array("Informações", "O que fazemos?", "Guardamos as pessoas que viram as tuas histórias (Instagram), para que depois possas consulta-las.", "Funcionalidades Extras?", "Por enquanto damos a opção de consultares quem mais vê as tuas histórias (Top Stalkers/Top Seguidores).", "Funcionalidades Pagas?", "De momento todas as funcionalidades são gratuitas.", "Segurança?", "Todos os dados são encriptados. Tipo de encriptação: DEFAULT (<a target='_blank' href='https://stackoverflow.com/questions/22393143/password-default-vs-password-bcrypt'>BCRYPT vs DEFAULT</a>).");
		$home = array("Histórias Guardadas", "Ainda não tens Histórias Guardadas.", "Agora sempre que publicar uma história, vai aparecer aqui juntamente com os visualizadores. Também te iremos notificar quando guardarmos os teus visualizadores através do Direct.", "Ordenar por:", "Data", "Vistas", "Viu", "histórias");
		$search = "Pesquisar Pessoas...";

	} else if ($_SESSION['language'] == "indonesia") {

		$new_index = array("Mulailah Sesi", "Syarat dan Ketentuan", "Media Sosial", "Ikuti kami di", "Cerita yang Tersimpan", "Pengikut", "Mengikuti", "Load on", "untuk informasi lebih lanjut");
		$nav = array("Rumah", "Keluar");
		$index = array("Informasi", "Apa yang kami lakukan?", "Kami menyimpan pemirsa di cerita Anda (Instagram) sehingga Anda dapat berkonsultasi dengan mereka.", "Fungsi Ekstra?", "Untuk saat ini, kami memberi Anda kemampuan untuk melihat siapa pemirsa yang paling sering (Penguntit Top). "," Fungsi Berbayar? "," Semua fungsi bebas saat ini. "," Keamanan? "," Semua data pengguna dienkripsi. Jenis enkripsi: DEFAULT (<a target='_blank' href='https://stackoverflow.com/questions/22393143/password-default-vs-password-bcrypt'>BCRYPT vs DEFAULT</a>). ");

		$home = array("Cerita Tersimpan", "Anda belum punya cerita tersimpan.", "Sekarang, setiap kali Anda memposting Cerita Instagram, itu akan ditampilkan di sini bersama-sama dengan daftar pemirsa.", "Pesan oleh:", "Tanggal" , "Tampilan", "Gergaji", "cerita");
		$search = "Telusuri Pemirsa ...";

	} else if ($_SESSION['language'] == "poland") {
		$new_index = array("Rozpocznij sesję", "Warunki", "Media społecznościowe", "Śledź nas dalej", "Zapisane historie", "Zwolennicy", "Śledzenie", "Załaduj", "więcej informacji");
		$nav = array("Strona główna", "Wyloguj");
		$index = array("Informacje", "Co robimy?", "Zapisujemy widzów na twoje historyjki (Instagram), abyś mógł się z nimi konsultować", "Dodatkowe funkcjonalności?", "Na razie dajemy ci możliwość zobaczenia, kto jest najczęściej oglądany (Top Stalkers)", "Płatne funkcjonalności?", "Wszystkie funkcje są obecnie bezpłatne", "Bezpieczeństwo?", "Wszystkie dane użytkownika są szyfrowane. Rodzaj szyfrowania: DEFAULT (<a target='_blank' href='https://stackoverflow.com/questions/22393143/password-default-vs-password-bcrypt'>BCRYPT vs DEFAULT</a>).");
		$home = array("Zapisane historie", "Nie masz jeszcze żadnych zapisanych historii", "Teraz, kiedy tylko zamieścisz Historię Instagramu, pokaże się ona tutaj wraz z listą widzów", "Porządek według:", "Data", "Widoki", "Piła", "Historie");
		$search = "Wyszukiwarkiwarki....";
	} else if ($_SESSION['language'] == "spain") {

		$new_index = array("Comenzar sesión", "Términos y condiciones", "Redes sociales", "Síganos en", "Historias guardadas", "Seguidores", "Seguir", "Pulsar", "para obtener más información");
		$nav = array("Inicio", "Cerrar sesión");
		$index = array("Información", "¿Qué hacemos?", "Guardamos a los espectadores en tus historias (Instagram) para que puedas consultarlas", "Funciones adicionales", "Por ahora, te damos la habilidad de ver quién son los espectadores más frecuentes (Top Stalkers) "," ¿Funcionalidades pagadas? "," Todas las funcionalidades son gratuitas en este momento. "," ¿Seguridad? "," Todos los datos de los usuarios están encriptados. Tipo de encriptación: DEFAULT (<a target='_blank' href='https://stackoverflow.com/questions/22393143/password-default-vs-password-bcrypt'>BCRYPT vs DEFAULT</a>).");

		$home = array("Historias guardadas", "Aún no tienes ninguna historia guardada", "Ahora, cuando publiques una historia de Instagram, se mostrará aquí junto con la lista de espectadores", "Ordenar por:", "Fecha", "Vistas", "Sierra", "historias");

		$search = "Buscar espectadores ...";

	} else if ($_SESSION['language'] == "india") {

		$new_index = array("आरंभ सत्र", "नियम और शर्तें", "सोशल मीडिया", "हमारा अनुसरण करें", "सहेजे गए कहानियां", "अनुयायी", "अनुसरण", "प्रेस पर", "अधिक जानकारी प्राप्त करने के लिए");
		$nav = array("होम", "लॉगआउट");
		$index = array("सूचना", "हम क्या करते हैं?", "हम आपकी कहानियों (इंस्टाग्राम) पर दर्शकों को बचाते हैं ताकि आप उनसे परामर्श करें।", "अतिरिक्त फ़ंक्शनलिटीज़?", "अभी के लिए, हम आपको देखने के लिए निवास स्थान देते हैं। सबसे लगातार दर्शक (टॉप स्टालर्स) हैं। "," पेड फंक्शनलिटीज? "," सभी फंक्शंस फिलहाल फ्री हैं। "," सिक्योरिटी? "," सभी यूजर डेटा एनक्रिप्टेड हैं। एन्क्रिप्शन का प्रकार।: DEFAULT (<a target='_blank' href='https://stackoverflow.com/questions/22393143/password-default-vs-password-bcrypt'>BCRYPT vs DEFAULT</a>).");
		$home = array("सहेजे गए कहानियां", "आपको अभी तक कोई सहेजी गई कहानियां नहीं मिली हैं।", "अब, जब भी आप एक Instagram कहानी पोस्ट करते हैं, तो यह यहां दर्शक सूची के साथ दिखाई देगी।", "ऑर्डर द्वारा:", "तिथि", "दृश्य", "देखा", "कहानियाँ");
		$search = "दर्शक खोजें ...";

	}
	
}
?>