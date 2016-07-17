---
layout: page
title: Įvairūs pamąstymai (2002-2003)
comments: false
sharing: true
footer: true
url: relyzai00.html
---

<p><em><a href="relyzai01.html">Atgal į "pamąstymų" puslapį</a></em></p>

<h4 id="20031229">2003 12 29</h4>
<p>
Dar šiek tiek magistrinio tema (pradžią žr. <a href="#20031205">žemiau</a>). Taigi, šiek tiek "kūrybiško tweakinimo" SH aproksimacijai; šiek tiek optimizacijos
pixel shader'iams, ir gana daug "kūrybiško" požiūrio į šešėlius (<em>"plono stulpo šešėlio nėra? gerai - stulpas plonas, šešėlis nesvarbus! globalus apšvietimas už
dyką!"</em> :)). Rezultatas - atrodo kiek geriau, klaidų mažiau (arba jas vėlgi - kūrybiškai paslepiam).
</p>
<p>
Kad palygint greitį ir vaizdą, šiaip sau padariau ir standartinius <em>shadow-depth-map</em> šešėlius (<em>floating point cubemap</em>'as vienam šviesos šaltiniui).
Per daug su jais nežaidžiau (taigi nepadariau nei <em>closer-percentage</em> filtravimo, nei nieko - dėl to ir juose "laipteliai" visokie matosi).
</p>
<p>
Taigi, lyginam: viršuje standartiniai cubemap'ai, apačioje šitas mano gaidulis (kaip nors gudriai jį galima pavadint...):<br>
<A href="img/lsh/031229-a-smap256.jpg"><img src="img/lsh/tn/031229-a-smap256.jpg"></A>
<A href="img/lsh/031229-b-smap256.jpg"><img src="img/lsh/tn/031229-b-smap256.jpg"></A>
<A href="img/lsh/031229-c-smap256.jpg"><img src="img/lsh/tn/031229-c-smap256.jpg"></A>
<A href="img/lsh/031229-d-smap256.jpg"><img src="img/lsh/tn/031229-d-smap256.jpg"></A>
<A href="img/lsh/031229-e-smap256.jpg"><img src="img/lsh/tn/031229-e-smap256.jpg"></A><br>
<A href="img/lsh/031229-a-localSH.jpg"><img src="img/lsh/tn/031229-a-localSH.jpg"></A>
<A href="img/lsh/031229-b-localSH.jpg"><img src="img/lsh/tn/031229-b-localSH.jpg"></A>
<A href="img/lsh/031229-c-localSH.jpg"><img src="img/lsh/tn/031229-c-localSH.jpg"></A>
<A href="img/lsh/031229-d-localSH.jpg"><img src="img/lsh/tn/031229-d-localSH.jpg"></A>
<A href="img/lsh/031229-e-localSH.jpg"><img src="img/lsh/tn/031229-e-localSH.jpg"></A>
</p>
<p>
Turiu dar keletą idėjų, kaip dar ką nors "kūrybiškai" padaryt... Reikės pabandyt :)
</p>

<h4 id="20031228">2003 12 28</h4>
<p>
Kartais pagalvoju - visai gerai, kad dar užtaikiau į laikmečio, kol nebuvo 3D spartintuvų, galą. Turėt šiokį tokį supratimą, kaip ir kas "tenais" vyksta, yra visai
neblogai. Dar geriau - suprasti, <em>kodėl būtent taip</em> kažkas vyksta. Netgi labai paprastus dalykus geriau yra suprasti - kad nenutiktų, pvz., z-buferis
(<em>"na... panaikina nematomus daiktus!"</em>) arba bi-linear filtravimas (<em>"šitas gi... sulieja tekstūras!"</em>).
</p>
<p>
Čia aišku panašų į nostalgiją <em>oldskool</em> laikams... Bet kai iš tikro žinai, kad, pvz., paprasčiausiai tekstūrą ant daugiakampio rankomis uždėt yra ne taip
jau paprasta (tais laikais ir masteliais netgi "labai daug skaičiavimų" tam reikėjo), tai ir 3D spartintuvams didesnė "pagarba" atsiranda, o ir šiaip geriau.
</p>
---
<p>
Manau, kad tikras (<em>in-depth</em>) supratimas, <em>kaip, kas ir kodėl</em> yra daroma, reikalingas praktiškai visur. Kadangi be programavimo nieko daugiau
neišmanau (o ir programavimo - neitin), tai pavyzdžiai bus iš to. Tarkim, dabar turi .NET, programuoji su C#, keletu kodo eilučių gali užkrovinėt milžiniškus XML
dokumentus arba kvietinėt web servisus - tikrai atrodo, kad esi smarkiai atsiribojęs nuo žemo lygio detalių. Bet - kažkada ateina toks momentas, kai kas nors
pradeda veikti lėtai, naudoti daug atminties ir t.t. Tada reikia kažkaip suprasti ne tik tai, kad diiidelio XML parsinimas su DOM vien tam, kad vieną elementą pažiūrėt,
yra "nelabai naudingas"; ir ne tik tai, kokių parametrų perdavimas RPC kiek atsieina.
</p>
<p>
Netgi .NET programuotojam, tarkim, reikia žinoti, kas yra "locality of reference", kaip veikia CPU kešas, kodėl kartais geriau padaryti 10x daugiau operacijų tam, kad
sutaupyti pusę atminties kreipinių (detaliau:
<a href="http://blogs.gotdotnet.com/jangr/commentview.aspx/2d2d1604-5511-4313-842e-51dbde3fe5fd">čia</a> ir
<a href="http://blogs.gotdotnet.com/ricom/PermaLink.aspx/c5e117b6-8f8c-4e07-b941-c6fa4d3413d8">čia</a>). Aišku, tai low-level detalės, kad ir
koks high-level .NETas bebūtų :(
</p>
--
<p>
Tas pats ir su grafika - praėjus "aš nupaišiau piramidę! o dabar nupaišysiu tekstūruotą piramidę!!!" periodui, kažkada reikia suprasti, kur ir kas "tenais" vyksta.
Negali tiesiog daryt <em>glVertex</em> ir tikėtis, kad "OpenGL pasirūpins!" [kažkodėl dažnas dar pamini, kad "OpenGL state machine" čia yra gerai - prie ko čia
"state machine", aš po šiai dienai nesuprantu]. Čia nevarau ant OpenGL - visur tas pats. <em>Turi</em> žinoti, kas iš tikro vyksta, ir <em>kaip</em> kažką padaryt tokiu
būdu, kuris būtų patogiausias aparatūrai.
</p>
<p><em>...atrodo, čia jau kažkur pradedu kartotis - taigi matyt užteks :)</em></p>

