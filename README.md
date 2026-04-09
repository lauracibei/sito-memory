<h1>MemInfo🖥️🧠</h1>

An interactive web application that reimagines the classic "Memory" game with a tech twist. The project goes beyond just the game itself, offering a comprehensive user experience that includes registration, global leaderboards, and a blog section.

<h2>✨ Key Features</h2>

Looking at the project structure, I implemented several advanced features:

<ul>
  <li><b>🎮 The Memory Game:</b> The core of the app, featuring computer science-themed cards to test your memory.</li>
  <li><b>🔐 User Authentication:</b> A registration and login system that allows users to save their scores.</li>
  <li><b>🏆 Global Leaderboard:</b> A dynamic leaderboard showing the players with the highest scores.</li>
  <li><b>📝 Blog Section:</b> An integrated blog where administrators can publish articles (using the <b>TinyMCE</b> editor for text formatting).</li>
  <li><b>🍪 Privacy Management:</b> A cookie consent banner integrated in compliance with regulations.</li>
</ul>

<h2>🛠️ Technologies Used</h2>

<ul>
  <li><b>Back-end:</b> PHP</li>
  <li><b>Front-end:</b> HTML, CSS, JavaScript (for game logic and interactions)</li>
  <li><b>Database:</b> MySQL (user management, scores, and blog posts)</li>
  <li><b>External Libraries:</b> TinyMCE (WYSIWYG editor for the blog)</li>
</ul>

<h2>📂 Project Structure</h2>

The code is modularly organized for easy maintenance:
- `/autenticazioni`: Scripts for login, registration, and session management.
- `/blog` & `/tinymce_8.3.1`: Article management and text editor library.
- `/classifica`: Logic for fetching and displaying scores from the database.
- `/database`: Configuration files for DB connection (and SQL scripts).
- `/gioco`: Memory game engine (JS logic and layout).
- `/homepage`: Main landing page.
- `/immagini`: Graphic assets, icons, and game cards.

<h2>🚀 How to run the project locally</h2>

To test this project on your computer, you will need a local server environment like **XAMPP**, **MAMP**, or **WAMP**.

1. **Clone the repository:**
   ```bash
   git clone [https://github.com/](https://github.com/)[Your-Username]/[Repository-Name].git

2. **Move the files:** Place the project folder inside the root directory of your local server (e.g., `htdocs` for XAMPP).
3. **Configure the Database:**
   * Open phpMyAdmin.
   * Create a new database named `[your_database_name]`.
   * Import the SQL file located in the `/database` folder to create the necessary tables (users, leaderboard, blog posts).
4. **Connect the Database:** Ensure that the connection parameters (username, password, db name) in the PHP files inside the `/database` folder are correct.
5. **Launch the site:** Open your browser and go to `http://localhost/[project-folder-name]/index.php`.

<h2> 🧠 What I Learned </h2>

Building this project was a great hands-on experience that allowed me to put theory into practice. It helped me bridge the gap between front-end design and back-end logic. Here are the main skills I developed:

* **Full-Stack Integration:** Connecting the front-end interface (HTML/CSS/JS) with a back-end server (PHP) to create a dynamic and interactive application.
* **Database Management:** Designing and interacting with a MySQL database to securely handle user registrations, leaderboards, and blog posts.
* **JavaScript Logic:** Writing the core mechanics of the Memory game, managing DOM manipulation, state, and event listeners.
* **Authentication & Sessions:** Implementing a login/registration system and managing user sessions securely in PHP.
* **Third-Party Libraries:** Learning how to integrate and configure external tools, such as the TinyMCE WYSIWYG editor for the blog section.
* **Project Architecture:** Organizing a complex project into a clean, modular folder structure (separating auth, database, game logic, etc.) to keep the codebase maintainable.
