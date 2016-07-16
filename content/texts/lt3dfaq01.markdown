---
layout: page
title: 3D Dažnai Užduodami Klausimai - 2003 07 16-18
comments: false
sharing: true
footer: true
section: texts
url: /texts/lt3dfaq01.html
---

<A href="lt3dfaq.html">Atgal į klausimų puslapį</A>.

<H3>Turinys</H3>
<p>
<ul>
<li><A href="#a">Kaip atrodytų žaidimai be vertex-shading proceso apskritai?</a></li>
<li><A href="#b">Ar svarbią vietą 3D vaizdo formavime atlieka tie shader'iai?</a></li>
<li><A href="#c">Dėl ko veik 99% mano matytų 3D žaidimų yra per daug kampuoti?</a></li>
<li><A href="#d">Girdėjau, labai efektyvu 3D programose naudoti VertexArray'us, DisplayList'us, CompiledArray'us, ir t.t. Ir ar būtų galima tais būdais perduoti tekstūrų duomenis?</a></li>
</ul>
</p>
<br>

<H3>Atsakymai</H3>


<p><A name="a"><strong>Kaip atrodytų žaidimai be vertex-shading proceso apskritai?</strong></a></p>
<p>
Esmė yra tokia - galų gale vaizdo plokštei reikia pateikti primityvų viršūnių koordinates ir kitus parametrus. Kur jie bus apskaičiuoti - tavo asmeninis reikalas.
Įprasti yra keletas atvejų:
<ol>
<li><em>Hardware T&amp;L (transform and lighting)</em> - Apskaičiuoji galutinius viršūnių duomenis iš pradinių, naudodamas tam keletą (keliolika) iš anksto numatytų skaičiavimo
	būdų. Skaičiavimai atliekami vaizdo plokštės procesoriaus. Gerai tinka vertex shader'ių nepalaikančioms plokštėms (&lt;DX8), ir gana dažnai šito ir tau pačiam užtenka.</li>
<li><em>Software T&amp;L</em> - tas pats, tik skaičiavimai atliekami tavo CPU. Šitai naudoji, jei vaizdo plokštė nepalaiko 1., arba tau pačiam kažkodėl reikia
	galutinių viršūnių duomenų.</li>
<li><em>Hardware vertex shader</em> - Pats pasirašai programėlę, kuri ir apdoroja viršūnės duomenis. Skaičiavimai atliekami GPU. Šitai naudoji, jei 1./2. neužtenka, arba
	dėl asmeninių priežasčių (patogiau, religija neleidžia T&amp;L naudoti, t.t.). Kokiem dalykam gali neužtekti 1./2. - atskiras platus klausimas :) Beje, vertex
	shader'ių irgi yra visokiausio plauko; šiuo metu - DX8 lygio 1.1 versija ir DX9 lygio 2.0, 2.0+ ir 3.0 (šitos dar niekas nepalaiko) versijos.</li>
<li><em>Software vertex shader</em> - tas pats, tik skaičiavimai atliekami tavo CPU. Šitai naudoji, jei vaizdo plokštė nepalaiko 3., arba tau pačiam kažkodėl reikia
	galutinių viršūnių duomenų.</li>
<li><em>Jau transformuotos viršūnės</em> - tais retais atvejais, kai tavo viršūnių duomenų niekaip nebereikia transformuoti, o juos galima tiesiai kišti gilyn į vaizdo
	plokštę. Čia beveik jokie realūs atvejai nepatenka, išskyrus galbūt visokių "plokščių" dalykų piešimą (koordinates nurodai ekrano koordinatėmis, taigi iš esmės 2D).</li>
</ol>
Kaip matosi, be vertex-shading proceso apsieina tik 5. atvejis - bemaž visais realiais atvejais kažką su viršūnėmis daryti reikia
(hm... ne tai kad reikia - taip patogiau ir dažnai greičiau, nei 5. atvejį naudoti).
</p>
<br>


<p><A name="b"><strong>Ar svarbią vietą 3D vaizdo formavime atlieka tie shader'iai?</strong></a></p>
<p>
Na, stebuklų tai jie nedaro tikrai :) Taip pat jie nedaro to, ką nori įteigti jų reklama - t.y. nedaro super-duper spec. efektų, tikroviškų veido animacijų, šešėlių, atspindžių
ir taip toliau.
</p>
<p>
Viskas daug proziškiau:
<ul>
<li>Vertex shader'is - tai programa, kuri vykdoma kiekvieno primityvo kiekvienai viršūnei. Iš ateinančių duomenų (kurios kažkur padeda programa/žaidimas) jinai
	apskaičiuoja išeinančius duomenis. Tai ir viskas, ir tai gana ribotas ir paprastas modelis. Pvz., vykdant vertex shader'į, tu žinai tik "dabartinės" viršūnės duomenis (nežinai nieko
	apie gretimas viršūnes, t.t.). Na, kur čia reklamos "tikroviškos veido animacijos"? :)</li>