<h4 id="kasyrakas">2003 12 28</h4>
<p>
Ne į temą - mažas žaislas <a href="kasyrakas.php">"<strong>Kas Yra Kas Lietuvoje</strong>"</a>. Štai ką naudingo galima nuveikti, kol vyksta <em>online chat</em>'as su bosu :)
</p>

<h4>2003 12 25</h4>
<p>
<span style="float: right">
<A href="img/img1196.jpg"><img src="img/tn/img1196.jpg"></A>
</span>
Geras knygas reikia pradėt skaityt nuo mažens - <a href="http://www.shaderx2.com">ShaderX^2</a> pakliuvo į Martos rankas :)
</p>
<p>
Ok, tai nėra kokia nors super-fundamentali knyga (t.y. jos turbūt neverta pirkt, jei "noriu 3D išmokt!"). Bet yra tikrai neblogų dalykų. O jei dar ir už dyką
gauni - tai negi neimsi?
</p>
<p>
Dabar reik žiūrėt, ar aš patsai papulsiu į <a href="http://www.shaderx3.com">ShaderX^3</a>, ar ne... Jei labai labai greitai ką nors gero sugalvosiu, tai,
žiū, dar gal ir du kartus papulsiu :)
</p>

<h4 id="20031205">2003 12 05</h4>
<p>
Pastaruoju metu galvojau apie šešėlius, lokalų apšvietimą, sferines harmonikas ir kitokį briedą, žodžiu, apie savo magistro darbą. Sugalvojau tokį dalyką: tarkim,
turim statinę sceną, ir norim dinaminių šviesos šaltinių. Taip pat norim šešėlių, pageidautina "minkštų", ir nenorim naudot įprastų metodų kaip kad <em>shadow
volumes</em> arba <em>shadow maps</em>.
</p>
<p>
Darom taip: daugelyje scenos paviršiaus taškų (viršūnėse, tarkim) apskaičiuojam iš-ten-matomo-atstumo funkciją. T.y. funkcija (pusėje) sferos paviršiaus, kur f-jos
reikšmė - kiek iš tos vietos "matosi" ta kryptimi. Dabar, renderinant kiekvieną tašką, reikia pažiūrėt, koks atstumas iš taško šviesos šaltinio kryptimi -- jei mažesnis nei
atstumas iki pačio šviesos šaltinio, vadinasi, taškas yra šešėlyje.
</p>
<p>
Problema: reikia kažkaip saugoti tas matomo-atstumo funkcijas daugelyje taškų. Kad taškų daug, tai nėra labai didelė bėda (dešimt ar šimtas tūkstančių, koks
skirtumas :)). Bėda - kaip jas saugoti, ir dar taip, kad galėtum labai greitai rasti f-jos reikšmę norima kryptimi. Kolkas aš f-jas aproksimuoju sferinėmis
harmonikomis <em>(SH)</em>, 5-tos eilės -- taigi f-jai saugoti reikia 25 skaičiukų. Tačiau 5-tos eilės SH, sakyčiau, nelabai gerai aproksimuoja tas funkcijas :(
</p>
<p>
Kolkas rezultatas atrodo maždaug taip (~27 tūkstančiai viršūnių, 25 komponentų SH f-ja kiekvienai viršūnei, trys spalvoti dinaminiai šviesos šaltiniai,
naudoja <em>pixel shader 2.0</em>, veikia ~60FPS ant Radeon9800Pro, taigi lėtai):<br>
<a href="img/lsh/shot01.jpg"><img src="img/lsh/tn/shot01.jpg"></a>
<a href="img/lsh/shot02.jpg"><img src="img/lsh/tn/shot02.jpg"></a>
<a href="img/lsh/shot03.jpg"><img src="img/lsh/tn/shot03.jpg"></a>
<a href="img/lsh/shot04.jpg"><img src="img/lsh/tn/shot04.jpg"></a>
<a href="img/lsh/shot05.jpg"><img src="img/lsh/tn/shot05.jpg"></a>
<a href="img/lsh/shot06.jpg"><img src="img/lsh/tn/shot06.jpg"></a><br>
Šešėliai vietomis atrodo gana gerai, bet vietomis ir labai blogai :(
</p>
<p>
Taip pat, SH aproksimavimo klaidos iš arti (šešėlis ant sienos, šešėlis ant stulpo prie lempos, šešėlis ant grindų po lempa):<br>
<a href="img/lsh/shotbug01.jpg"><img src="img/lsh/tn/shotbug01.jpg"></a>
<a href="img/lsh/shotbug02.jpg"><img src="img/lsh/tn/shotbug02.jpg"></a>
<a href="img/lsh/shotbug03.jpg"><img src="img/lsh/tn/shotbug03.jpg"></a>
</p>
<p>
Dabar galvoju, ką čia galima būtų padaryt. Viktoras Jucikas man sako, kad reikia naudot <em>waveletus</em>, mol, gal bus geriau. Bičeliai iš Stenfordo irgi naudojo
<em>waveletus</em> šiek tiek susijusia tema (<A href="http://graphics.stanford.edu/papers/allfreq/">All-Frequency Shadows Using Non-linear Wavelet
Lighting Approximation</a>), ir sako, kad tai geriau už SH... Tai ką, dabar man dar ir <em>waveletus</em> išmokt reikia? :)
</p>
<p>
Pabaigai: šiek tiek fun: jei tiesiog sumuoji SH koeficientus į spalvos kanalus be jokios aiškios priežasties, gaunasi gana gražios pastelės:<br>
<a href="img/lsh/fun02-shsum.jpg"><img src="img/lsh/tn/fun02-shsum.jpg"></a>
<a href="img/lsh/fun01-shsum.jpg"><img src="img/lsh/tn/fun01-shsum.jpg"></a><br>
O va kaip viskas atrodo <em>in wireframe</em> (t.y. reikia daug viršūnių, kur laikyt matomo-atstumo f-jas):<br>
<a href="img/lsh/wireframe.png"><img src="img/lsh/tn/wireframe.jpg"></a><br>
Aj, dar bandžiau pasikonsultuot su žmonėmis iš <em>gd-algorithms</em> konferencijos visu šituo klausimu. Na, išvadų jokių nebuvo :), ir pati diskusija dar į konferencijos
archyvus, rodos, nesuplaukė -- bet jinai turėtų <a href="http://sourceforge.net/mailarchive/forum.php?forum_id=6188">būti už keleto dienų čia</a>
(tema "Occlusion maps for local lights via SH?").
</p>

