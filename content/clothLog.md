---
title: Cloth simulation - log
comments: false
sharing: true
footer: true
menusection: proj
url: clothLog.html
---

<p>
<A href="cloth.html">Atgal į medžiagos imitavimo puslapį</A>.
</p>

<p><strong>2003 01 10</strong></P>
<p>
<em>God damn implicit integrators!</em> Nieko nesigauna (na, gaunasi, bet stabilumas
toks pats kaip ir <em>explicit</em>, veikia daug kartų lėčiau, ir kodo žymiai daugiau
- neprikolkas). Straipsniais nepatikėjęs, net visas išvestines pats išsivedžiau
(o vektorių išvestinės vektorių atžvilgiu visai kala), <em>n</em> kartų tikrinau
sprendimą, ir t.t. - ir nieko! Nieko gero!
</p>
<p>
Taigi padariau krūvą paprastųjų išreikštinių integratorių - Oilerio, vidurio taško
ir 4 laipsnio Rungės-Kutos. Dabar matyt darysiu susidūrimų tikrinimą, o neišreikštiniai
integratoriai gal patys apsigalvos ir susitvarkys savaime :)
</p>


<p><strong>2003 01 04</strong></P>
<p>
<div style="float: right">
<A href="img/cloth/030104-1.jpg"><img src="img/cloth/tn/030104-1.jpg"></A>
<A href="img/cloth/030104-2.png"><img src="img/cloth/tn/030104-2.jpg"></A>
</div>
Ehe, išvestinės tai dar nieko... Kai padarai visas išvestines, viską padarai kaip
reikia, viską suskaičiuoji ir netgi teisingai, o veikia viskas neteisingai, tai va
tada jau blogai. Vis niekaip nepriverčiu neišreikštinio integratoriaus veikti
gerai...
</p>
<p>
Na, kol nepriverčiu, tai padariau keletą paveikslėlių su didesne medžiagos
skiaute, iš 6400 dalelių.
</p>


<p><strong>2002 12 30</strong></P>
<p>
O, išvestinės, išvestinės! O, atvirkštinių funkcijų galvojimas!
</p>
<p>
Su atvirkštinėm
yra taip: yra <em>"sine cardinal"</em> funkcija <em>sinc(x) = sin(x)/x</em>. Man reikia
jai atvirkštinės funkcijos (straipsnyje, aišku, parašo "atvirkštinė", o detaliau - ne).
Kiek suprantu, analitiškai atvirkštinė nesusiskaičiuoja, arba/turbūt aš viską užmiršau,
o Mathcadas man irgi nepadeda.
</p>
<p>
Pakolkas iš lempos sugalvojau aproksimaciją <em>0.502*sqrt(1-x)+0.98*acos(2*x-1)</em> - kaip
visiškai iš galvos sugalvotai, visai neblogai :)
</p>

<p><strong>2002 12 28</strong></P>
<p>
<div style="float: right">
<A href="img/cloth/021228-1.jpg"><img src="img/cloth/tn/021228-1.jpg"></A>
<A href="img/cloth/021228-2.jpg"><img src="img/cloth/tn/021228-2.jpg"></A>
</div>
Na štai, "(un)Stable but Responsive Cloth" jau yra ir veikia. Unstable todėl,
kad kolkas varom tiesiog išreikštiniu Oileriu, kas šiaip jau yra nevalia. Dar
nėra jokių susidūrimų tikrinimų, bet vistiek gerai.
</p>


<p><strong>2002 12 26</strong></P>
<p>
"Physically based modeling" kursas
(<a href="http://www2.pixar.com/companyinfo/research/">čia</a>) yra labai gerai.
Super slaidai, ir super anotacijos. Visiem, kas domisi fizika/partiklais, būtina!
</p>


<p><strong>2002 11 21</strong></P>
<p>
Pradėjau žiūrėt į MTL ir ITL bibliotekas (Matrix Template Library ir Iterative
Template Library). Pakol žiūri - labai gražios ir galingos bibliotekos (vėlgi -
"power of C++" - templeitai valdo!).
</p>


