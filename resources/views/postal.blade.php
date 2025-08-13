<x-guest-layout>
    <x-jet-banner />
    @push('styles')
    	<style type="text/css">
    		.prose ol.list-alpha > li::before {
			    content: counter(list-counter, upper-alpha) ".";
			}
			.prose ol.list-roman > li::before {
			    content: counter(list-counter, upper-roman) ".";
			}
			.prose ol.list-lower-alpha > li::before {
			    content: counter(list-counter, lower-alpha) ".";
			}
			.prose ul[type="square"] > li::before {
				border-radius: 0;
			}
    	</style>
    @endpush
    @livewire('navigation-menu')
    <div class="pt-16 bg-gray-100">
        <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0">
            <div class="w-full sm:max-w-7xl mt-6 p-6 bg-white shadow-md overflow-hidden sm:rounded-lg prose">
            	<h1>Condiții generale privind furnizarea serviciilor poștale ale <br> societății AMR COLET SRL</h1>
				<h2>
				    INFORMAŢII DE IDENTIFICARE:
				</h2>
				<p>
				    Număr de ordine în Registrul Comerţului: J26/659/2021, atribuit în data de
				    14.04.2021
				</p>
				<p>
				    Cod unic de înregistrare: 44117011
				</p>
				<p>
				    Adresă sediu social: Targu-Mures, str. Livezeni, nr.26, Judet Mures
				</p>
				<p>
				    Pagina web: <a href="{{ route('home') }}">{{ route('home') }}</a>
				</p>
				<p>
				    E-mail: amrcolet@gmail.com
				</p>
				<p>
				    Serviciu clienți: 0757579845
				</p>
				<h3>
				    I. Definiții.
				</h3>
				<ol start="1" type="1" class="ml-4">
					<li>
			            Colet poştal - trimitere poştală cu greutatea maximă de 31,5 kg, ce
			            conţine bunuri cu sau fără valoare comercială;
			        </li>
				    <li>
				        Trimitere poștală (denumită, în cele ce urmează expediere) - bun
				        adresat, aflat în forma finală în care urmează să fie transportat și
				        livrat la adresa indicată de expeditor pe trimiterea în sine, pe
				        ambalaj sau într-o listă de distribuție. Pe lângă trimiterile de
				        corespondență, în această categorie sunt incluse, coletele poștale care
				        conțin bunuri cu sau fără valoare comerciala;
				    </li>
				    <li>
				        Serviciul express - serviciu poştal care presupune, în mod cumulativ:
						<ul type="circle">
						    <li>
						        eliberarea de către furnizor expeditorului a unui document care permite
						        furnizorului identificarea internă a trimiterii în reţeaua poştală şi
						        care atestă data, ora şi minutul depunerii, precum şi, de regulă, plata
						        tarifului;
						    </li>
						    <li>
						        predarea trimiterii poştale la adresa destinatarului, personal către
						        acesta sau către persoana autorizată să primească trimiterea poştală;
						    </li>
						    <li>
						        predarea rapidă a trimiterii poştale;
						    </li>
						    <li>
						        răspunderea furnizorului pentru nerespectarea timpilor de livrare;
						    </li>
						</ul>
					</li>
					<li>
					    Serviciul de trimitere contra ramburs - serviciul poştal a cărui
					    particularitate constă în achitarea de către destinatar expeditorului, prin
					    intermediul rețelei poștale, a contravalorii bunului care face obiectul
					    trimiterii poştale înregistrate;
					</li>
					<li>
					    Serviciul confirmare de primire - serviciu poştal a cărui particularitate
					    constă în predarea către expeditor a dovezii privind predarea trimiterii
					    poştale înregistrate, confirmată în scris de către destinatar;
					</li>
					<li>
					    Serviciul schimbare destinaţie - serviciu poştal având ca obiect trimiteri
					    poştale înregistrate, a cărui particularitate constă în posibilitatea
					    schimbării înainte de predarea trimiterii, la solicitarea expresă a
					    expeditorului comunicată furnizorului într-un termen convenit în prealabil
					    cu acesta, a destinatarului sau a adresei de livrare, precum şi în
					    posibilitatea opririi predării trimiterii poştale;
					</li>
					<li>
					    Serviciul livrare specială - serviciul poştal având ca obiect trimiteri
					    poştale înregistrate, a cărui particularitate constă în predarea trimiterii
					    poştale, personal destinatarului sau persoanei autorizate să primească
					    trimiterea poştală, potrivit indicaţiilor expeditorului privind fie data şi
					    ora predării, la adresa indicată, fie ordinea de predare, în cazul mai
					    multor destinatari;
					</li>
					<li>
					    Serviciul de trimitere cu valoare declarată - serviciul poștal a cărui
					    particularitate constă în asigurarea unei trimiteri poștale înregistrate
					    împotriva pierderii, furtului, distrugerii totale sau parțiale ori
					    deteriorării, pentru o sumă care nu poate depăși valoarea declarată de
					    către expeditor, și în eliberarea, la cerere, ulterior depunerii, respectiv
					    livrării trimiterii poștale, a unei dovezi privind depunerea trimiterii
					    poștale sau livrarea la destinatar, fără a fi confirmată în scris de către
					    acesta;
					</li>
					<li>
					    Serviciul de trimitere recomandată - serviciul poștal ale cărui
					    particularități constau în oferirea unei garanții forfetare împotriva
					    riscurilor de pierdere, furt, distrugere totală sau parțială ori
					    deteriorare a trimiterii poștale înregistrate și în eliberarea, la cerere,
					    ulterior depunerii, respectiv livrării trimiterii poștale, a unei dovezi
					    privind depunerea trimiterii poștale sau livrarea la destinatar, fără a fi
					    confirmată în scris de către acesta;
					</li>
				</ol>
				<h3>
				    II. Dispoziții generale.
				</h3>
				<ol start="1" type="1" class="ml-4">
				    <li>
				        Aceste condiții generale privind furnizarea serviciilor poștale ale AMR
				        COLET SRL se aplică raporturilor juridice legate de furnizarea de
				        servicii poştale născute, conform prevederilor legale, în temeiul
				        acestui document.
				    </li>
				    <li>
				        Părțile contractante sunt clientul și AMR COLET SRL, care acceptă
				        comanda de a prelua și livra trimiterile poștale. Livrarea trimiterii
				        poștale se poate efectua inclusiv prin rețeaua poștală a partenerilor
				        contractati de AMR COLET SRL . Fiecare trimitere poștală se indentifică
				        în rețeaua poștală de către AMR COLET SRL printr-un număr unic de
				        transport (AWB).
				    </li>
				    <li>
				        AMR COLET SRL oferă servicii poștale având ca obiect atât trimiteri
				        poștale interne, cât și internaționale.
				    </li>
				</ol>
				<h3>
				    III. Serviciile poştale incluse în sfera serviciului universal oferite şi
				    prestate de AMR COLET SRL sunt:
				</h3>
				<ol start="1" type="1" class="ml-4">
				    <li>
				        Servicii constând în colectarea, sortarea, transportul și livrarea
				        trimiterilor poștale interne si internaționale în greutate de până la 2
				        kg (inclusiv) (trimiteri de corespondență, imprimate);
				    </li>
				    <li>
				        Servicii constând în colectarea, sortarea, transportul si livrarea
				        coletelor poștale interne și internaționale în greutate de până la 10
				        kg (inclusiv);
				    </li>
				    <li>
				        Servicii constând în distribuirea coletelor poștale internaționale cu
				        limite de greutate între 10 kg și 20 kg (inclusiv) expediate din afara
				        teritoriului României către o adresă aflată pe teritoriul acesteia;
				    </li>
				    <li>
				        Serviciul de trimitere recomandată având ca obiect trimiteri poștale
				        interne si internaționale în greutate de până la 2 kg (inclusiv)
				        (trimiteri de corespondență, imprimate);
				    </li>
				    <li>
				        Serviciul de trimitere cu valoarea declarată având ca obiect trimiteri
				        poștale interne si internaționale în greutate de până la 2 kg
				        (inclusiv) (trimiteri de corespondență, imprimate) sau colete poștale
				        interne si internaționale în greutate de până la 10 kg (inclusiv),
				        respectiv colete poștale internaționale cu limite de greutate între 10
				        kg si 20 kg (inclusiv) expediate din afara teritoriului României către
				        o adresă aflată pe teritoriul acesteia.
				    </li>
				</ol>
				<h3>
				    IV. Serviciile poştale neincluse în sfera serviciului universal oferite şi
				    prestate de AMR COLET SRL sunt:
				</h3>
				<ol start="1" type="1" class="ml-4">
				    <li>
				        Servicii constând în colectarea, sortarea, transportul și livrarea
				        trimiterilor poștale interne si internaționale în greutate mai mare de
				        2 kg (corespondență, imprimate);
				    </li>
				    <li>
				        Servicii constând în colectarea, sortarea, transportul si livrarea
				        coletelor poștale interne cu limite de greutate între 10 şi 31,5 kg
				        (inclusiv);
				    </li>
				    <li>
				        Servicii constând în colectarea, sortarea si transportul coletelor
				        poștale internaționale cu limite de greutate între 10 kg si 31,5 kg
				        (inclusiv) expediate de pe teritoriul României către o adresă aflată în
				        afara teritoriului acesteia;
				    </li>
				    <li>
				        Servicii constând în distribuirea coletelor poștale internaționale cu
				        limite de greutate între 20 kg și 31,5 kg (inclusiv) expediate din
				        afara teritoriului României către o adresă aflată pe teritoriul
				        acesteia;
				    </li>
				    <li>
				        Serviciul contra ramburs;
				    </li>
				    <li>
				        Serviciul schimbare destinație;
				    </li>
				    <li>
				        Serviciul livrare specială;
				    </li>
				    <li>
				        Serviciul confirmare de primire;
				    </li>
				    <li>
				        Serviciul express.
				    </li>
				</ol>
				<h3>
				    V. Condiții pe care trebuie să le îndeplinească trimiterile poștale:
				</h3>
				<ol start="1" type="1" class="ml-4">
				    <li>
				        Dimensiunea si greutatea trimiterii poștale:
				    
						<p>
						    Vor fi acceptate trimiterile poștale cu următoarele dimensiuni și greutăți:
						</p>
						<p>
						    Greutate maximă:
						</p>
						<p>
						    30 kg – trimiteri poștale internaționale care fac obiectul serviciului
						    express ;
						</p>
						<p>
						    31,5 kg – toate celelalte trimiteri poștale interne si internaționale;
						</p>
						<p>
						    Lungime maximă: 120 cm pentru trimiteri poștale internaționale care fac
						    obiectul serviciului express, 175 cm pentru toate celelalte trimiteri
						    postale interne (inclusiv cele care fac obiectul serviciului Express) si
						    internationale
						</p>
						<p>
						    Circumferința + lungime &lt;= 300 cm (Circumferința = 2 x înălțime + 2 x
						    lățime) pentru toate trimiterile poștale interne si internaționale.
						</p>
					</li>
				    <li>
				        Modul de ambalare sau gradul de prelucrare a trimiterii poștale:
				    
						<p>
						    Clientul este unic răspunzător pentru asigurarea ambalării adecvate
						    interioare si exterioare a trimiterilor poștale, în raport cu natura și
						    greutatea conținutului, acestea trebuind sa fie ambalate cu un înveliș
						    exterior opac, astfel încât să nu permită vizualizarea conținutului.
						    Coletele vor fi ambalate in mod compact.
						</p>
						<p>
						    În cazul trimiterilor poştale având ca obiect bunuri fragile, clientul are
						    obligația să asigure o ambalare corespunzătoare, în raport cu natura
						    bunului fragil introdus în rețeaua poștală AMR COLET SRL, utilizând ca
						    umplutură talaș, polistiren antișoc sau alt material (de exemplu: folie cu
						    bule, talaş, polistiren antişoc etc.) care atenuează șocul mecanic și să
						    aibă aplicată inscripția „Fragil”. La solicitarea scrisă din partea
						    clientului expeditor, AMR COLET SRL va asigura etichetarea corespunzătoare
						    a trimiterii poștale având ca obiect bunuri fragile.
						</p>
						<p>
						    În toate cazurile, clientul trebuie să asigure ambalarea astfel încât
						    conținutul trimiterilor poştale să fie suficient de protejat in conditii
						    normale de manipulare, sortare, transport si depozitare. În plus, este
						    obligatoriu să nu fie posibil accesul la conținutul trimiterilor fără a
						    lăsa urme.
						</p>
						<p>
						    La modalitatea de ambalare, clientul trebuie să se asigure că bunurile ce
						    urmează a fi expediate nu vor fi ambalate în mai multe colete individuale
						    legate/prinse între ele, care să circule sub acelaşi document unic de
						    transport.
						</p>
						<p>
						    Recomandări şi instrucţiuni privind metodele de ambalare, în funcţie de
						    categoria bunurilor, sunt descrise pe larg pe site-ul subscrisei:
						    <a href="https://amrcolet.ro/info/packaging-policy">
						        https://amrcolet.ro/info/packaging-policy
						    </a>
						</p>
					</li>
				    <li>
				        Modul de completare a datelor de identificare a destinatarului: trebuie
				        să fie corecte si complete, astfel: Nume, Prenume, Localitate, Adresa,
				        numărul de telefon (recomandabil) etc.; recomandabil a fi înscrise și
				        datele expeditorului, cu excepția serviciului contra ramburs, caz în
				        care este obligatoriu să fie înscrise și datele expeditorului;
				    </li>
				    <li>
				        Zonele geografice în care AMR COLET SRL poate livra trimiterile poștale
				        prin mijloace proprii, incluzand şi zonele geografice în care poate
				        asigura livrarea prin intermediul unui alt furnizor: întreg teritoriul
				        României și internațional conform Ofertei comerciale;
				    </li>
				    <li>
				        Dovada identităţii/calităţii de reprezentant al expeditorului a
				        persoanei care depune trimiterea poștală la punctul de acces deservit
				        de personal: în cazul persoanei fizice, prin act de identitate/în cazul
				        persoanei juridice, prin delegație/împuternicire, semnată de
				        reprezentantul legal, insotita de actul de identitate al
				        delegatului/imputernicitului;
				    </li>
				    <li>
				        În cazul serviciului Contra ramburs: limita maximă admisă a contra
				        rambursului este de 5.000 lei/ persoană juridică și 10.000 lei/
				        persoană fizică, conform art. 3 alin. (2) din Legea nr. 70/2015 pentru
				        întărirea disciplinei financiare privind operațiunile de încasări și
				        plăți în numerar și pentru modificarea si completarea Ordonanței de
				        urgență a Guvernului nr. 193/2002 privind introducerea sistemelor
				        moderne de plată, cu modificările și completările ulterioare.
				        Modalitatea de colectare şi, respectiv, de achitare a rambursului este
				        în numerar sau prin virament în cont bancar;
				    </li>
				    <li>
				        Modalităţile de plată a tarifului serviciului poştal:
					    <ul type="circle">
					        <li>
					            Numerar la sediul societatii sau plata cu cardul pe site-ul
					            subscrisei <a href="https://amrcolet.ro/">https://amrcolet.ro/</a>.
					            Clientii - persoanele fizice si persoanele juridice - trebuie sa
					            achite contravaloarea serviciului la momentul depunerii trimiterii
					            poştale (COP – plata la expeditor);
					        </li>
					        <li>
					            Numerar/ordin de plata/bilet la ordin/file CEC/compensare - în
					            cazul persoanelor juridice cu care s-au incheiat contracte (în baza
					            unor oferte individuale negociate), suplimentare ofertei comerciale
					            a AMR COLET SRL;
					        </li>
					    </ul>
				    </li>
				    <li>
				        Moneda (admisă) în care se poate face încasarea, respectiv achitarea
				        sumelor de bani în cazul serviciului Contra ramburs: RON pentru
				        trimiterile poştale interne, LEVA, FORINTI, ZLOTI sau EURO pentru
				        trimiterile postale internationale colectate/livrate din Bulgaria,
				        Ungaria , Polonia, Grecia. Sume maxime admise la incasare contra
				        ramburs fiiind: Ungaria - 500,000 HUF, Grecia – 500 EUR, Polonia -
				        15.000 PLN, Bulgaria – contravaloarea a 500 EUR, acestea fiind
				        singurele țări în care AMR COLET SRL oferă și prestează serviciul
				        Contra ramburs.
				    </li>
				</ol>
				<h3>
				    VI. Sunt excluse de la colectare, sortare, transport şi livrare:
				</h3>
				<ol type="A" class="ml-4 list-inside list-alpha">
					<li>
						<ol start="1" type="1" class="ml-4">
						    <li>
						        Trimiteri poștale care nu pot fi prelucrate cu personalul şi mijloacele
						        obişnuite de care dispune furnizorul (care exced dimensiunilor şi
						        limitelor de greutate de mai sus); a căror livrare este interzisă de
						        dispoziţiile legale; etc.;
						    </li>
						    <li>
						        Trimiteri poştale având ca obiect bunuri cu valoare speciala, cu
						        deosebire metale prețioase, bijuterii veritabile, pietre prețioase,
						        perle veritabile, antichități, lucrări de artă, tablouri care fac parte
						        din patrimoniul național;
						    </li>
						    <li>
						        Trimiteri poştale având ca obiect bunuri contrafăcute, produse ADR,
						        instrumente de negociere, bani, documente de valoare, acte originale,
						        titluri de valoare, file CEC, bilete la ordin, cambii, cartele
						        telefonice sau certificate de valoare similare, acte de identitate,
						        carduri bancare;
						    </li>
						    <li>
						        Trimiteri poştale având ca obiect vouchere si bilete de acces cu o
						        valoare declarată mai mare de 520 euro/ trimiterea poştală (sau
						        echivalent in RON);
						    </li>
						    <li>
						        Trimiteri poştale având ca obiect blănuri, covoare, ceasuri, alte
						        articole de bijuterie si bunuri din piele cu o valoare declarată mai
						        mare de 520 euro / trimitere poştală (sau echivalent in RON);
						    </li>
						    <li>
						        Trimiteri poştale având ca obiect bunuri (produse) perisabile, bunuri
						        (produse) alimentare, medicamente;
						    </li>
						    <li>
						        Trimiteri poştale având ca obiect orice alte bunuri cu o valoare
						        declarată mai mare de 13.000 euro/trimitere poştală (sau echivalent in
						        RON);
						    </li>
						    <li>
						        Trimiteri postale al caror continut si/sau aspect exterior contravin
						        legii;
						    </li>
						    <li>
						        Trimiteri poştale având ca obiect arme de foc asa cum sunt definite de
						        legea referitoare la arme de foc din Romania, o tara de tranzit sau
						        tara colectare ori de destinatie, parti/ componente ce alcatuiesc/ fac
						        parte din categoria arme de foc sau arme dezasamblate, munitie pentru
						        arme de foc – indiferent de tip, forma sau calibru;
						    </li>
						    <li>
						        Trimiterile postale constand in bunuri al căror transport este interzis
						        prin dispoziții legale, fie chiar și numai pe o porțiune din parcurs;
						    </li>
						    <li>
						        Trimiterile postale al căror ambalaj prezintă inscripții care contravin
						        ordinii publice sau bunelor moravuri, precum și trimiterile postale
						        constând în bunuri care contravin ordinii publice sau bunelor moravuri,
						        dacă se depun neambalate sau in ambalaj transparent;
						    </li>
						    <li>
						        Trimiterile postale care prezintă etichete sau inscripții vechi
						        neîndepărtate;
						    </li>
						    <li>
						        Trimiterile postale care, prin modalitatea de ambalare sau prin natura
						        continutului, pot cauza deteriorari unor bunuri sau pot pune in pericol
						        persoane, precum şi trimiteri poştale având ca obiect tigari (peste 10
						        pachete tigari), animale vii sau moarte, materiale pentru examinari
						        medicale sau biologice, deseuri medicale, substante stupefiante,
						        ramasite umane sau animale, parti ale corpului sau organe;
						    </li>
						    <li>
						        In cazul trimiterilor postale internationale, continutul trimiterilor
						        al caror export sau import este interzis sau necesita aprobari speciale
						        in concordanta cu reglementarile tarii respective, de colectare/
						        livrare postala, tranzit sau destinatie. Astfel, se vor respecta,
						        suplimentar, de către ambele părţi, dispoziţiile legale incidente în
						        domeniul vamal, precum şi legislaţia statelor pe teritoriul cărora se
						        prestează operaţiuni componente ale serviciilor poştale şi a celor
						        tranzitate de trimiterea poştală.;
						    </li>
						    <li>
						        Trimiterile postale constând în bunuri pentru care sunt stabilite
						        condiții speciale de transport, prin dispoziții legale administrative,
						        economice, sanitare, veterinare, fitosanitare și altele similare, în
						        condițiile prevăzute prin aceste dispoziții, intrucat AMR COLET SRL nu
						        este o persoana juridica autorizata in sensul prestarii unor asemenea
						        servicii;
						    </li>
						</ol>
					</li>

					<li>
					    AMR COLET SRL este imputernicită sa refuze livrarea daca, dupa
					    acceptarea trimiterii postale, AMR COLET SRL descopera un motiv de
					    excludere sau daca exista motive temeinice de presupunere a excluderii
					    trimiterii poştale de la colectare, sarcina probei revind în aceste
					    circumstanţe AMR COLET SRL.
					</li>
					<li>
					    Acceptarea în vederea livrării a trimiterilor postale depuse închise,
					    cuprinzând bunuri excluse, despre existenţa cărora AMR COLET SRL nu are
					    cunoştinţă, nu reprezinta o renuntare la excluderea de la livrare;
					</li>
					<li>
					    In plus fata de orice raspundere constituita legal, clientul va raspunde
					    pentru orice paguba directa cauzată de bunurile care fac obiectul
					    trimiterilor postale, care sunt excluse de la acceptare (colectare).
					</li>
				</ol>
				<h3>
				    VII. Scopul serviciilor. Termene de păstrare.
				</h3>
				<ol start="1" type="1" class="ml-4">
				    <li>
				        Serviciile acoperă:
					    <ol start="1" type="1">
					        <li>
					            Organizarea serviciului poştal de catre AMR COLET SRL, de la
					            colectarea si până la livrarea trimiterilor poştale la destinatar.
					            AMR COLET SRL colecteaza si livreaza trimiterile postale inclusiv
					            prin personal si mijloace aparţinând colaboratorilor contractati,
					            respectiv prin intermediul punctelor de acces apartinand
					            partenerilor contractati in acest sens;
					        </li>
					        <li>
					            Livrarea, cu scopul indeplinirii obligatiei, destinatarului sau
					            persoanei autorizate, la adresa indicată de expeditor, cu exceptia
					            cazurilor in care exista un motiv plauzibil de a pune la indoiala
					            dreptul acestei persoane de a prelua trimiterea poştală - nu este
					            obligatorie verificarea de către AMR COLET SRL a identitatii
					            acestei persoane, cu exceptia trimiterilor postale recomandate, AMR
					            COLET SRL asumându-şi răspunderea, în consecinţă, cu privire la
					            acest fapt. AMR COLET SRL oferă, de asemenea, posibilitatea de a
					            livra trimiterile poştale la un sistem automat de livrare. AMR
					            COLET SRL nu livrează trimiteri poştale la cutia poştală.
					        </li>
					        <li>
					            Returul către client (expeditor) a oricaror trimiteri poştale ce nu
					            pot fi livrate sau sunt refuzate de destinatar;
					        </li>
					    </ol>
				    </li>
				    <li>
				        Cu excepţia trimiterilor poştale care fac obiectul serviciilor Express
				        şi Livrare specială, daca destinatarul nu este gasit, AMR COLET SRL va
				        efectua si o a doua incercare de livrare, informând destinatarul cu
				        privire la următoare încercare de livrare, inclusiv in cuprinsul
				        avizului de la pct.6 de mai jos;
				    </li>
				    <li>
				        AMR COLET SRL va efectua implicit doua incercari de livrare, in zile
				        lucratoare consecutive. In cazul in care destinatarul sau persoana
				        autorizata sa primeasca trimiterea postala nu este gasita la destinatie
				        nici la a doua încercare de livrare, se poate totusi stabili cu
				        expeditorul si/ sau destinatarul, şi detaliile unei noi (ultimei)
				        incercari de livrare, fără a implica vreun tarif suplimentar, in cadrul
				        perioadei avizate conform pct.6 de mai jos.,
				    </li>
				    <li>
				        In cazul in care destinatarul refuza trimiterea, returul trimiterii
				        postale se proceseaza automat in aceeasi zi, fără parcurgerea
				        procedurii de avizare a destinatarului.;
				    </li>
				    <li>
				        In cazul in care trimiterea postala nu poate fi predata destinatarului
				        si nici returnata expeditorului aceasta va fi pastrata la ultima
				        locatie avizata timp de 9 luni de la data preluarii, perioadă care nu
				        va implica plata unor tarife în vederea păstrării.
				    </li>
				    <li>
				        Cu excepţia trimiterilor poştale care fac obiectul serviciilor Express
				        şi Livrare specială, în cazul imposibilităţii inițiale de predare a
				        trimiterilor poştale către destinatar (sau persoana autorizată), AMR
				        COLET SRL va înştiinţa destinatarul printr-un aviz care să anunţe
				        încercarea de livrare a trimiterii poştale şi va păstra, fără a implica
				        vreun tarif , la punctul de contact specificat, în vederea predării,
				        trimiterea poştală care nu a putut fi predată acestuia, pentru o
				        perioadă de 7 zile calendaristice de la data avizării acestuia. Avizul
				        va contine detalii despre: numarul documentului de transport, data
				        avizarii destinatarului, perioada de pastrare a trimiterii postale la
				        dispozitia destinatarului, precum si denumirea, adresa si programul de
				        lucru cu publicul al punctului de contact de la care poate fi ridicata
				        trimiterea postala, iar în cazul în care la adresa destinatarului nu
				        este instalat niciun recipient pentru primirea trimiterilor poştale,
				        AMR COLET SRL va lua toate măsurile pentru a se asigura că avizul va
				        ajunge la cunoştinţa destinatarului, inclusiv prin transmiterea
				        acestuia prin mijloace electronice..
				    </li>
				    <li>
				        Pentru serviciile poştale contra ramburs, confirmare de primire,
				        schimbare destinaţie, livrare specială şi, respectiv, pentru serviciile
				        având ca obiect trimiteri poştale cu valoarea declarată, AMR COLET SRL
				        are obligaţia de a elibera, la momentul acceptării trimiterii poştale,
				        un înscris care să ateste serviciul ales de către expeditor.
				    </li>
				    <li>
				        Momentul acceptării trimiterii poştale în reţeaua poştală este momentul
				        depunerii la punctul de acces nedeservit de personal/ preluării
				        trimiterii poştale de către personalul/reprezentanţii furnizorului.
				    </li>
				    <li>
				        In momentul acceptarii (colectarii) / preluarii trimiterilor poştale,
				        acestea se vor cantari, masura si greutatea ce se va lua in calcul va
				        fi cea mai mare dintre greutatea fizica si cea volumetrica.
				    </li>
				</ol>
				<h3>
				    VIII. Timpii de livrare şi termene specifice anumitor tipuri de servicii.
				</h3>
				<ol start="1" type="1" class="ml-4">
				    <li>
				        Trimiteri postale interne:
				        <ul type="circle">
						    <li>
						        Cu excepţia serviciului express și livrare specială, timpii de livrare
						        garantaţi pentru serviciile poştale având ca obiect trimiteri postale
						        interne, sunt de 2 zile lucratoare. De asemenea, prin derogare de la
						        acest termen, în cazul localitatile cu zile fixe de vizitare, astfel
						        indicate în oferta comercială a AMR COLET SRL, termenul de livrare a
						        trimiterilor poștale interne este de maxim 7 zile calendaristice
						        (acesta nefiind aplicabil în cazul serviciului express și livrare
						        specială).
						    </li>
						    <li>
						        Termenul de returnare a confirmarii de primire catre expeditor, in
						        cadrul serviciului poştal Confirmare de primire având ca obiect
						        trimiteri poştale interne, este de 2 zile lucratoare de la momentul
						        livrarii trimiterii poștale;
						    </li>
						    <li>
						        Returnarea contravalorii bunurilor care fac obiectul serviciului contra
						        ramburs având ca obiect trimiteri postale interne se face in termen de
						        maxim 7 zile lucratoare de la livrarea trimiterii poştale;
						    </li>
						</ul>
				    </li>
				    <li>
				        Trimiterile postale internationale:
				        <ul type="circle">
						    <li>
						        În cazul serviciilor poştale având ca obiect trimiteri poştale
						        internaţionale, cu excepţia serviciului Express, timpii de livrare
						        garantati sunt de 10 zile lucratoare.
						    </li>
						    <li>
						        Returnarea contravalorii trimiterii postale care face obiectul
						        serviciului contra ramburs având ca obiect trimiteri poştale
						        internaţionale se face in termen de 7 zile lucratoare de la livrarea
						        trimiterii;
						    </li>
						    <li>
						        AMR COLET SRL nu presteaza serviciul confirmare de primire având ca
						        obiect trimiteri poştale internationale;
						    </li>
						</ul>
				    </li>
				    <li>
				        Timpii de livrare garantati pentru serviciul express:
				        <ul type="circle">
						    <li>
						        Timpii de livrare garantaţi pentru serviciul express încep de la
						        acceptarea trimiterii postale in vederea livrarii şi vor respecta
						        următoarele condiţii:
						        <ul type="square">
							        <li>
							            în cazul trimiterilor poştale interne, timpii de livrare nu vor
							            depăşi 12 ore în aceeaşi localitate, 24 de ore între reşedinţele de
							            judeţ şi intra-judeţean, respectiv 36 de ore între oricare alte
							            două localităţi.
							        </li>
							        <li>
							            Timpii de livrare garantati pentru trimiteri postale internationale
							            care fac obiectul serviciului express:
							            <ul type="square">
								            <li>
								                în cazul trimiterilor poştale internaţionale, timpul cât
								                trimiterea postala se află pe teritoriul României nu poate
								                depăşi limitele prevăzute de prezentele conditii aferente
								                trimiterilor poştale interne;
								            </li>
								            <li>
								                în cazul trimiterilor poştale internaţionale colectate de pe
								                teritoriul României şi care urmează a fi livrate unui
								                destinatar aflat în unul dintre statele membre ale Uniunii
								                Europene sau ale Spaţiului Economic European, timpul cât
								                trimiterea postala se află în afara teritoriului României este
								                de 120 de ore;
								            </li>
								            <li>
								                în cazul trimiterilor poştale internaţionale colectate de pe
								                teritoriul României şi care urmează a fi livrate pe teritoriul
								                unui stat aflat in afara Uniunii Europene sau in afara
								                Spaţiului Economic European, timpul cât trimiterea postala se
								                află în afara teritoriului României este de maxim 168 de ore;
								            </li>
								        </ul>
							        </li>
							    </ul>
						    </li>
						</ul>
				    </li>
				    <li>
				        AMR COLET SRL va raspunde fata de expeditor pentru predarea cu
				        intarziere a trimiterilor postale, respectiv a returului cu intarziere,
				        prin plata unor penalitati in cuantum de 10% din tariful serviciului
				        indiferent de intarziere, cu exceptia serviciului Express, in cazul
				        caruia raspunde în acelaşi cuantum per 12 ore intarziere, fara a depasi
				        insa contravaloarea tarifului serviciului postal Express.
				    </li>
				    <li>
				        AMR COLET SRL prestează serviciul de trimitere recomandată care are
				        obiect trimiteri poştale interne si internaţionale.
				    </li>
				    <li>
				        Termenul în care poate fi solicitată de către expeditor dovada privind
				        depunerea sau, respectiv, livrarea la destinatar a trimiterii poştale
				        care face obiectul serviciului de trimitere recomandată este de 9 luni
				        de la data colectării de către AMR COLET SRL a respectivei trimiteri
				        poştale. Dovada solicitată se comunică în termen de 7 zile de la data
				        solicitării acesteia, printr-o modalitate agreată cu expeditorul
				        (e-mail, adresă scrisă etc.).
				    </li>
				    <li>
				        Serviciile poştale având ca obiect trimiteri poştale (interne sau
				        internaţionale) cu valoare declarată sunt prestate de către AMR COLET
				        SRL doar in temeiul unor contracte incheiate cu expeditorii,
				        suplimentar ofertei comerciale AMR COLET SRL.
				        <p>
						    Conform prezentelor reglementari, limita maximă admisă a valorii declarate
						    este de 13.000 euro/trimitere poştală,sau echivalent in RON.Prin excepţie,
						    pentru sumele valorii declarate care depasesc 5000 euro (sau echivalentul
						    în lei), este posibila optarea pentru serviciul de trimitere cu valoare
						    declarata numai cu consultarea si confirmarea AMR COLET SRL anterior
						    introducerii trimiterii poștale în rețeaua poștală a AMR COLET SRL, în
						    condiţii contractuale stabilite expres în acest sens.
						</p>
				    </li>
				    <li>
				        Termenul în care poate fi solicitată de către expeditor dovada privind
				        depunerea, respectiv livrarea trimiterii poştale care face obiectul
				        unui serviciu având ca obiect bunuri cu valoare declarată este de 9
				        luni de la data colectării de către AMR COLET SRL a respectivei
				        trimiteri poştale. Dovada solicitată se comunică în termen de 7 zile de
				        la data solicitării acesteia, conform modalitătii de transmitere si
				        comunicare prevăzute în contractul dintre părţi, în temeiul căruia se
				        prestează serviciul (de ex. e-mail, adresă scrisă etc.).
				    </li>
				</ol>
				<h3>
				    IX. Returnarea trimiterii poştale
				</h3>
				<ol start="1" type="1" class="ml-4">
				    <li>
				        AMR COLET SRL are obligaţia de a returna expeditorului trimiterea
				        poştală înregistrată care nu a putut fi predată destinatarului din una
				        dintre următoarele cauze:
				        <ol type="a" class="ml-4 list-inside list-lower-alpha">
					        <li>
							    adresa destinatarului nu există sau la adresa indicată nu există nicio
							    construcţie ori un serviciu disponibil destinat primirii trimiterii
							    poștale;
							</li>
							<li>
							    destinatarul sau persoana autorizată să primească trimiterea poştală nu a
							    fost găsit(ă) la adresa indicată, după expirarea, atunci când este cazul, a
							    termenului de păstrare în vederea predării;
							</li>
							<li>
							    destinatarul sau persoana autorizată să primească trimiterea poştală a
							    refuzat să primească, după caz, trimiterea poştală, confirmarea în scris,
							    conform prevederilor legale, a primirii trimiterii poştale în cazul
							    serviciului Confirmare de primire ori achitarea contravalorii bunului care
							    face obiectul serviciului contra ramburs;
							</li>
						</ol>
				    </li>
				    <li>
				        Returnarea se va face la cel mai apropiat punct de acces/contact de
				        adresa expeditorului, şi nu va implica, in sarcina expeditorului, plata
				        unor tarife în acest sens, expeditorul fiind, în prealabil, informat in
				        scris despre posibilitatea preluarii trimiterii poştale de la
				        respectivul punct de acces/contact.
				    </li>
				    <li>
				        Termenul de returnare a trimiterii postale la expeditor/integrator este
				        de 5 zile lucratoare pentru trimiterile postale interne, respectiv de
				        10 zile lucratoare pentru trimiterile postale internationale, după caz,
				        de la expirarea termenului de pastrare, in vederea predarii, sau de la
				        data ultimei incercări de livrare a trimiterilor postale care nu au
				        putut fi predate destinatarului ori care au fost refuzate, conform
				        lit.c de mai sus, de către destinatar.
				    </li>
				</ol>
				<h3>
				    X. Plata serviciilor.
				</h3>
				<ol start="1" type="1" class="ml-4">
				    <li>
				        Cu exceptia cazurilor in care a fost prevazut anterior contrar prin
				        intermediul unor contracte scrise între utilizator si AMR COLET SRL,
				        plata serviciilor se va efectua in conformitate cu lista de preturi
				        actuala a furnizorului de servicii.
				    </li>
				    <li>
				        Moneda in care se face achitarea tarifului serviciilor prestate este
				        RONpe teritoriul Roamaniei;
				    </li>
				    <li>
				        La calcularea greutătii taxabile a coletului se va lua in considerare
				        greutatea cea mai mare dintre greutatea gravimetrica (determinata prin
				        cantarirea efectiva) si greutatea volumetrica (greutatea volumetrica =
				        lungime x latime x inaltime in cm / 5000) rezultata in Kg.
				    </li>
				    <li>
				        In cazul in care Clientul nu plateste valoarea facturilor in termenul
				        convenit, AMR COLET SRL va aplica penalitati in cuantum de 0.15 %
				        pentru fiecare zi de intarziere, calculate la valoarea scadenta si
				        neachitata.
				    </li>
				</ol>
				<h3>
				    XI. Obligatia de cooperare.
				</h3>
				<ol start="1" type="1" class="ml-4">
				    <li>
				        Clientul este responsabil sa se asigure ca datele înscrise pe
				        trimiterea poştală sau pe ambalajul acesteia sunt lipsite de erori. De
				        asemenea, pentru situatiile in care Clientul alege să completeaze pe
				        cont propriu documentul de transport prin intermediul aplicatiei
				        informatice puse la dispozitie de catre AMR COLET SRL, Clientul este
				        responsabil sa se asigure ca datele furnizate sunt lipsite de erori si
				        corect atasate de trimiterea postala sau de ambalajul acesteia.
				    </li>
				    <li>
				        Cand trimiterile postale au drept continut bunuri impozabile, clientul
				        raspunde de asigurarea intregii documentatii necesare pentru vama, prin
				        atasarea acesteia la trimiterea poştală, intr-un plic exterior.
				    </li>
				</ol>
				<h3>
				    XII. Deschiderea, dispunerea si distrugerea de trimiteri postale.
				</h3>
				<ol start="1" type="1" class="ml-4">
				    <li>
				        Secretul trimiterilor poştale este inviolabil;
				    </li>
				    <li>
				        AMR COLET SRL nu va deschide trimiterea postala si nu va conditiona
				        prestarea serviciilor postale de deschiderea trimiterii postale depusa
				        inchisa;
				    </li>
				    <li>
				        Retinerea, deschiderea si divulgarea continutului trimiterilor postale
				        este permisa numai in conditiile si cu procedura prevazute de lege;
				    </li>
				    <li>
				        AMR COLET SRL poate distruge trimiterea postala care a produs sau poate
				        produce iminent pagube importante persoanelor, mediului, instalatiilor
				        utilizate sau altor trimiteri postale, cu informarea expeditorului,
				        daca aceasta este posibila. In acest caz contractul inceteaza de drept.
				        Sarcina probei revine AMR COLET SRL.
				    </li>
				</ol>
				<h3>
				    XIII. Responsabilitatea costurilor.
				</h3>
				<ol start="1" type="1" class="ml-4">
				    <li>
				        Costurile pentru formalitatile vamale si pentru declaratia vamala de
				        export (dupa caz) sunt suportate de catre expeditor. In cazul cererilor
				        de colectare, destinatarul va suporta doar costurile pentru
				        formalitatile vamale;
				    </li>
				    <li>
				        In cazul importului din afara U.E., taxele de import, TVA sau orice
				        alte taxe aplicabile sunt facturate destinatarului. In cazul exportului
				        in afara U.E., taxele de import, TVA sau orice alte taxe aplicabile
				        sunt facturate destinatarului, iar daca acestea nu sunt acceptate la
				        prima solicitare, acestea vor fi imputate si recuperate AMR COLET SRL
				        de conform prevederilor si procedurilor legale in vigoare;
				    </li>
				    <li>
				        Clientul va despagubi AMR COLET SRL pentru toate costurile legitime
				        suportate de AMR COLET SRL din momentul deschiderii si / sau dispunerii
				        si/sau distrugerii trimiterii postale conform Sectiunii XII.
				    </li>
				</ol>
				<h3>
				    XIV. Raspunderea.
				</h3>
				<ol start="1" type="1" class="ml-4">
				    <li>
				        Pentru furtul, pierderea totală sau parţială, distrugerea toală sau
				        parţială sau deteriorarea trimiterilor poştale interne si
				        internationale, suvernite între momentul colectării şi cel al livrării
				        la destinatar, AMR COLET SRL raspunde in conformitate cu prevederile
				        O.U.G. 13/2013, completate cu celelalte dispozitii legale in vigoare.
				    </li>
				</ol>
				<h3>
				    XV. Limitele de de raspundere.
				</h3>
				<p class="ml-4">
				    Beneficiar al eventualelor despagubiri este numai Clientul (expeditorul sau
				    destinatarul);
				</p>
				<ol start="1" type="1" class="ml-4">
				    <li>
				        Nu suntem raspunzatori si nu ne sunt imputabile pierderile speciale
				        (pierderi de profit, de venit, dobanzi, piete de desfacere, licitatii)
				        sau alte daune si pagube indirecte generate de intarzierea, pierderea,
				        distrugerea, livrarea viciata sau nelivrarea trimiterii poștale;
				    </li>
				    <li>
				        In cazul in care AMR COLET SRL va transmite obligatia de prestare a
				        servicilor postale catre o terta persoana, raspunderea pentru
				        eventualele daune cauzate clientului va ramane in sarcina AMR COLET
				        SRL;
				    </li>
				    <li>
				        In cazul furtului, pierderii totale sau partiale, distrugerii totale
				        sau partiale ori al deteriorarii trimiterii postale, AMR COLET SRL
				        raspunde pentru paguba pricinuita, daca aceste imprejurari au survenit
				        intre momentul depunerii trimiterii si momentul livrarii la destinatar;
				    </li>
				    <li>
				    	<ol start="1" type="1" class="list-roman">
				        	<li>
					        	AMR COLET SRL răspunde pentru trimiterile postale interne si internationale, după cum urmează:
						        <ul type="circle">
								    <li>
								        în caz de pierdere, furt sau distrugere totală:
								        <ul type="square">
									        <li>
									            cu întreaga valoare declarată, pentru o trimitere poştală care face
									            obiectul unui serviciu de trimitere cu valoare declarată, inclusiv
									            dacă respectiva trimitere poştală face sau nu obiectul unui
									            serviciu contra ramburs;
									        </li>
									        <li>
									            cu valoarea rambursului, pentru o trimitere poştală care face
									            obiectul unui serviciu contra ramburs fără valoare declarată;
									        </li>
									        <li>
									            cu suma reprezentând de 5 ori tariful serviciului, pentru
									            trimiterile poştale care nu fac obiectul unui serviciu de trimitere
									            cu valoare declarată sau unui serviciu contra ramburs;
									        </li>
									    </ul>
								    </li>
								    <li>
							            în caz de pierdere ori distrugere parțială sau deteriorare:
								        <ul type="square">
								            <li>
								                cu valoarea declarată pentru partea lipsă, distrusă sau
								                deteriorată ori cu cota-parte corespunzătoare greutății lipsă
								                din valoarea declarată, pentru trimiterile poștale care fac
								                obiectul unui serviciu de trimitere cu valoare declarată;
								            </li>
								            <li>
								                cu suma reprezentând de 5 ori tariful serviciului, în caz de
								                pierdere parțială, distrugere parțială sau deteriorare a
								                trimiterilor poștale care nu fac obiectul unui serviciu de
								                trimitere cu valoare declarată;
								            </li>
								        </ul>
							        </li>
							        <li>
							            în cazul unei trimiteri care face obiectul unui serviciu contra
							            ramburs, furnizorul de servicii poştale răspunde cu întreaga
							            valoare a rambursului pentru situaţia în care nu a restituit
							            expeditorului întreaga valoare a acestuia sau cu diferenţa
							            corespunzătoare până la valoarea integrală a acestuia, în cazul în
							            care contra rambursul a fost încasat parţial de la destinatar;
							        </li>
								</ul>
					        </li>
					        <li>
							    La sumele prevăzute mai sus se adaugă dobânda legală penalizatoare
							    care curge din momentul introducerii reclamației prealabile sau, dupa caz,
							    al introducerii cererii de chemare în judecată, indiferent care dintre
							    aceste momente intervine primul;
							</li>
							<li>
							    Pierderea completă a conținutului este echivalentă cu pierderea
							    trimiterii poștale;<br><br>
							    În situația în care expeditorul a declarat o valoare mai mică decât cea
							    reală, despăgubirea este la nivelul valorii declarate;
							</li>
							<li>
							    În afara despăgubirilor prevăzute, se restituie proportional și
							    tarifele încasate, în limita valorică a neîndeplinirii obligației asumate
							    de către AMR COLET SRL. Restituirea integrală a tarifelor încasate are loc
							    doar în cazul pierderii, furtului sau a distrugerii totale;
							</li>
							<li>
							    În cazul neefectuării prestațiilor care constituie caracteristici
							    suplimentare ale serviciilor poștale, nominalizate de expeditor prin
							    indicații speciale, se restituie numai tarifele încasate suplimentar față
							    de tariful aplicabil pentru serviciul poștal standard;
							</li>
							<li>
							    În cazul pierderii dovezii privind predarea trimiterii poștale
							    înregistrate, confirmată în scris de către destinatar, furnizorul de
							    serviciu poștal are obligația întocmirii și punerii la dispoziția
							    expeditorului a unui duplicat al dovezii de predare;
							</li>
							<li>
							    Expeditorul poate renunța la dreptul sau de despăgubire în favoarea
							    destinatarului;
							</li>
						</ol>
				    </li>
				    <li>
				        AMR COLET SRL este exonerat de răspundere în următoarele situaţii:
				        <ul type="circle">
						    <li>
						        paguba s-a produs ca urmare a faptei expeditorului sau destinatarului;
						    </li>
						    <li>
						        trimiterea a fost primită fără obiecţii de către destinatar, cu
						        excepţia reclamaţiilor referitoare la pierderea, furtul, deteriorarea
						        sau distrugerea totală ori parţială a conţinutului trimiterii poştale;
						    </li>
						    <li>
						        paguba s-a produs ca urmare a unui caz de forţă majoră sau a cazului
						        fortuit; în acest caz, expeditorul are dreptul la restituirea tarifelor
						        achitate, cu excepţia tarifului de asigurare;
						    </li>
						    <li>
						        utilizatorul nu are asigurat un serviciu destinat primirii trimiterilor
						        poştale (de exemplu, registratură).
						    </li>
						</ul>
				    </li>
				</ol>
				
				<h3>
				    XVI. Mecanismul de soluționare a reclamațiilor.
				</h3>
				<ol start="1" type="1" class="ml-4">
				    <li>
				        Orice reclamație referitoare la prestarea necorespunzătoare sau
				        neprestarea serviciului poștal se va face în scris, prin completarea
				        unei reclamații și va putea fi trimisă prin următoarele căi de
				        comunicare:
				        <ul type="circle">
						    <li>
						        prin poștă, recomandabil cu confirmare de primire, la sediul social al
						        SC AMR COLET SRL;
						    </li>
						    <li>
						        la sediul societatii SC AMR COLET SRL, prin depunere personala
						    </li>
						    <li>
						        scanată, pe e-mail, folosind următoarea adresă:
						        <a href="mailto:office@amrcolet.ro">
						            office@amrcolet.ro
						        </a>
						    </li>
						</ul>

						<p>
						    Reclamația trebuie să conțină inclusiv informații complete și corecte
						    legate de numărul de cont, banca și sucursala la care este deschis,
						    informații în temeiul cărora, ulterior să fie platită despăgubirea, dacă
						    este cazul, pentru situații în care petentul dorește ca plata să fie
						    efectuată prin virament bancar.
						</p>
				    </li>
				    <li>
				        Fiecare reclamație va fi înregistrată primind un număr de înregistrare
				        (numar care va fi ulterior trecut în toate comunicările care se vor
				        face între prestatorul de servicii poștale, AMR COLET SRL, și
				        reclamant), iar confirmarea primirii acesteia se va face corelativ
				        modalităţii prin care aceasta a fost înaintată către AMR COLET SRL,
				        respectiv prin completarea corespunzătoare și semnarea confirmării de
				        primire (exclusiv în cazul reclamaţiilor transmise către AMR COLET SRL
				        prin intermediul serviciului Confirmare de primire), adresă scrisă
				        transmisă printr-un serviciu poştal (în cazul reclamațiilor transmise
				        prin poștă fără confirmare de primire), prin înmânarea numărului de
				        înregistrare, în cazul reclamaţiilor depuse personal, prin e-mail ori
				        printr-o metodă solicitată de către petent în reclamație, atunci când
				        acesta a solicitat transmiterea printr-o modalitate distinctă de cea
				        prin care a înaintat reclamaţia către AMR COLET SRL;

				        <p>
						    Dreptul de a face reclamație îl are atât expeditorul, cât și destinatarul,
						    respectiv de imputerniciții legali ai acestora. Orice reclamație se va
						    înregistra în „Registrul Electronic de Reclamații”;
						</p>
				    </li>
				    <li>
				        Obiectul reclamației îl poate constitui:
				        <ul type="circle">
						    <li>
						        pierderea, furtul, distrugerea / deteriorarea totala sau parțială a
						        trimiterilor poștale;
						    </li>
						    <li>
						        nerespectarea timpului de predare, respectiv de returnare a
						        trimiterilor poștale;
						    </li>
						    <li>
						        conduita profesională a reprezentanților AMR COLET SRL;
						    </li>
						    <li>
						        orice alte obiecții referitoare la calitatea prestațiilor;
						    </li>
						</ul>
				    </li>
				    <li>
				        Termenul de introducere a reclamaţiei prealabile adresate furnizorului
				        de servicii poştale – în speță AMR COLET SRL - este de șase luni şi se
				        calculează de la data depunerii trimiterii poștale;
				    </li>
				    <li>
				        Reclamația se face individual, pentru fiecare trimitere poștală în
				        parte ;
				    </li>
				    <li>
				        Reclamația trebuie să fie însoțită de documente relevante,
				        corespunzătoare evenimentului reclamat, cum ar fi, de exemplu:
				        <ul type="circle">
						    <li>
						        Dovadă preluare trimitere poștală – copie borderou
						    </li>
						    <li>
						        Orice dovezi pe care reclamantul le consideră relevante şi pe baza
						        cărora își întemeiază pretențiile în vederea efectuării unor analize
						        corecte și complete şi a soluţionării reclamaţiei (de exemplu,
						        fotografii, descrierea bunurilor care fac obiectul trimiterii poştale,
						        bunul distrus/deteriorat care face obiectul trimiterii poştale,
						        ambalajul trimiterii poştale etc.);
						    </li>
						    <li>
						        Documente fiscale (copie bon fiscal, chitanta, ordin de plata etc.)
						        care atestă plata serviciului reclamat, atunci când plata a fost
						        efectuată de persoana reclamantă;
						    </li>
						    <li>
						        Proces verbal constatator încheiat în prezența reprezentantului AMR
						        COLET SRL sau a partenerilor contractati, dacă este cazul;
						    </li>
						</ul>
				    </li>
					<li>
					    Expeditorul / destinatarul sau imputerniciții legali ai acestora vor avea
					    dreptul la despăgubiri, numai dacă reclamația a fost introdusă în cadrul
					    termenelor prevăzute mai sus;
					</li>
					<li>
					    Termenul de soluționare a reclamației (implicând analiză reclamaţie,
					    comunicare răspuns şi acordare despăgubiri) este de 60 zile de la data
					    depunerii acesteia.
					</li>
				</ol>
				<h3>
				    XVII. Procedura de despagubire.
				</h3>
				<ol start="1" type="1" class="ml-4">
				    <li>
				        Termenul de plată a despăgubirilor este de 15 zile de la data
				        soluţionării reclamaţiei, fără a depăși însă termenul de 60 de zile
				        menționat anterior, plata făcându-se fie în numerar, la sediul social
				        din Targu-Mures , fie prin transfer bancar, în funcție de preferința
				        utilizatorului îndreptățit.
				    </li>
				    <li>
				        Dacă în urma analizei documentelor puse la dispoziție de utilizator se
				        constată ca, în baza Condițiilor generale privind furnizarea
				        serviciilor poștale ale AMR COLET SRL, ce fac parte din oferta
				        comercială a AMR COLET SRL, acesta este îndreptățit să primească o sumă
				        de bani cu titlu de despăgubire, plata acestor sume se va realiza în
				        termenul stabilit mai sus, astfel:
				        <ul type="circle">
						    <li>
						        persoane juridice:
						        <p>
								    În cazul în care reclamaţia clientului este intemeiată, fiind soluţionată
								    favorabil, acesta va fi informat în acest sens și va trebui să emită și să
								    transmită către AMR COLET SRL documentatia finaciar-contabila legal
								    necesara in astfel de situatii (facturile aferente etc).
								</p>
						    </li>
						    <li>
						        persoane fizice:
						        <p>
								    În cazul în care reclamaţia clientului este întemeiată, fiind soluţionată
								    favorabil, acesta va fi informat în acest sens. În cazul persoanelor
								    fizice, despagubirea se poate achita și în numerar la sediul social din
								    Targu-Mures, cu excepția cazului în care s-a solicitat prin formularul de
								    reclamație plata prin virament bancar, cu precizarea coordonatelor bancare
								    necesare plății.
								</p>
						    </li>
						</ul>
				    </li>
				    <li>
				        În cazul reclamaţiilor întemeiate, furnizorul va acorda despăgubirile
				        în termenul şi în condiţiile menţionate la pct.1 şi 2, fără a fi
				        necesară în acest sens vreo solicitare din partea utilizatorului.
				    </li>
				    <li>
				        Expeditorii trimiterilor poștale răspund față de furnizorul de servicii
				        poștale, AMR COLET SRL, pentru daunele rezultate din natura periculoasă
				        a trimiterilor poștale sau a condițiilor de ambalare neadecvate ale
				        acestora, în limita valorii daunelor și a sumelor plătite ca
				        despăgubire altor beneficiari ai serviciilor poștale ale căror
				        trimiteri poștale au fost deteriorate din această cauză.
				    </li>
				</ol>
				<h3>
				    XVIII. Jurisdicția, legislația aplicabilă, invaliditate parțială
				</h3>
				<ol start="1" type="1" class="ml-4">
				    <li>
				        Orice dispute care apar din oferta comercială a AMR COLET SRL vor fi
				        soluționate pe cale amiabilă; daca aceasta nu este posibilă se vor
				        apela entităţile competente.
				    </li>
				    <li>
				        Invaliditatea dispozițiilor individuale a acestor condiții nu va
				        prejudicia validitatea dispozițiilor rămase.
				    </li>
				    <li>
				        Orice modificări ulterioare ale Condițiilor generale privind furnizarea
				        serviciilor poștale ale AMR COLET SRL vor fi disponibile pe site-ul
				        oficial <a href="http://www.amrcolet.ro/">http://www.amrcolet.ro</a> cu
				        cel puțin 30 de zile înainte ca modificările respective să intre în
				        vigoare.
				    </li>
				    <li>
				        Prevederile Condițiilor generale privind furnizarea serviciilor poștale
				        ale AMR COLET SRL se completează în mod corespunzător cu prevederile
				        legislației în vigoare aplicabile în domeniu.
				    </li>
				    <li>
				        În cazul în care reclamaţia adresată AMR COLET SRL nu a fost
				        soluţionată în mod satisfăcător sau nu s-a răspuns la aceasta în
				        termenul prevăzut în prezentele condiţii generale privind furnizarea
				        serviciilor poştale, utilizatorul în cauză poate înainta, în termen de
				        un an de la data depunerii trimiterii poştale, o plângere autorităţii
				        de reglementare însoţită de dovada îndeplinirii procedurii reclamaţiei
				        prealabile sau o cerere de chemare în judecată. Cererea de chemare în
				        judecată poate fi introdusă indiferent dacă o plângere având acelaşi
				        obiect a fost înaintată sau nu autorităţii de reglementare.
				    </li>
				</ol>
				<p>
				    Prezentul document face parte integrantă din oferta comercială a AMR COLET
				    SRL reprezentând clauzele generale ale contractului individual ce se va
				    considera încheiat între expeditor şi AMR COLET SRL la momentul acceptării
				    trimiterii poştale în reţeaua poştală. Contractul individual se încheie
				    prin acceptarea de către expeditor a ofertei AMR COLET SRL, fără a fi
				    necesară întocmirea unui înscris.
				</p>
				<h4>
				    SC AMR COLET SRL
				</h4>
				<p>
				    Administrator,<br>
				    Oroian Mihaela
				</p>
            </div>
        </div>
    </div>
</x-guest-layout>