<h4>2003 11 03</h4>
<p>
Ką gi - rodos, kad laimėjau ATi/Beyond3D "šeiderių" konkursą. Netikėta, bet visai malonu :)
Šiek tiek žemiau mėtęsi paveikslėliai ir yra iš jo, t.y. demkės-šeiderkės <a href="projShaderey.html">Shaderey</a> - galit ją parsisiųsti ir žiūrėt, jei tik ji veiks.
</p>

<h4>2003 10 09</h4>
<p>
Kartais, kai paskaitau kokius nors žaidimų <em>review</em>'us, arba kokias nors diskusijas vieno ar kito žaidimo/"variklio" klausimu, vis aptinku maždaug tokį:
<em>"naudojamas Super Turbo variklis, palaikantis Tuos ir Anuos efektus ar fyčerus"</em>. Ir tai pristatoma kaip variklio "pliusas". Juokinga!
</p>
<p>
Iš principo, bet koks "variklis" tave gali tik riboti. Grafikos pavyzdys: imam <em>plain</em> DX9 - ir turim visas priemones, kokiomis tik galime pasiekti spartintuvą.
Turim visų įmanomų technologijų palaikymą, apskritai <em>viską</em>. Reiškia, galim realizuoti bet kokius efektus ar "fyčerus". Dabar, jei pradedam ant to lipdyti "variklį",
tai automatiškai kažką abstrahuojam. Varikliai tam ir yra, kad tave atitolintų nuo žemo lygio detalių, kažkiek abstrahuotų no to, kas iš tikro vyksta, ir šiaip
palengvintų darbą. Tačiau neišvengiamai abstrakcijos procese kažkas prarandama - kažko padaryt nebeišeina (variklis neleidžia!), arba kažką padaryt tampa net sunkiau
(reikia apeiti variklio apribojimus!). Taigi, galimybių požiūriu, grafinis variklis gali <em>tik riboti</em>.
</p>
<p>Čia panašu į Joel'io <A href="http://www.joelonsoftware.com/articles/LeakyAbstractions.html">Law of Leaky Abstractions</A>...</p>
<p>
Aišku, varikliai paslepia žemo lygio detales, šiaip daug ką palengvina - tai gerai. Bet kai kitą kartą man rodysit variklį, geriau išvardykit jo apribojimus, o ne
"ką jis palaiko" :)
</p>


