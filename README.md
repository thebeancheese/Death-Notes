Disclaimer: All of the files are inside the Death_Notes folder, this includes the PHP, CSS, and SQL files.

Database directions:
  1) Download the repository zip, extract to C:\xampp\htdocs or wherever your xampp folder is.
  2) Make sure the file path is C:\xampp\htdocs\Death_Notes. This will matter especially on deploying the system using local host.
  3) Open XAMPP shell and create a database named "deathnotes".
       mysql -u root
       create database deathnotes;
       exit;
  4) On exit, change directory to C:\xampp\htdocs\Death_Notes and type the following:
       mysql -u root deathnotes < deathnotes.sql
  5) Database is succussfully imported. Open a web browser and go to localhost/Death_Notes as this will directly launch the system.


Bacani, Ivan
Bangit, Eisen Josh
Cruzada, Vince Raiezen
Dela Cruz, Anthony Fernan
