<x-guest-layout>
    <x-jet-banner />
    @livewire('navigation-menu')
    <div class="pt-16 bg-gray-100">
        <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0">
            <div class="w-full sm:max-w-7xl mt-6 p-6 bg-white shadow-md overflow-hidden sm:rounded-lg prose">
            	@isset($packaging)
                {!! $packaging !!}
                @endisset
                <h1 align="center"><u>{{ __('Packaging Policy') }}</u></h1>
				<p>
                    Societatea noastră este planificată în așa fel încât să ridice și să
                    livreze plicuri, colete și o gamă largă de piese paletizate sau
                    nepaletizate. Restricțiile de greutate și dimensiuni pentru coletele
                    transportate se aplică pentru a asigura transportul și manipularea
                    trimiterilor în depozitele firmelor de curierat colaboratoare în condiții
                    de maximă siguranță.
                </p>
                <p>
                    Pentru a evita deteriorările pe parcursul tranzitului prin rețeaua de
                    curierat, toate expedițiile trebuie ambalate într-un mod adecvat pentru
                    transportul vrac sau consolidat. Acest ghid prezintă recomandările noastre
                    de ambalare, adresate atât clienților expeditori, cât și personalului
                    firmelor de curierat care este responsabil de procesul de acceptare a
                    expedițiilor de la clienți și oferă îndrumări de ambalare specifice
                    diferitelor tipuri de mărfuri.
                </p>
                <p>
                    Acest ghid nu înlocuiește normele de ambalare create pentru fiecare
                    industrie, dar clarifică standardele minime necesare pentru acceptarea
                    mărfii în rețeaua de curierat, norme necesare pentru a evita deteriorarea
                    mărfii trimise, pentru a elimina deteriorarea altor expediții, echipamente
                    și pentru a preveni accidentarea personalului.
                </p>
                <p>
                    În cazul în care aceste standarde nu sunt respectate, există posibilitatea
                    ca societatea de curierat selectată să nu accepte bunurile pentru transport
                    sau să solicite achitarea unei taxe suplimentare.
                </p>
                <h2>
                    <strong>Greutate și dimensiune</strong>
                </h2>
                <h3>
                    <strong>DPD </strong>
                </h3>
                <p>
                    Greutate maximă admisă per colet este <strong>31.5 kg</strong> pentru
                    livrari „usa la usa” ;
                </p>
                <p>
                    Lungimea maxima, admisa per colet este 175 cm;
                </p>
                <p>
                    Pentru expedieri prin automatele DPD, greutatea maximă admisă per colet
                    este <strong>20kg</strong>, iar dimensiunile maxime acceptate sunt 60 x 35
                    x 37cm.
                </p>
                <p>
                Pentru expedieri prin oficiile DPD, greutatea maximă admisă per colet este <strong>31.5 kg.</strong>
                </p>
                <p>
                    Oficiile DPD nu pot primi de la un singur expeditor, într-o singură zi, mai
                    mult de 30 de colete iar greutatea totală nu va depăşi 500kg.
                </p>
                <h3>
                    <strong>CARGUS</strong>
                </h3>
                <p>
                    Greutate maximă admisă per colet este <strong>31 kg</strong> pentru livrari
                    „usa la usa” ;
                </p>
                <p>
                    Lungimea maxima, admisa per colet este 175 cm; iar suma dimensiunilor
                    (LxlxH) nu poate depasi 240 cm. Expedierile ce depasesc aceasta dimensiune
                    vor fi transportate in regim de transport marfa.
                </p>
                <h2>
                    <strong>Folosirea tipului corespunzător de ambalaj</strong>
                </h2>
                <p>
                    Este recomandată folosirea unui carton rezistent cu dublu strat pentru
                    ambalarea expedițiilor, cutii de lemn sau paleți (după caz) și bandă
                    adezivă rezistentă, astfel încât să reziste la suprapozare și conținutul să
                    nu fie vizibil. Ambalajul trebuie ales în funcție de conținut și trebuie să
                    se utilizeze materiale de umplere, pentru a împiedica produsele să se miște
                    în interiorul acestuia. În cazul expedierii mai multor cutii pe un palet,
                    acestea trebuie separate cu plăci din carton, iar trebuie înfășurat cu
                    folie protectoare.
                </p>
                <p>
                    Cutiile care nu sunt suficient încărcate se pot deforma iar cele încărcate
                    peste capacitate se pot deforma sau pot lua forme neregulate,
                    nesuprapozabile. Este posibil ca articolele cu forme neregulate și
                    nesuprapozabile să fie acceptate la transport cu plata unei taxe
                    suplimentare la societatea de curierat.
                </p>
                <p>
                    Plicurile și coletele expediate prin rețeaua de curierat trebuie să fie
                    compatibile cu sistemele automate de sortare sau cu instalațiile de
                    manipulare (minim dimensiuni format A5).
                </p>
                <p>
                    Stivuirea cutiilor pe tip coloană într-un palet este cel mai bun mod pentru
                    menținerea rezistenței acestora în timpul transportului și pentru
                    protejarea bunurilor împotriva compresiei. Stivuiți cutiile pe coloane,
                    colț în colț și margine în margine, pentru cea mai mare rezistență la
                    stivuire. Paletul poate fi apoi stabilizat și fixat cu bandă sau folie
                    întinsă. Nu aranjați bunurile pe palet în formă de piramidă și asigurați-vă
                    că nu depășesc marginea paletului. Recomandăm folosirea protecțiilor pentru
                    margini, a cartoanelor și a întăritoarelor, precum și securizarea și
                    ancorarea acestora.
                </p>
                <p>
                    Vă rugăm să vă asigurați de faptul că expediția dumneavoastră poate suporta
                    o suprapunere cu alte piese, de faptul că nu este afectată de o eventuală
                    înclinare, de eventuale vibrații sau șocuri minore.
                </p>
                <p>
                    Tipul de ambalaj considerat corespunzător pentru diferite tipuri de mărfuri
                    este:
                </p>
                <table border="1" cellspacing="0" cellpadding="0">
                    <tbody>
                        <tr>
                            <td width="208" valign="top">
                                <p>
                                    <strong>Categorie </strong>
                                </p>
                            </td>
                            <td width="208" valign="top">
                                <p>
                                    <strong>Produs</strong>
                                </p>
                            </td>
                            <td width="208" valign="top">
                                <p>
                                    <strong>Ambalaj </strong>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td width="208" rowspan="8" valign="top">
                                <p>
                                    Mărfuri fragile
                                    <br/>
                                    Casante
                                </p>
                            </td>
                            <td width="208" valign="top">
                                <p>
                                    Sticlărie, geamuri, lichide
                                </p>
                            </td>
                            <td width="208" valign="top">
                                <p>
                                    Ambalaj special destinat pentru acestea, compartimentat,
                                    din lemn, umplut cu polistiren
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td width="208" valign="top">
                                <p>
                                    Produse cosmetice
                                </p>
                            </td>
                            <td width="208" valign="top">
                                <p>
                                    Ambalaj de carton multistratificat umplut cu polistiren și
                                    sigilat cu folie protectoare
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td width="208" valign="top">
                                <p>
                                    Tablouri / gravuri
                                </p>
                            </td>
                            <td width="208" valign="top">
                                <p>
                                    Ambalaj din lemn umplut cu polistiren
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td width="208" valign="top">
                                <p>
                                    Piese auto, echipament electro-casnic
                                </p>
                            </td>
                            <td width="208" valign="top">
                                <p>
                                    Ambalaj de carton presat, umplut cu polistiren
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td width="208" valign="top">
                                <p>
                                    Dischete, benzi, CD-uri
                                </p>
                            </td>
                            <td width="208" valign="top">
                                <p>
                                    Ambalaj de carton sau plastic umplut cu folie cu bule
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td width="208" valign="top">
                                <p>
                                    Articole de pescuit
                                </p>
                            </td>
                            <td width="208" valign="top">
                                <p>
                                    Ambalaj de carton multiplus stratificat umplut cu
                                    polistiren
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td width="208" valign="top">
                                <p>
                                    Mobilă, piese mobilier și instrumente muzicale, jucării
                                </p>
                            </td>
                            <td width="208" valign="top">
                                <p>
                                    Ambalaj de carton presat umplut cu polistiren și sigilat cu
                                    folie protectoare
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td width="208" valign="top">
                                <p>
                                    Electronice
                                </p>
                            </td>
                            <td width="208" valign="top">
                                <p>
                                    Ambalaj de carton multistratificat umplut cu polistiren și
                                    sigilat cu folie protectoare
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td width="208" valign="top">
                                <p>
                                    Produse albe
                                </p>
                            </td>
                            <td width="208" valign="top">
                                <p>
                                    Frigider, mașină de spălat, aragaz
                                </p>
                            </td>
                            <td width="208" valign="top">
                                <p>
                                    Ambalaj de lemn umplut cu polistiren
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <h2>
                    <strong>Selectarea cutiilor folosite pentru ambalare</strong>
                </h2>
                <p>
                    Nu acceptăm cutii găurite, strivite sau deteriorate, cutii umede, din care
                    curg lichide sau care prezintă deteriorări cauzate de umezeală. Nu plasaţi
                    articole grele în cutii lipsite de rezistenţă. Cutiile trebuie să fie
                    suficient de rezistente, să fie superioare calitativ şi să aibă
                    dimensiunile adecvate. Dacă este necesar, utilizaţi benzi de plastic rigide
                    pentru a asigura un plus de rezistenţă cutiilor.
                </p>
                <h2>
                    <strong>Etichetarea</strong>
                </h2>
                <p>
                    Etichetarea clară reprezintă un avantaj esenţial. Aceasta ne permite să vă
                    trimitem articolele expediate la destinaţia corectă şi să le manipulăm
                    corect pe drum. Pregătirea corectă a etichetelor, cu informaţii despre
                    traseu şi instrucţiuni pentru manipulare specială, este foarte importantă
                    pentru livrarea la timp şi în condiţii perfecte. Asigurați vizibilitatea
                    etichetei, nu o acoperiți cu folie colorată, îndepărtați eventuale etichete
                    anterioare și tipăriți etichetele de transport care v-au fost trimise în
                    momentul lansării comenzii. Asigurați-vă că datele destinatarului și ale
                    expeditorului sunt clare, corecte și complete și că apar și pe ambalajul
                    expediției.
                </p>
                <strong>
                    Pentru a vă asigura de faptul că expediția dumneavoastră este preluată de
                    către firma de curierat selectată și că ajunge la destinație în acceași
                    stare precum la expediere, vă recomandăm vizualizarea cerințelor specifice
                    ale societății de curierat selectate. Societatea noastră nu își asumă
                    răspunerea pentru eventuale deteriorări ale expedițiilor datorate unei
                    împachetări necorespunzătoare.
                </strong>
            </div>
        </div>
    </div>
</x-guest-layout>