<li>Pixel shader'is - tai programa, kuri vykdoma kiekvienam vaizduojamam taškui (tiksliau: fragmentui). Joje gali nuskaityt keletą spalvų iš keletos tekstūrų, ir
	atlikti aritmetines operacijas su tomis spalvomis. Na, ir dar šiek tiek... Vėlgi - čia tiesiogiai niekur nefigūruoja nei šešėliai, nei atspindžiai...</li>
</ul>
Beje, ankstesnieji pixel shader'iai (1.1-1.3, t.y. tie, kurios palaiko GeForce3, GeForce4Ti, Xabre ir Parhelia) iš tikro net nėra tikros "programėlės" - iš tikro tai šiek tiek
išplėsti "register combiners" (kurie jau ir GeForce2 yra). Tai va...
</p>
<p>
Aišku, shader'iai nėra blogai - tai yra priemonė kontroliuoti daliai vaizdo plokštės darbo (parašai programą, kuri vykdoma GPU). Ir kartais būna tokia situacija, kai
prieš-shader'inio funkcionalumo neužtenka kažkam atlikti - tada gali pasirašyti savo shader'į, kuris tai ir daro, ko tau reikia. Taip pat dažnai būna situacija, kai ir dabartinių
shader'ių neužtenka kažkam atlikti :)
</p>
<p>
Beje, "shader" pavadinimas kiek blogai atspindi jo prasmę - daug geriau OpenGL naudojami terminai "vertex program" ir "fragment program". Bet jie, matyt, taip gerai
reklamai netinka :) Žiūrėsim, koks lietuviškas terminas atsiras -- kažkur mačiau "šešėliuoklė", taigi galbūt mes irgi klaidingai vadinsim...
</p>
<br>


<p><A name="c"><strong>Dėl ko veik 99% mano matytų 3D žaidimų yra per daug kampuoti?</strong></a></p>
<p>
Dėl to, kad naudojama mažai trikampių modeliams aprašyti... O kodėl mažai trikampių - tam yra gana daug priežasčių:
<ul>
<li>PC žaidimas turi veikti su visokiausiomis vaizdo plokštėmis - t.y. nuo visokių integruotų iki Radeon9800. Krūva šių plokščių neturi galimybės transformuoti
	viršūnės aparatūriškai (HW T&amp;L) - pvz., šiuo metu naujausia Intel integruota vaizdo plokštė vis dar neturi T&amp;L... o Intel turi 50% vaizdo plokščių
	rinkos. Taigi viršūnes turi transformuoti CPU, kuris ir taip dažnai turi ką veikti žaidime (nustatyti susidūrimus, skaičiuoti žaidimo veikėjų logiką, animacijas, t.t.) --
	taigi galime sau leisti ne itin daug viršūnių...</li>
<li>
	Aukštesnės problemos sprendimas galėtų būti - naudoti keletą versijų kiekvieno modelio (detalizuota, mažiau detalizuota, t.t.). Praktiškai taip yra daroma, tik šiek tiek
	kitaip (mažai detalizuota, dar mažiau detalizuota, t.t. :)). Sukurti labiau detalizuotas versijas užima laiko (o jo visada trūksta), jos užima vietą ir t.t. -- taigi dauguma
	ir nesistengia.
</li>
<li>
	Dažnai žaidimai naudoja gana senus "variklius", kurie buvo sukurti dar žiloje senovėje - o tuomet ir pajėgumai buvo kitokie. Pvz., QuakeIII variklis vis dar naudojamas,
	nors buvo sukurtas prieš pat atsirandant T&amp;L (taip, jis gali jį naudoti, bet optimaliam išnaudojimui reikėtų perrašyti visą variklį).</li>
</li>
<li>
	Turbūt dar kažka užmiršau...
</li>
</ul>
</p>
<br>


<p><A name="d"><strong>Girdėjau, labai efektyvu 3D programose naudoti VertexArray'us, DisplayList'us, CompiledArray'us, ir t.t. Ir ar būtų galima tais būdais perduoti tekstūrų duomenis?</strong></a></p>
<p>
Pirmiausia geriau išsiaiškinti, kaip būtent veikia vaizdo plokštė -- tada ir optimalūs programavimo būdai natūraliai paaiškės :)
Taigi, kaip apdorojami geometrijos duomenys (pirma klausimo dalis - būtent apie juos):
</p>
<p>
Geometrija apdorojama labai paprastai - primityvų (trikampių) viršūnių duomenys yra kažkur atmintinėje; o grafinė plokštė tik gauna komandas, iš katros vietos
kiek primityvų piešti. Natūraliai aiškėja optimalumo pasiekimo priemonės: 1) duomenis padėti į tinkamiausią atmintinės vietą, 2) duomenis keisti kuo rečiau, 3) kiekviena
grafinės plokštės komanda padaryti kuo daugiau darbo. Detaliau:
<ol>
<li>Vaizdo plokštei greičiausiai pasiekiama (šiuo metu - iki 20GB/s) yra lokali jos atmintinė (Video RAM); šiek tiek blogesnis pasirinkimas -
	sisteminės RAM AGP sritis (1-2GB/s); ir pats blogiausias - paprasta sisteminė atmintinė (133MB/s). CPU požiūriu - jam rašyti į bet kokią
	atmintinę yra gana gerai (šiek tiek lėčiau į AGP ir Video RAM). Skaityti iš AGP arba Video RAM visgi yra gana lėtas procesas.<br>
	Iš šito daugmaž aišku toks dalykas: statinius (t.y. tuos, kurie nesikeičia) geometrijos duomenis geriausia padėti į Video atmintinę; dinaminius duomenis - į Video
	atmintinę arba AGP atmintinę.</li>
