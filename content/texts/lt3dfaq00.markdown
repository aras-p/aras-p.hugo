---
layout: page
title: 3D Dažnai Užduodami Klausimai - 2003 07 14
comments: false
sharing: true
footer: true
menusection: texts
url: /texts/lt3dfaq00.html
---

<A href="lt3dfaq.html">Atgal į klausimų puslapį</A>.

<H3>Turinys</H3>
<p>
<ul>
<li><A href="#a">Nuo kokių programinių dalykų priklauso 3D grafinės plokštės paišymo našumas?</a></li>
<li><A href="#b">Kuo iš programuotojo taško žiūrint, skiriasi OpenGL ir D3D?</a></li>
<li><A href="#c">Ką turi skaičiuot pagrindinis procesorius, ir ką skaičiuoja 3D vaizdo plokštės procesorius?</a>
	Kalbu apie situaciją, kai nori gaut apylygį apkrovimą ir nenori, kad vienas iš tų procesorių lauktų kito.</li>
<li><A href="#d">Ar dažnai programos veikimo metu reikia siųst tekstūras iš pagr. atminties į vaizdo plokštę?</a>
	Nuo ko gali priklausyt tekstūrų siuntimo dažnumas?</li>
<li><A href="#e">Kuo skiriasi įvairių D3D versijų galimybės, pvz.: 7-tos, 8-tos ir 9-tos?</a></li>
<li><A href="#f">Ar norint padaryt vidutinišką 3D žaidimą tenka naudot daug gijų (threads)?</a> Kokioms užduotims užtektų vienos vienintelės gijos?</li>
<li><A href="#g">Kas yra "vertex shader"?</a></li>
<li><A href="#h">Ar D3D ir OpenGL žaidimuose visi 3D modeliai susideda iš trikampių?</a></li>
<li><A href="#j">Kas (ūkiškai tariant) yra 3D plokštė? :)</a></li>
</ul>
</p>

<H3>Atsakymai</H3>


<p><A name="a"><strong>Nuo kokių programinių dalykų priklauso 3D grafinės plokštės paišymo našumas?</strong></a></p>
<p>
Pagrinde nuo: grafinės plokštės tvarkyklių optimalumo bei konkrečios programos/žaidimo optimalumo. Taip pat priklauso nuo akivaizdžių šalutinių faktorių:
tuo pačiu metu veikiančių kitų programų, operacinės sistemos, kompiuterio pajėgumo :) ir t.t.
</p>
<p>
Vienok, grafinių plokščių tvarkyklės paprastai yra gana gerai padarytos (ir pastoviai tobulinamos), taigi pagrindinis faktorius yra konkreti programa.
Čia galimybių daug - galima gerai išnaudoti vaizdo plokštę, o galima ir ne :)
</p>
<br/>


<p><A name="b"><strong>Kuo iš programuotojo taško žiūrint, skiriasi OpenGL ir D3D?</strong></a></p>
<p>
Tai atskiros, bet į (daugmaž) tą patį funkcionalumą orientuotos bibliotekos. Didžiausias programuotojo požiūriu skirtumas turbūt yra: OpenGL pateikiama kaip
C funkcijų krūva, D3D pateikiamas kaip COM interfeisų krūva. Mano subjektyvia nuomone, šitas skirtumas gana minorinis...
</p>
<p>
Kiti skirtumai yra daugiau filosofinio pobūdžio ir dažnai sukelia religinius ginčus. Pvz.:
<ul>
<li>OpenGL stengiasi smarkiai išlaikyti atgalinį suderinamumą, ko pasekoje šiuo metu turime dešimties metų senumo biblioteką. D3D gi vos ne su kiekviena
	karta yra radikaliai perdaromas, kad kuo labiau atspindėtų tuo metu egzistuojančius vaizdo spartintuvus. Kas geriau, nuspręskit patys :)</li>
<li>D3D "gamina" Microsoft, OpenGL teoriškai yra atviras standartas (nors praktiškai jį šiuo metu "gamina" 3DLabs :))</li>
<li>...ir t.t.</li>
</ul>
Šiuo metu mano subjektyvusis protas yra už D3D9 - jis paprasčiausiai geresnis :)
</p>
<br/>