<p><strong>2002 11 20</strong></P>
<p>
Pradėjau kodinti pačią medžiagą (pseudo-frameworką jau padariau anskčiau). Mieliausiu
dūšiai būdu pasirodė besąs K.Choi, H.Ko "Stable but Responsive Cloth" (SigGraph'2002),
taigi maždaug jo būdu ir darau.
</p>
<p>
Jėgų medžiagoje skaičiavimas "pasidarė" stebėtinai lengvai. Pliusas straipsnio
autoriams, kad neperkrovė matematine notacija, kurios, aišku, padoriai nebeatsimenu
(na, straipsnyje mėtosi Jakobianai ant kiekvieno kampo, bet jie ten tik įrodymo
tikslais :)).
</p>
<p>
Aišku, kad nieko vizualaus dar nematyti (nes dar niekas nepadarė, kad kas nors ką
nors rodytų)...
</p>

<p><strong>2002 11 17</strong></P>
<p>
Teoriškai aš turėčiau daryti "medžiagos imitavimas baigtinių elementų metodu" -
t.y. <em>"finite elements method"</em> (FEM).
Praktiškai gi neimanoma rasti straipsniu apie FEM medžiagai imituoti - visi naudoja
<em>"interacting particles"</em> (dar vadinama <em>"mass spring system"</em>).
</p>
<p>
Nesu tikras, kad FEM ir MassSpring kuo nors skiriasi (arba jie gali but vienas
kito subset'ai)... nieko apie FEM nežinau :)
</p>
<p>
Mano durnai galvai atrodo, kad itin didelio skirtumo tarp jų neturėtų būti - visvien
yra "kažkokios" medžiagą aprašančios lygtys, ir jas pritaikai arba FEM'ui, arba
mass-spring sistemai. O štai kad iš didžiausių konferencijų per pastaruosius 7
metus nepasitaikė nei vieno straipsnio "grynai" apie FEM (palyginimui: yra
dešimtys straipsnių apie mass-spring), jau yra šioks toks, nors ir subjektyvus,
rodiklis :)
</p>

<p><strong>2002 11 11</strong></P>
<p>
Čia bus viskas apie imitavimą "interacting particles" metodu. Taigi, medžiagos
imitavime yra 3 iš esmės atskiros dalys: 1) jėgų medžiagoje skaičiavimas,
2) pagal jėgas medžiagos "pernešimas" į sekančią būseną, 3) susidūrimų aptikimas
ir reagavimas į juos.
</p>
<p>
Kas įdomiausia - daugelis straipsnių 3 dalį daugmaž ignoruoja <em>("aj, padarom bilekaip,
vistiek niekam nerūpi")</em>. Kolkas radau vieną naują straipsnį, iš esmės nagrinėjantį
šią problemą (o ji ne tokia maža, kaip atrodo) - Brison, Fedkiw, Anderson "Robust
Treatment of Collisions, Contact and Friction for Cloth Animation", SigGraph 2002
(staipsnis lengvai susi-<em>google</em>'ina).
</p>
<p>
Na, o kitos 2 dalys yra straight-forward: pirmoje turi ryšius tarp particl'ų
ir iš jų pagal susigalvotas lygtis apskaičiuoji jėgas; antroje imi ir integruoji.
Dabar "ant bangos" yra netiesioginiai <em>(implicit)</em> integratoriai (backward
euler, implicit midpoint, BDF). Na, čia irgi šiek tiek matematikos yra, tačiau
bent jau sprendimo metodai pakankamai plačiai žinomi ir aiškūs...
</p>

<p><strong>2002 11 10</strong></P>
<p>
Įdomu, ar pagaliau būsiu priverstas <em>iš tikro</em> sužinot, kas yra BRDF? T.y.:
ar naudosiu BRDF medžiagos renderinimui?
</p>
<p>
...nes kolkas tai kaip ir su daugeliu kitų dalykų: "konceptualiai" žinau, o
iš tikro - nevisai...
</p>
