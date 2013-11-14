<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Automatically generated strings for Moodle installer
 *
 * Do not edit this file manually! It contains just a subset of strings
 * needed during the very first steps of installation. This file was
 * generated automatically by export-installer.php (which is part of AMOS
 * {@link http://docs.moodle.org/dev/Languages/AMOS}) using the
 * list of strings defined in /install/stringnames.txt.
 *
 * @package   installer
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['admindirname'] = 'Cartella Admin';
$string['availablelangs'] = 'Elenco delle lingue disponibili';
$string['chooselanguagehead'] = 'Scegli la lingua';
$string['chooselanguagesub'] = 'Scegli la lingua da usare durante l\'installazione. La lingua usata nel sito e dagli utenti potrà essere modificata in seguito.';
$string['clialreadyconfigured'] = 'Il file config.php esiste già, per favore utilizza admin/cli/install_database.php se desideri installare questo sito.';
$string['clialreadyinstalled'] = 'Il file config.php è già presente, se desideri aggiornare il sito per favore utilizza admin/cli/upgrade.php.';
$string['cliinstallheader'] = 'Programma di installazione Moodle {$a} via linea di comando';
$string['databasehost'] = 'Host database';
$string['databasename'] = 'Nome database';
$string['databasetypehead'] = 'Scegli un database driver';
$string['dataroot'] = 'Cartella dati';
$string['datarootpermission'] = 'Permessi cartella dati';
$string['dbprefix'] = 'Prefisso tabelle';
$string['dirroot'] = 'Cartella di Moodle';
$string['environmenthead'] = 'Verifica dell\'ambiente...';
$string['environmentsub2'] = 'Ciascuna release di Moodle prevede come requisito minimo una data versione del PHP ed una serie di estensioni. Prima di una installazione o di un aggiornamento viene eseguita la verifica dei requisiti minimi. Se non sai come installare nuove versioni del PHP o le sue estensioni, contatta l\'amministratore del tuo server.';
$string['errorsinenvironment'] = 'Ci sono problemi nel vostro ambiente';
$string['installation'] = 'Installazione';
$string['langdownloaderror'] = 'Purtroppo non è stato possibile scaricare la lingua "{$a}". L\'installazione proseguirà in lingua Inglese.';
$string['memorylimithelp'] = '<p>Il limite di memoria assegnata al PHP attualmente è {$a}.</p>

<p>Tale limite potrà causare problemi nel funzionamento di Moodle, specialmente se usi molti moduli di attività con molti utenti.</p>

<p>Ti raccomandiamo di impostare il PHP con un limite più alto se possibile, ad esempio 40M.
Ci sono diversi modi che puoi provare:
<ol>
<li>Se possibile, ricompila il PHP con l\'opzione <i>--enable-memory-limit</i>.
Questo consentirà a Moodle di impostare in autonomia il limite di memoria.</li>
<li>Se hai accesso al file php.ini, è possibile modificare la variabile <b>memory_limit</b> a un valore più alto, ad esempio 40M. Se non hai accesso, potete chiedere al vostro amministratore di sistema di farlo.</li>
<li>Su alcuni server con il PHP è possibile creare un file .htaccess nella cartella di Moodle contenente questa linea:
<blockquote>php_value memory_limit 40M</blockquote>
<p>Tuttavia, su alcuni server la direttiva potrebbe impedire  a <b>tutte</b> le pagine PHP di funzionare (apapriranno degli erorri durante la visualizzazione delle pagine), in tal caso dovrai rimuovere il file .htaccess.</li></ol>';
$string['paths'] = 'Percorsi';
$string['pathserrcreatedataroot'] = 'Lo script di installazione non ha potuto creare la Cartella dei dati ({$a->dataroot}).';
$string['pathshead'] = 'Conferma percorsi';
$string['pathsrodataroot'] = 'Non è possibile scrivere nella  Cartella dati.';
$string['pathsroparentdataroot'] = 'La cartella genitore ({$a->parent}) non è scrivibile. Lo script di installazione non può creare la Cartella dati ({$a->dataroot}).';
$string['pathssubadmindir'] = 'Alcuni web host utilizzano la cartella /admin come URL di accesso a pannelli di controllo od altre funzioni particolari. Tuttavia questo nome coincide con il nome della cartella che Moodle utilizza per i propri file di amministrazione. Per evitare conflitti, è possibile specificare un nome alternativo per la cartella Admin di Moodle. Ad esempio:<p><b>moodleadmin</b></p>
Tutti i link che puntano ai file di amministrazione di Moodle terranno conto di questa variazione.';
$string['pathssubdataroot'] = 'E\' necessario specificare una cartella dove Moodle inserirà i file caricati dagli utenti. Il web server (in genere \'nobody\' o \'apache\') DEVE avere i permessi di lettura e di scrittura su questa cartella. In aggiunta, la cartella dei dati NON DEVE essere direttamente accessibile via web. Se la Cartella dei dati non esiste, lo script di installazione tenterà di crearla.';
$string['pathssubdirroot'] = 'Percorso assoluto della cartella di installazione di Moodle.';
$string['pathssubwwwroot'] = 'Indirizzo web per accedere a Moodle. Non è possibile accedere alla stessa installazione Moodle usando più di un indirizzi web. Se il tuo sito usa più indirizzi web, devi configurare dei re-indirizzamenti permanenti per tutti gli altri indirizzi.
Se il tuo sito è raggiungibile sia dalla Internet che dalla Intranet, allora usa l\'indirizzo Internet pubblico ed imposta il DNS in modo che anche gli utenti della Intranet possano accedere usando l\'indirizzo pubblico.
Se l\'indirizzo è errato per favore correggilo nella barra degli indirizzi del browser per avviare nuovamente l\'installazione.';
$string['pathsunsecuredataroot'] = 'La posizione della Cartella dati non è sicura';
$string['pathswrongadmindir'] = 'La cartella Admin non esiste';
$string['phpextension'] = '{$a} estensioni PHP';
$string['phpversion'] = 'Versione PHP';
$string['phpversionhelp'] = '<p>Moodle necessita come minimo della versione 4.3.0 o 5.1.0 del PHP. (La versione 5.0.x soffre di problemi ben conosciuti)</p>
<p>La versione installata nel vostro sistema è la {$a}</p>
<p>Dovete aggiornare la versione del PHP oppure spostarsi su un host che abbia una versione più aggiornata del PHP!<br>
(Se avete la 5.0.x, potete fare il downgrade alla versione 4.4.x)</p>';
$string['welcomep10'] = '{$a->installername} ({$a->installerversion})';
$string['welcomep20'] = 'Se vedi questa pagina hai installato correttamente e lanciato il pacchetto <strong>{$a->packname} {$a->packversion}</strong>. Complimenti!';
$string['welcomep30'] = 'La release di <strong>{$a->installername}</strong> include l\'applicazione per creare l\'ambiente necessario a far girare <strong>Moodle</strong>:';
$string['welcomep40'] = 'Il pacchetto include anche <strong>Moodle {$a->moodlerelease} ({$a->moodleversion})</strong>.';
$string['welcomep50'] = 'L\'utilizzo delle applicazioni incluse in questo pacchetto è regolato dalle rispettive licenze. L\'intero pacchetto <strong>{$a->installername}</strong> è <a href="http://www.opensource.org/docs/definition_plain.html">open source</a> ed è distribuito in accordo alla licenza <a href="http://www.gnu.org/copyleft/gpl.html">GPL</a>.';
$string['welcomep60'] = 'Le prossime pagine ti guideranno attraverso semplici passi per installare e configurare <strong>Moodle</strong> nel tuo computer. Puoi utilizzare le impostazioni di default oppure modificarle per adeguarle alle tue esigenze.';
$string['welcomep70'] = 'Fai click sul pulsante "Avanti" per continuare l\'installazione di <strong>Moodle</strong>.';
$string['wwwroot'] = 'Indirizzo web';
