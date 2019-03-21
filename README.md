# WP-tenttiarkisto
### Wordpress-plugin tenttiarkiston hallintaan Asteriskin sivuille (Wordpress-plugin for managing exams)

Plugin can seen in action at [asteriski.fi](http://asteriski.fi)

To do
-----------
* Tenttiarkiston uploaderi ei-kirjautuneille käyttäjille

Muuten valmis

Riippuvuudet / vaatimukset
-----------
Palvelimella tulee olla asennettuna thumbnailien luontian varten:
* Imagemagick
* Imagick

Imagick on Imagemagickin php-moduuli joten se täytyy olla myös ladattuna (extension=imagick.so php.ini-tiedostoon ja webbipalvelimen restarttaus)

Wordpressin asetuksista:
* Päivämäärät oltava muotoa dd.mm.yyyy (eli wordpressin asetuksissa d.m.Y)
* Pretty permalinks

Vähän ohjeita
-----------
Mene wp-content/plugins -hakemistoon
```
git clone https://github.com/asteriskiry/wp-arkisto.git
```
Plugin luo automaattisesti sivut example.com/tentit, mutta niille generoituu sisältöä vasta kun ensimmäiset pöytäkirjat/tentit lisätään
