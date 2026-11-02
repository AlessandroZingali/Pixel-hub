# Pixel Hub

Un hub web per acquistare e scaricare videogiochi, sviluppato come progetto per l'esame di **Linguaggi per il Web**. La piattaforma simula le funzionalità principali di uno store digitale (ispirato a Steam), permettendo la consultazione del catalogo, l'acquisto tramite carrello, la gestione della libreria personale e l'accesso ad un'area utente riservata.

Il progetto è realizzato seguendo lo standard rigoroso **XHTML 1.0 Strict** per la struttura e la separazione dei contenuti.

## Come funziona

L'applicazione si appoggia ad un ambiente server locale gestito tramite **XAMPP**:

- **Front-End**: Realizzato in **XHTML 1.0 Strict** validato, con layout formattato tramite **CSS3** e comportamenti dinamici gestiti tramite **JavaScript**.
- **Back-End**: Realizzato in **PHP**, gestisce la sessione dell'utente, la logica del carrello, le transazioni simulate di acquisto e le interazioni con il database.
- **Database**: **MySQL** memorizza le informazioni relative ai giochi, agli utenti, ai ruoli e allo storico dei download/acquisti.

## Struttura del progetto

```
./Sorgente
├── index.php            # Home page dello store e vetrina dei giochi
├── catalog.php          # Catalogo completo con filtri e ricerca
├── game.php             # Scheda dettagliata del singolo videogioco
├── cart.php             # Gestione del carrello e procedura di checkout
├── library.php          # Libreria personale dell'utente per il download dei titoli
├── login.php            # Autenticazione e registrazione utente
├── css/                 # Fogli di stile CSS
├── js/                  # Script JavaScript per la validazione e l'interfaccia
├── includes/            # Script PHP di utilità, connessione DB e componenti riutilizzabili
├── db/                  # Dump SQL per l'importazione del database
└── README.md
```

## Requisiti

- [XAMPP](https://www.apachefriends.org/) (con moduli **Apache** e **MySQL** attivi)
- Browser web moderno (es. Chrome, Firefox, Edge)

## Installazione

1. Clona la repository all'interno della cartella `htdocs` di XAMPP:

```bash
cd C:/xampp/htdocs  # O la directory equivalente nel tuo sistema
git clone https://github.com/AlessandroZingali/Pixel-hub.git
```

2. Avvia i servizi **Apache** e **MySQL** dal *XAMPP Control Panel*.
3. Apri **phpMyAdmin** nel browser (`http://localhost/phpmyadmin`).
4. Crea un nuovo database (es. `pixel_hub`) e importa il file SQL presente nella cartella `db/`.
5. Configura i parametri di connessione al database nel file di configurazione presente in `includes/` se necessario.

## Utilizzo

Apri il browser e naviga all'indirizzo locale del progetto:

```
http://localhost/Pixel-hub
```

- Esplora i titoli presenti in home page o passa al catalogo completo.
- Registra un nuovo account o effettua il login per accedere alle funzionalità di acquisto.
- Aggiungi i giochi al carrello ed effettua il checkout per trasferirli nella tua **Libreria**.
- Dalla sezione Libreria puoi simulare il download dei file dei videogiochi acquistati.

## Note

- Progetto sviluppato a scopo puramente didattico ed accademico per il corso di **Linguaggi per il Web**.
- Assicurarsi che i moduli Apache e MySQL di XAMPP siano in esecuzione prima di navigare sul sito.

## Licenza e Diritti

Tutti i diritti riservati ad **Alessandro Zingali** e **Riccardo Tulini**.

---

# Pixel Hub (English)

A web hub to purchase and download video games, developed as a project for the **Web Languages** course. The platform simulates key functionalities of a digital store (inspired by Steam), allowing users to browse the catalog, buy games via cart, manage a personal library, and access a user area.

The project is built adhering to the strict **XHTML 1.0 Strict** standard for structure and content separation.

## How it works

The application runs on a local server environment managed through **XAMPP**:

- **Front-End**: Built with validated **XHTML 1.0 Strict**, styled with **CSS3**, and enhanced with client-side interactions using **JavaScript**.
- **Back-End**: Developed in **PHP**, handling user sessions, cart logic, simulated transactions, and database querying.
- **Database**: **MySQL** stores information about games, users, roles, and purchase/download history.

## Project Structure

```
.
├── index.php            # Store home page and game showcase
├── catalog.php          # Complete catalog with filters and search
├── game.php             # Individual video game detail page
├── cart.php             # Cart management and checkout process
├── library.php          # User personal library to download owned titles
├── login.php            # User authentication and registration
├── css/                 # CSS stylesheets
├── js/                  # JavaScript scripts for validation and UI logic
├── includes/            # PHP utility scripts, DB connection, and reusable components
├── db/                  # SQL dump file for database initialization
└── README.md
```

## Requirements

- [XAMPP](https://www.apachefriends.org/) (with active **Apache** and **MySQL** modules)
- A modern web browser (e.g., Chrome, Firefox, Edge)

## Installation

1. Clone the repository into your XAMPP `htdocs` directory:

```bash
cd C:/xampp/htdocs  # Or equivalent path on your OS
git clone https://github.com/AlessandroZingali/Pixel-hub.git
```

2. Start the **Apache** and **MySQL** services from the *XAMPP Control Panel*.
3. Open **phpMyAdmin** in your browser (`http://localhost/phpmyadmin`).
4. Create a new database (e.g., `pixel_hub`) and import the SQL file located in the `db/` folder.
5. Update database connection credentials in the configuration file under `includes/` if required.

## Usage

Open your browser and navigate to the project's local URL:

```
http://localhost/Pixel-hub
```

- Browse featured titles on the home page or view the full catalog.
- Register a new account or log in to unlock purchase features.
- Add games to your cart and complete checkout to add them to your **Library**.
- Access the Library section to simulate downloading owned games.

## Notes

- Developed solely for educational and academic purposes for the **Web Languages** university course.
- Make sure XAMPP Apache and MySQL modules are running before accessing the application.

## License & Rights

All rights reserved by **Alessandro Zingali** and **Riccardo Tulini**.