<h4>2003 08 30</h4>
<p>
<span style="float: right">
<A href="img/stuff030830a.jpg"><img src="img/tn/stuff030830a.jpg"></A>
<A href="img/stuff030830c.jpg"><img src="img/tn/stuff030830c.jpg"></A><br>
<A href="img/stuff030830b.jpg"><img src="img/tn/stuff030830b.jpg"></A>
<A href="img/stuff030830d.jpg"><img src="img/tn/stuff030830d.jpg"></A>
</span>
Paskutiniai - kas gi vis dėlto tai yra, galima pamatyti paveikslėlių viršuje :) Dabar - lauksim, ir žiūrėsim, kas gi bus!
</p>

<h4>2003 08 28</h4>
<p>
<span style="float: right">
<A href="img/stuff030828a.jpg"><img src="img/tn/stuff030828a.jpg"></A>
<A href="img/stuff030828c.jpg"><img src="img/tn/stuff030828c.jpg"></A><br>
<A href="img/stuff030828b.jpg"><img src="img/tn/stuff030828b.jpg"></A>
<A href="img/stuff030828d.jpg"><img src="img/tn/stuff030828d.jpg"></A>
</span>
Dar keletas - atrodo gana baisiai! Ypač <em>wireframe</em>'u...
</p>
<p>
Tai va, atrodo baisiai, o vyksta ten daug (na, nelabai daug) <em>funky</em> dalykų:
<ul>
<li>gerai užsislėpęs <em>"atmospheric light scattering"</em> (dėl jo dangus yra dangaus spalvos, ir kiti daiktai nublanksta, tipo),</li>
<li><em>"image space edge detection"</em> (juodos linijos, pagal gylio skirtumus ir normalių skirtumus),</li>
<li>taip pat kažkas keisto su spalvom - aš konvertuoju iš RGB į HSV, tuo pačiu kvantuoju HSV komponentes, tada kažkaip
(gana bjauriai) išdarkau S ir V komponentes, tada atgal į RGB. Šitą vietą dar reikia taisyt, idant atrodytų ne per daug klaikiai :)</li>
</ul>
Kitkas vis dar tas pats - šešėliai, trikampiai (dabar jau apie milijonas vienam kadrui vietomis būna :)), ir t.t.
</p>

<h4>2003 08 17</h4>
<p>
<span style="float: right">
<A href="img/stuff030817a.jpg"><img src="img/tn/stuff030817a.jpg"></A>
<A href="img/stuff030817b.jpg"><img src="img/tn/stuff030817b.jpg"></A>
</span>
Šiaip paveikslėlis iš to, ką šiuo metu bandau daryti - kad ir kas tai bebūtų :)
</p>
<p>
Nieko įspūdingo - kokis tai terrain'as (apie pusė milijono trikampių), kelios tekstūros ant jo, kažkiek pseudo medžių (jie turi taip atrodyti :)), ir paprasti projektuoti
šešėliai, nuo medžių einantys ant terrain'o. Aišku, šešėliai dinaminiai - šviesos šaltinio kryptį galima keist kaip nori...
</p>
<p>
Sunkiausia čia padaryt, kad šešėliai per daug blogai neatrodytų - aš naudoju vieną 1024x1024 tekstūrą visiem šešėliam - supaišau į ją visus medžius, tada projektuoju
ant žemės. Į tekstūrą paišoma maždaug tiek, kiek mato žiūrovas (ant viso terrain'o uždėta tekstūra būtų ryškiai per mažos rezoliucijos), taigi jam judant plotas, kuriam
"skiriamas" šešėlis, pastoviai keičiasi. Va čia ir išlenda - po truputį keičiantis tam plotui, šešėlių pikseliai baisingai pradeda matytis (nors aš ir naudoju pseudo-anti-alias
šešėliams, t.y. paskaitau iš tekstūros keturis kartus su kiek kitomis koordinatėmis). Na, bet galų gale viskas dabar gerai - reikia tą "šešėliuojamą" plotą kvantuot tokiu
žingsniu, kuris lygus vienam shadow map'o tekseliui. Va :)
</p>
<p>
Dar - iš antro shot'o matyt, kad terrain'as neturi jokio LOD'o (tik cull'inami gabalai, kurie į vaizdą nepatenka). Aš padariau paprastą GeoMipMapping'ą, bet praktiškai
kolkas jo neprireikė - kolkas dažniau visvien stabdo ne trikampių kiekis, o pikselių kiekis...
</p>