<li>Tavo pateikiamus geometrijos duomenis vaizdo plokštė gali visaip juokingai sukonvertuoti į sau patogiausią formatą - užtad greičiausia yra naudoti statinę geometriją.
	Statinės geometrijos atveju CPU tik vieną kartą užpildo duomenis, vaizdo plokštė juos sukonvertuoja į sau patogų formatą ir (dažniausiai) pasideda į Video atmintį
	(iš kurios ji gali greitai skaityti).<br>
	Dinaminės geometrijos atveju darbo žymiai daugiau -- ir CPU turi dirbti (t.y. apskaičiuoti ir užpildyti duomenis), ir grafinė plokštė turi juos pastoviai konvertuoti;
	dažnai tokie duomenys būna AGP atmintinėje (reiškia, grafinė plokštė iš ten lėčiau skaito). Taip pat iškyla sinchronizacijos problemos -- reikia užtikrinti,
	kad CPU nekeis duomenų tuo pačiu metu, kai GPU kaip tik juos skaito.</li>
<li>Šitas tiesiogiai su klausimu nesusijęs, bet vistiek :) Šitai reikštų, kad viena komanda reikia stengtis nupiešti kuo daugiau. Be to, geriau kuo mažiau kaitalioti
	vaizdavimo parametrus tarp komandų. Na, čia plati tema...</li>
</ol>
</p>
<p>
Taigi, optimaliam darbui su geometrija užtenka visai nedaug priemonių -- reikia kažko, atspindinčio geometrijos duomenų krūvą (na, viršūnių masyvą -- tai ir vadinama
Vertex Buffer arba Vertex Array). Taip pat reikia priemonių pasakyti, ar kažkuris viršūnių masyvas bus naudojamas dinaminei, ar statinei geometrijai; bei sinchronizacijos
priemonių dinaminės geometrijos atveju.
</p>
<p>
Visos šios priemonės labai tiesiogiai ir paprastai realizuotos pastarosiose Direct3D bibliotekose (DX7-DX9); tačiau gana ilgai OpenGL'e jų pilnai nebuvo. Berods (nesu OpenGL žinovas)
jos tik visai neseniai buvo standartizuotos -- t.y. standartinis <em>extension</em> :) -- ARB_Vertex_Buffer_Object. Nemačiau jo iš labai arti, bet jis berods panašus į D3D priemones,
taigi tinka visam darbui su geometrija (t.y. galima pamiršti DisplayList'us, VertexArray'us ir t.t.). Šioks toks įvadas į jį mėtėsi prie nVidia GDC2003 straipsnių -
<A href="http://developer.nvidia.com/docs/IO/4449/SUPP/GDC2003_OGL_BufferObjects.pdf">GDC2003_OGL_BufferObjects.pdf</A>. Beje,
straipsnius iš nVidia/ATI visada verta paskaityt :) Ir dar beje -- "paprastasis" OpenGL piešimas glVertex() funkcijomis yra laaaabai neoptimalus variantas :)
</p>
<p>
Trumpai apie paminėtus būdus - kai kurie iš jų atsirado beveik dinozaurų laikais, šiuo metu būtų geriau naudoti šiuolaikines priemones (žr. aukščiau). Taigi:
<ul>
<li>DisplayList - tai statinė geometrija ir statinės piešimo ar būsenos keitimo komandos. Gana keistas konglomeratas
	(t.y. į vieną vietą suplakta ir geometrija, ir komandos) :)</li>
<li>VertexArray - viršūnių masyvas, ir tiek. Tačiau neturi sinchronizacijos priemonių, geometrijos tipo (statinė/dinaminė) nurodymo priemonių -- vienu žodžiu, kiek
	nepilnas daiktas (bet trūkstamas vietas galima "prilipdyt" naudojant specifines gamintojų <em>extensions</em>).</li>
<li>CompiledVertexArray - matyt, statinei geometrijai skirtas -- iš arti šito nemačiau.</li>
</ul>
Bet kokiu atveju, naujas "standartinis" <em>extension</em> turėtų "padengti" visus kitus būdus :)
</p>
<p>
Apie tekstūras - ne, anie būdai yra geometrijos saugojimo/valdymo būdai. Tekstūros įprastais atvejais saugomos Video atmintinėje, ir tau tik reikia pasirūpinti atvejais,
kai reikiamos tekstūros nebetelpa ten (tada išimti seniai naudotas, ir sudėti naujas). Daug apie šitai nežinau, nes Direct3D pats turi gana gerą automatinį "tekstūrų valdytoją"
šiam reikalui :)
</p>
<br>

<? include '../common/foot.php'; ?>