<p><A name="c"><strong>Ką turi skaičiuot pagrindinis procesorius, ir ką skaičiuoja 3D vaizdo plokštės procesorius?
Kalbu apie situaciją, kai nori gaut apylygį apkrovimą ir nenori, kad vienas iš tų procesorių lauktų kito.</strong></a></p>
<p>
Šiuo metu (DX7-DX9 karta) vaizdo plokštės moka piešti trikampius (ir nieko daugiau!). Aišku, trikampius piešia gana greitai
(padori DX9 plokštė - 100 mln. trikampių per sekundę), ir galima naudoti krūvas visokiausių trikampių piešimo parametrų (tekstūros,
viršūnių transformacijos programos <em>aka vertex shaders</em>, fragmentų apdorojimo programos <em>aka pixel shaders</em> ir t.t.).
</p>
<p>
Taigi, trumpai galima pasakyti - vaizdo plokštė piešia tekstūruotus trikampius, o pagrindinio procesoriaus tikslas yra padėti trikampių ir tekstūrų
duomenis į labiausiai tam tinkamą vietą, nustatinėti piešimo parametrus ("nuo šiol pieši su šita tekstūra!"), ir pasakyti vaizdo plokštei, kad jau reikia kažka piešti
("piešk šituos 10000 trikampių!").
</p>
<p>
Aišku, pagrindinis procesorius gali skaičiuot ir kažką iš vaizdo plokštės darbo - būtent, trikampių viršūnių transformacijas
(tai galima pavadinti T&amp;L arba vertex shader). Kitus darbus teoriškai procesorius atlikti galėtų, bet greitis būtų labai jau apgailėtinas... Viršūnių transformacijų
atveju CPU gali per sekundę apskaičiuot keletą milijonų transformacijų - tai lėčiau, nei vaizdo plokštė, bet kartais pakanka.
</p>
<p>
Klausimas: kodėl CPU skaičiuoja viršūnių transformacijas? Todėl, kad vaizdo plokštė gali to nemokėti (pvz., GeForce4MX nepalaiko vertex shader'ių), arba
kad paskirstyti apkrovimą tarp CPU ir GPU (pvz., dalį viršūnių transformuoja GPU, dalį CPU - kai kuriose situacijose tai gali apsimokėti). Taip pat būna atvejų,
kad transformuotų viršūnių reikia ir kitiems tikslams (ne tik vaizdavimui), pvz., susidūrimų tikrinimui. Tokiu atveju CPU nori ar nenori turi viršūnes transformuot.
</p>
<p>
Kaip optimaliai paskirstyti apkrovimą tarp CPU ir GPU - čia labai plati tema, ir gana gerai būna paaiškinta plokščių gamintojų dokumentuose (nVidia/ATI turi labai
gerų dokumentų). Pagrindiniai principai būtų: padėti duomenis į labiausiai tinkamas vietas, kuo daugiau darbo atlikti "vienu ypu" ir nedaryti nieko kvailo :)
</p>
<br/>


<p><A name="d"><strong>Ar dažnai programos veikimo metu reikia siųst tekstūras iš pagr. atminties į vaizdo plokštę?
Nuo ko gali priklausyt tekstūrų siuntimo dažnumas?</strong></a></p>
<p>
Geriausia, kai tekstūros yra "padėtos" vaizdo plokštės atmintinėje. Taigi idealus atvejis - ten pradžioje padedamos visos reikiamos tekstūros, ir tada naudojamos
belekiek laiko. Tokiu idealiu atveju atsakymas - "nedažnai" :)
</p>
<p>
Tačiau tekstūrų duomenų gali būti daug, arba vaizdo plokštė gali turėti gana mažai atmintinės. Tuomet iškyla situacijos, kai "visos" tekstūros netelpa į atmintinę.
Tokiu atveju stengiamasi, kad vaizdo plokštėje būtų šiuo metu naudojamos tekstūros (t.y. tos, kuriu prireiks šiam kadrui vaizduoti). Taip galima pasiekti, kad, tarkim,
keletą sekundžių tekstūrų į vaizdo plokštę siųsti nereikia.
</p>
<p>
Gali iškilti situacija, kai net vienam kadrui reikiamos tekstūros netelpa į vaizdo plokštės atmintinę - tuomet jos padedamos AGP atminties srityje ir per AGP
magistralę GPU jas "traukia" pas save. Ši situacija jau gali būti pakankamai bloga, nes AGP pralaidumas (AGP4X - apie 1GB/s, AGP8X - apie 2GB/s) žymiai mažesnis
už vidinės vaizdo plokštės magistralės pralaidumą (Radeon9700Pro - apie 20GB/s).
</p>
<p>
Nuo ko visa tai priklauso - nuo tekstūrų kiekio, tekstūrų dydžio (pvz., 2048x2048 užima 64 kartus daugiau atminties nei 256x256 tekstūra) bei tekstūrų formato (pvz.,
32 bitų RGBA tekstūroje - 4 baitai vienam taškui; DXT3 kompresuotoje tekstūroje - 1 baitas vienam taškui).
</p>
<br/>


<p><A name="e"><strong>Kuo skiriasi įvairių D3D versijų galimybės, pvz.: 7-tos, 8-tos ir 9-tos?</strong></a></p>
<p>
Geriausia - paimti visas jas ir tiesiog pažiūrėti, kuo jos skiriasi :)
</p>
<p>
Esminės D3D kartų ypatybės atspindi to meto 3D spartintuvų ypatybes:
<ul>
<li>DX7 atnešė trikampių viršūnių transformacijų skaičiavimą GPU pagalba (Hardware T&amp;L), kubo išklotinės tekstūras (cube-maps), paprastą apšvietimo
	kiekvienam fragmentui skaičiavimą (gali apskaičiuot skaliarinę sandaugą kiekvienam taškui - "dot3") bei keletą tekstūravimo spec. efektų (EMBM ir kt.).</li>