<h4>2003 07 08</h4>
<p>
<span style="float: right">
<A href="img/cvfx1.png"><img src="img/tn/cvfx1.jpg"></A>
</span>
Čia iš darbo, projekto "CvFX" (ne, nVidia anei 3dfx čia neprieko :)) - toks <em>entertainment</em> projektas, kur stovi prieš kameras, o iš tavęs rodo "fokusus".
Čia vienas iš paprastų - <em>half-toning</em> (ATI, GDC2003) ir <em>edge detection</em>, abu realizuoti viename pixel shaderyje 2.0 (galima ir senesniame,
bet nebuvo reikalo). Veikia >100FPS ant paprastojo Radeon9500.
</p>
<p>
Čia aš ir klaviatūra, kaip tik alt-printscreen paspaudimo momentu :)
</p>


<h4>2003 06 17</h4>
<p>
<span style="float: right">
<A href="img/cloth/030614-4.jpg"><img src="img/cloth/tn/030614-4.jpg"></A>
<A href="img/cloth/030614-5.jpg"><img src="img/cloth/tn/030614-5.jpg"></A>
</span>
BRDF "spiria į rimtą subinę"! Dešinėje - atlasas <em>(satin)</em> ir aksomas/atlasas <em>(velvet/satin)</em>; su identišku <em>mesh</em>'u ir dviem tokiais pačiais kryptiniais
šviesos šaltiniais. Renderint tokius realiuoju laiku pigiau grybo - du mažučiai <em>cubemap</em>'ai (vieną reikia du kartus dėt, kitą vieną) ir nieko daugiau.
Ir atrodo pusėtinai, ne kaip plastmasinis <em>Phong</em> - mano akim žiūrint, atlasas į tikrą atlasą panašus; o aksomo po ranka neturėjau, kad palyginti :)
</p>
<p>
Čia, jei ką, pagal McCool et al. "Homomorphic Factorizations of BRDFs for High-Performance Rendering", SigGraph 2001
(<A href="http://www.cgl.uwaterloo.ca/Projects/rendering/Papers/homomorphic.pdf">PDF</A>). Ir, taip, čia <a href="cloth.html">mano bakalauro</a>
dalis - ryt ginsiuos.
</p>


<h4>2003 05 21</h4>
<p>
Neseniai:
"<A href="http://www.kompiuterija.lt/cgi-bin/kompiuterija/forumas/topic_show.pl?tid=15138">lietuviškos informacijos apie 3d varikliuko programinima</A>"...
Lietuviškos! Na jau...
</p>
<p>
Kad nėra - faktas. Kodėl nėra - geras klausimas :) Matyt, nėra, nes niekam nereikia, arba niekas nemoka/tingi parašyt. Arba (turbūt tai teisingiausia) ir tas, ir tas.
</p>
---
<p>
Dalykas, kuris "spiria į rimtą subinę" DirectX9 - tai "effect framework" (ID3DXEffect ir t.t.). Naudoji - ir vargo nematai, ir praktiškai turi viską (kas tiesiogiai liečia
renderinimą), ko reikia. Pvz., po <A href="jam2log.html">LTGameJam2003</A> kai kas iš dalyvių manęs klausinėjo
"o kaip ten paišymas vyksta? turbūt labai sudėtingai?". O iš tikro ten nieko nėra, ir pats paišymo kodas turbūt kokius 5-10% viso kodo apimties sudaro, ir ten taip
nuoseklu ir aišku viskas...
</p>
<p>
Turbūt (tik turbūt) ID3DXEffect netinka "labai rimtiems" dalykams (na, normaliems žaidimams), bet pradžiai - super.
</p>


