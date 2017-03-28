## Opencontent Mail Alert

Estensione per eZPublish Legacy per impostare l'invio di mail in base al risultato di una query eseguita con ocopendata


### Requisiti

* eZP >= 4.X or 5.X (solo in Legacy Stack)

### Installazione

* Copiare l'estensione in <ezpublish_legacy_root>/extensions
* Attivare l'estensione a livello di backend
* Installare la tabella sql secondo il tipo di db
* Rigenerare gli autoloads
* Svuotare le cache degli ini e dei template
* Configurare il crontab perché svolga una volta al giorno l'istruzione  `php runcronjobs.php -sbackend ocmailalert` sostituendo a backend il nome del siteaccess di backend
* Controllare che nel site.ini di backend sia correttamente configurato il SiteURL

### Utilizzo

L'estensione installa in ambiente di backend un nuovo elemento nel menu principale "Mail alert", attraverso cui si accede alla lista delgi alert attivi.
Dal menu di sinistra è possibile inserire un nuovo alert tramite la voce di menu 'Configure new alert'.

Ciascun alert è composto da:

* Un'etichetta (Label)
* Frequenza (Frequency)
* Una query in [OCQL](https://github.com/Opencontent/openservices/blob/master/doc/06-search-query.md) (Query)
* Una condizione (Send alert if result count is)
* Gli indirizzi destinatari (Email recipients (one per line))
* Il titolo della mail (Mail alert subject)
* Il testo della mail (Mail alert body)

Tutti i campi sono obbligatori. Il sistema valida anche se la query inserita è scritta correttamente.

Lo script che il cronjob esegue compie per ciascun alert le seguenti azioni:

* Controlla in base alla data dell'ultima esecuzione e della frequenza impostata nell'alert se deve rieseguire il controllo
* Nel caso in cui debba procedere, esegue la query impostata e valuta il risultato in base alla condizione impostata nell'alert
* Se la condizione è vera, invia una mail ai destinatari impostati nell'alert con oggetto e testo definiti nell'alert e con la lista dei primi 30 risultati ottenuti, salva poi il timestamp di esecuzione e la mail in database

Cliccando sul link 'Reset' presente nella lista degli alert è possibile azzerare il timestamp e forzare quindi il controllo al successivo passaggio del cronjob