<li>DX8 atnešė viršūnių ir fragmentų apdorojimo programas (vertex, pixel shaders), tūrines tekstūras ir keletą kitų minorinių dalykų.</li>
<li>DX9 atnešė "geresnes" viršūnių/fragmentų apdorojimo programas, įvedė slankaus kablelio skaičiavimus kiekvienam fragmentui, slankaus kablelio tekstūrų
	formatus, viršūnių keitimą pagal tekstūras (displacement mapping) ir keletą kitų dalykų.</li>
</ul>
</p>
<p>
Greta to, su kiekviena D3D karta pateikiama vis didesnė ir geresnė papildoma biblioteka D3DX, kuri tiesiogiai su grafine plokšte nesusijusi - tai tiesiog pagalbinių
priemonių rinkinys (pvz., matematinės f-jos, paveiksliukų skaitymo f-jos, ir t.t.). Viena geresnių DX8 D3DX ypatybių buvo "effects framework" - karkasas
vaizdavimo parametrams aprašyti tekstinio formato bylose. DX9 jį dar pagerino (pvz., įvedė aukšto lygio viršūnių/taškų programų kalbą HLSL).
</p>
<br/>


<p><A name="f"><strong>Ar norint padaryt vidutinišką 3D žaidimą tenka naudot daug gijų (threads)? Kokioms užduotims užtektų vienos vienintelės gijos?</strong></a></p>
<p>
Praktiškai visoms su grafika susijusioms užduotims užtenka vienos gijos (aišku, plokštės tvarkyklė, arba grafinės bibliotekos kodas gali veikti ir atskirose gijose,
bet nuo programuotojo tai nepriklauso).
</p>
<p>
Gali būti atvejų, kai keletas gijų turi kažkokį pranašumą, bet tai dažnai būna tiesiogiai su grafika nesusiję atvejai (pvz., ilgą fizikinių procesų skaičiavimą galima "paleisti"
atskira gija - bet nebūtina :)).
</p>
<br/>


<p><A name="g"><strong>Kas yra "vertex shader"?</strong></a></p>
<p>
Vaizduojamo primityvo (dažniausiai trikampio) viršūnės apdorojimo programa. Nei daugiau, nei mažiau :)
</p>
<p>
Ši programa vykdoma kiekvienai vaizduojamai viršūnei; į ją ateina kažkoks kiekis duomenų, ji suskaičiuoja kažkiek rezultatų, ir skaičiavimą gali įtakoti kažkiek skaičių "iš šalies"
(vadinamų konstantomis). Tai gana ribotas modelis, pvz., vertex shader pagalba negalima sukurti ar pašalinti viršūnių - galima tik kažkaip transformuoti ateinančius duomenis
į išeinančius rezultatus.
</p>
<p>
Vertex shader'is turi apskaičiuoti galutinę viršūnės poziciją vadinamojoje "clipping" erdvėje, taip pat gali apskaičiuoti keletą (iki dviejų) spalvos reikšmių ir keletą (berods iki 8)
tekstūros koordinačių. Kokiu būdu tai apskaičiuojama - jau pačio vertex shader reikalas. Dažniausiai viršūnės pozicija gaunama, koordinatę "pasaulio" erdvėje
dauginant iš objekto galutinės transformacijos matricos; tekstūrų koordinatės arba apskaičiuojamos kokiu nors paprastu būdu, arba pateikiamos kaip ateinantys
duomenys.
</p>
<p>
Galimų viršūnės transformavimo būdų yra daug, bet iš esmės tai ir išlieka tik viršūnių transformavimas. Vertex shader'is stebuklingai nesukuria įstabių vaizdų,
nevaizduoja tikroviškų emocijų ir t.t. :)
</p>
<br/>


<p><A name="h"><strong>Ar D3D ir OpenGL žaidimuose visi 3D modeliai susideda iš trikampių?</strong></a></p>
<p>
Teoriškai - nebūtinai, praktiškai - taip, iš trikampių. Dabartinės vaizdo plokštės nieko kito vaizduoti nemoka, išskyrus trikampius. Taigi, bet kuris modelis
vaizdavimui turi būti paverčiamas trikampių krūva.
</p>
<p>
Aišku, pats modelis gali būti aprašytas ir ne trikampiais (o, tarkim, Bezjė skiautėmis - gerai tinka kreiviems paviršiams), tačiau tuomet programa/žaidimas turi
jį paversti trikampiais vaizdavimui (naudojant savo priemones, arba grafinės bibliotekos teikamas priemones). Pagrinde dėl to dažniausiai modeliai jau iš karto
būna trikampiai, o alternatyvios formos naudojamos tik išimtinais atvejais.
</p>
<br/>


<p><A name="j"><strong>Kas (ūkiškai tariant) yra 3D plokštė? :)</strong></a></p>
<p>
Šiuo metu - aparatūros gabalas, turintis atmintinę, procesorių (GPU), magistralę, vaizduoklio išvadus ir t.t.
Ką jinai moka - greitai vaizduoti tekstūruotus trikampius; su krūva reguliuojamų parametrų, kurie įtakoja vaizdavimą. Tai praktiškai ir viskas :)
</p>