<h4>2003 04 20</h4>
<p>
Kuo toliau, tuo labiau manyje stiprėja įsitikinimas, kad visi <em>"games programmer wannabe"</em>
per daug dėmesio skiria grafikai. Aš pats, beje, irgi :) Čia ypač taip pagalvojau paskaitęs
<A href="http://www.mif.vu.lt/~vysa1570/gamedev/article01.html">Vytauto Šaltenio straipsnelį</A>
- ten viskas teisybė, bet pradedama nuo grafikos.
</p>
<p>
Grafika - oras; jai visai suprast reikia tik truputėlio smegenų ir netingėt straipsnių skaityt (SigGraph,
GDC, t.t.), kad neatsiliktum nuo gyvenimo. 3D plokščių veikimo principai paprasti iki bukumo, 90% visų
"prijomų" ir algoritmų žinomi jau dešimtmečius, nekurios grafinės API irgi paprastesnės nei DuKartDu.
Vienu žodžiu, grafikai "daug karmos turėti" nereikia.
</p>
<p>
Kas kita yra visa likusi dalis - pirmiausia pats žaidimas, po to šalutiniai efektai (Bus fizikos modeliavimas -
koks, kodėl ir kaip tai veiks? Kaip su žaidimu tinkle? Kaip ir kodėl pats žaidimas bus padarytas?). Na, į
šiuos klausimus aš irgi ieškau atsakymo - jei kas nors žinot, praneškit :) Aš galiu manyt, kad žinau, kaip daroma
grafika, bet kaip sužinot, kaip daromi žaidimai (arba: kaip <em>turėtų</em> būti daromi žaidimai)?
</p>
---
<p>
Va, pribūriau, kaip "grafika yra gaidys", tai dabar reikia vėl apie 3D...
<a href="http://research.microsoft.com/~ppsloan/">Peter-Pike Sloan</a> išmislai (minėjau kažkur čia jau)
labai ir labai įdomu, reiktų kada nors pabandyt ką nors ta tema sukept. "Kada nors", aišku, vėl tas
hipotetinis momentas - "kai bus laiko"...
</p>
<p>
Aš įsivaizduoju, kad galima būtų įprastus <em>lightmap</em>'us pabandyt pakeist šitais stebuklais -
tikriausiai reikėtų pagrindinę geometriją suskaidyt į daugiau trikampių (bet trikampiai šiais laikais nemokami)
ir kiekvienoje viršūnėje laikyti sferinių harmonikų koeficientus (arba CPCA skaičiukus). Tada iš esmės gautume
kažką tarpinio tarp <em>lightmap</em>'ų ir per-vertex dinaminio apšvietimo. Hm, galbūt tai nelabai tiktų itin
mažoms patalpoms arba arti esantiems šviesos šaltiniams; bet turėtų tikt kokiai nors katedrai, pro kurios
langus/vitražus šviečia besikeičianti šviesa :) Arba, dar geriau - išorės scenoms.
</p>
<p>
Dar kita idėja - išplėsti tai, ką nVidia darė GeForceFX demoškei "Ogre". Iš jų GDC2003 prezentacijos
"Ogres and Fairies: Secrets of the NVIDIA Demo Team" (berods <A href="http://developer.nvidia.com/docs/IO/4449/SUPP/GDC2003_Demos.pdf">čia</A>)
galima suprast, kai kiekvienai ogro modelio viršūnei jie buvo paskaičiavę "occlusion term" - na, jos apšviestumą.
Dar tikriausiai per anksti apšviestumą (1 skaičius) pakeist sferinėm harmonikom (tarkim, 25 skaičiai), bet ta
diena turbūt artėja.
</p>
---
<p>
<span style="float: right"><A href="img/bzhykt2.jpg"><img src="img/tn/bzhykt2.png"></A></span>
Dar viena mane persekiojanti idėja: turiu įtarimą (įtariu bites), kad "<A href="projBzhykt.html">Bžykt</A>" vaizdą
galima išgauti kitu, galbūt geresniu, būdu. Net nepamenu, kas sugalvojo, kaip padaryt tuos "spalvotus brūkšnius" -
berods, tai buvau aš :) Blogiausias dalykas su jais - kad supaišius viską į mažą tekstūrą, tą tekstūrą reikia atsitempti
atgal iš video plokštės, ir pagal jos pikselių spalvas paišyt tuos brūkšnius. Ta dalis "atsitempti atgal" ir
stabdo labiausiai...
</p>
<p>
Galvoju, kad tikriausiai išeitų supaišius viską į tokią pat mažą tekstūrą, vaizdą išgaut kaip nors gudriai panaudojant
<em>pixel shader</em>'ius. Pvz., paskaitom tą tekstūrą keliose vietose, jei spalvos skiriasi - tai, <em>e... kažkaip</em>
išgaunam "perėjimą tarp brūkšnių". Panaši idėja, tik žymiai paprastesnė, kaip tik neseniai buvo vienoje iš
ATI GDC2003 prezentacijų - berods
<A href="http://www.ati.com/developer/gdc/GDC2003_ScenePostprocessing.pdf">Realtime 3D Scene Post-Processing</A>.
Ech - ir šitą daiktą reikia imt ir pabandyt kada nors... Kas nors žino planetą, kur paroje yra 64 valandos?
</p>


<h4>2003 04 08</h4>
<p>
Visu smarkumu ruošiuosiu būsimajam <A href="http://jammy.sourceforge.net">GameJam#2</A> -
visai sunku; įtariu, kad šiek tiek "per daug" užsibrėžiau (net lygių redaktorių beveik baigiu
padaryt - siaubas!). Nežinia, kas iš viso to gausis, bet kolkas aš savim visai patenkintas, va
tik kad spėčiau padaryt viską...
</p>

<h4>2003 02 27</h4>
<p>
Vienas įspūdingiausių praeitų metų (2002) SigGraph straipsnių buvo
<em>"Precomputed Radiance Transfer for Real-Time Rendering in Dynamic,
Low-Frequency Lighting Environments"</em> - straipsnį, slaidus ir video galima
<a href="http://research.microsoft.com/~ppsloan/">rasti čia</a>.
</p>
<p>
Pirmas žingsnis link gero apšvietimo realiuoju laiku; rašydamas "geras"
turiu omenyje ne "mes turim per-pixel specular'us ir stencil šešėlius"
<em>(primena DoomIII, ane?)</em>, bet kad mes turim šviesos transporto vaizdavimą -
iš ten turim ir "minkštus" šešėlius, ir šviesos atspindžius, t.t. Praktiškai tą
patį, ką gauname ir skaičiuodami <em>radiosity</em>. Tik kad mūsų šviesos gali judėti
ir visaip kitaip duotis. Kaip <em>lightmap</em>'ai, tik dinaminiam apšvietimui. Gerai!
</p>
<p>
Žiūrint į straipsnį, pirma jis kala per smegenis (bent jau man taip buvo), bet kai
geriau įsiskaitai (arba ilgiau pažiūri ne į straipsnį, o į slaidus), tai paaiškėja,
kad visa idėja stebėtinai paprasta. Du kart du beveik :)
</p>


<h4>2003 02 27</h4>
<p><em>O, kokia pauzė buvo :)</em></p>
<p>
Krapštau po truputį <A href="http://jammy.sourceforge.net">GameJam#2</A> "variklį"
ir jaučiu, kad vėl darau klaidą - pradedu daryt viską "nuo apačios"... T.y.: padarau
resursų krovimą, renderinimą, ir t.t. O tada ant to gauto "variklio" bandysiu lipdyt
"žaidimo variklį".
</p>
<p>
Va čia ir įtariu bites - galbūt iš tikro pradėt daryt nuo pačio žaidimo, ir palaipsniui
sudėt į "variklį" tai, ko reikia ir tokiu būdu, kad būtų patogu?
</p>
<p>
Dar bėda - kaip pats žemiausias sluoksnis ("grynasis variklis" - pats sugalvojau :))
daromas maždaug įsivaizduoju... Įdomu, kaip reikėtų daryt aukštesnį - "žaidimo variklį"?
T.y. kaip valdomi žaidimo veikėjai, kaip bendrauja tarpusavyje, t.t. Kas pasakys man?
</p>


<h4>2003 01 11</h4>
<p>
Žiūrinėjau DirectX9 - visai gerai. Jis šiek tiek per anksti išleistas, nes užsilikę
nemažai klaidų (apie jas galima paskaityt
<A href="http://discuss.microsoft.com/archives/DIRECTXDEV.html">DX konferencijoje</A>), dokumentacijoje
yra klaidų ir kai kas nedokumentuota. Bet šiaip gerai - beveik kaip DX8, ir
nemažai gerų dalykų pridėta.
</p>


<h4>2002 12 26</h4>
<p>
"Physically based modeling" kursas
(<a href="http://www2.pixar.com/companyinfo/research/">čia</a>) yra labai gerai.
Super slaidai, ir super anotacijos. Visiem, kas domisi fizika/partiklais, būtina!
</p>

<h4>2002 12 08</h4>
<p>
Darbe kažkaip atėjo mintis, ar tikrai greitai veikia mūsų <em>3d engine</em> su visu
jo OOP ir krūva <em>abstraction layer</em>'ių. Ir ką - realiame žaidimo pavyzdyje
niekas iš pačio variklio į "daugiausia laiko užimu" topus neįeina! Daugiausia užima
pats paišymas - logiška...
</p>
<p>
O dar sako, kad OOP stabdo (ar stabdo bent jau ten, kur greičio reikia)... Pas mus
to OOP net per daug (abstrakcijos ant abstrakcijų... galima turbūt būtų apatinius
sluoksnius iš viso išmest, ir niekas nepastebėtų), o įtakos greičiui - jokios :)
</p>


<h4>2002 11 21</h4>
<p>
Nugirdau mintį, jog <em>"C++ šiek tiek populiari yra tik todėl, jog Microsoft ją
stengiasi prastumti. Jei taip nebūtų, tai Delphi viską valdytų."</em> Įdomi mintis,
nieko nepasakysi :)
</p>
<p>
C++ mane vis labiau stebina. Tikrai kad su ja taip yra: pradžioj manai, kad jos
nemoki, po kokių metų manai, jog moki, o po 2-3 metų suvoki, kad nė velnio tu C++
nemoki...
</p>
<p>
Greitas pavyzdys iš <A href="http://spirit.sourceforge.net">Spirit-Phoenix</A>:
visi žino std::for_each - jis kiekvienam konteinerio elementui vykdo kažkokią
funkciją (t.y. paduotą funktoriaus objektą). Dabar pavyzdys: atspausdinkim
visus nelygnius STL konteinerio skaičius su for_each:</p>

```c
for_each( c.begin(), c.end(),
    if_( arg1 % 2 == 1 ) [
        cout << arg1 << ' '
    ]
);
```

<p>Stebėkit, kas pasidaro vidurinėse eilutėse - iš tos "C++ primenančios" išraiškos
sukonstruojamas <em>funktoriaus objektas</em>, kuris turi <em>operator()</em>, kuris
savo ruožtu spausdina lyginius argumentus! Nereikia rašyt jokios naujos klasės su
"kažkokiu ten" operatoriumi, etc. C++ naudojamas kaip funkcinio programavimo kalba!
</p>
<p>
Aišku, galima klaust "kas iš to? mes galim paprastą <em>for</em> ciklą parašyt!". Taip.
Tikrai galima ciklą parašyt. Aš pats irgi nelabai suprantu funkcinio programavimo
prasmę/naudą (kolkas), bet mane vistiek labai žavi C++ kalba...
</p>



<h4>2002 11 12</h4>
<p>
Apie tinkamą duomenų konteinerį: testavau <em>particle</em> sistemą: 9000 dalelių,
kiekviena tik juda pastoviu greičiu, kas sekundę "numiršta" bei yra sukuriama po
maždaug 1500 dalelių. Į GPU pučiama per AGP (kolkas nenaudojant index buferių),
<em>fillrate</em> neėda, viskas po AthlonXP1500+ su DDR atmintimi.
<table class="table-cells">
<tr>
	<td>Konteineris</td><td>FPS su viskuo</td><td>FPS atjungus visą piešimą</td>
</tr>
<tr>
	<td>std::list&lt;PARTICLE*&gt;</td><td>105</td><td>172</td>
</tr>
<tr>
	<td>resizable pool</td><td>162</td><td>395</td>
</tr>
<tr>
	<td>packed array</td><td>205</td><td>488</td>
</tr>
</table>
</p>
<p>
Na, jau iš anksto aišku, kad std::list dalelėms laikyt netinka - baisus iteravimas
(kešo atžvilgiu), new/delete kiekvienai dalelei, ir t.t.
</p>
<p>
<em>Resizable pool</em> yra paprasto <em>pool</em> idėja, išplėsta iki tiek, kad
pool'as nėra fiksuotos talpos ir į jį sudėti objektai niekada nekeičia savo vietos
atmintyje. Realizavau taip: kaip sąrašą iš paprastų <em>pool</em>'iukų. Na, o paprastas
pool'as - tai fiksuoto dydžio masyvas objektams, ir indeksų masyvas. Pagal indeksų
masyvą įterpimo ir šalinimo greičiai yra O(1), iteravimas daugeliu atvejų irgi
<em>cache-friendly</em>. Resizable pool atveju iteravimas yra šiek tiek sudėtingesnis.
</p>
<p>
<em>Packed array</em> gi yra iš viso paprastas daiktas - tiesiog masyvas. Šalinant ką
nors iš vidurio, paskutinis elementas atkopijuojamas į ką tik pašalinto vietą. Taip
niekada nebūna "skylių", o ir kešas tiesiog džiaugiasi, iteruojant per masyvą.
Realizavau iš viso paprastai, std::vector pagalba (kas papildomai suteikia ir
kintamą talpą).
</p>
<p>
...tai štai, kiek įtakoja konteineris. Dar primeskit, kad čia yra ir kitų neoptimalių
dalių (keletas virtualių metodų kiekvienai dalelei, etc.).
</p>


<h4>2002 11 10</h4>
<p>
Visgi įdomu, kodėl <em>ray-tracing</em> kai kurie žmonės laiko dievu? Kiek mano
galva neša, jis tinkamas atspindžiams/refrakcijai (o ir tiems ne itin). Tu negali
padaryt normalaus apšvietimo su raytraceriu. Negali <em>caustics</em>'ų padaryt.
Negali padaryt švytėjimo. Na, ir taip toliau.
</p>
<p>
Taigi, kad raytraceriai yra riboti - faktas. Kad pakankamai lėti - irgi faktas.
Man įdomu, kodėl raytracerių fanatai nenaudoja kito - riboto, bet greito -
metodo - paprasto trikampių piešimo? Nežino? Nebando?
</p>
<p>
Argumentas, kad "OpenGL/D3D viską stengiasi vaizduoti greitai", o "raytraceriai
viska daro akuratniai" - nieko vertas. GL/D3D vaizduoja <em>tekstūruotus
trikampius</em>, ir vaizduoja juos <em>tiksliai</em> (na, proto ribose :)) - daugiau
jie nieko nedaro. Greitis yra tiesiog šiaip, šalutinis (bet geras) požymis. O
kuo tekstūruoti trikampiai nusileidžia spindulių leidinėjimui? Manau, kad kaip
ir niekuo... netgi galėčiau teigti, kad įmanoma su GL/D3D sugeneruot <em>tokį patį</em>
vaizdą kaip ir su Pov-Ray, tik su šalutiniu efektu, jog veiks gerus keliasdešimt
kartų greičiau :)
</p>
<p>
Va, tik šiandien perskaičiau Jensen "Global Illumination using Photon Maps"
(gėda - taip vėlai!) - tai, IMHO, yra gerai.
</p>
<p>
O šiaip - man rodos, kad 3D hardwarui nebeliko itin daug kliūčių iki tikrai gero
vaizdo - prieš kiek laiko vienintelė bėda buvo mažas tikslumas pikseliuose
(8 bitai - nekas...), betgi dabar ir šios bėdos nebeliko.
</p>
<p>
...tai jei viskas gerai, tai turbūt nėra blogai, ane?
</p>
