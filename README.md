# Descrierea problemei

Se da o masina automata care vinde cafea / ciocolata calda / espresso / etc.
Masina are o cantitate default din diversele ingrediente (cafea, lapte, ciocolata, vanilie, etc.), si contine o 
lista predefinita pt. ce e nevoie pt. fiecare tip de bautura.

Fiecare bautura are un pret.

Masina poate accepta bancnote de 1, 5, 10 lei.

Masina este apelata prin intermediul cli, unde va primi o comanda ce va avea ca parametrii: Id-ul bauturii, 
cate bancnote si de ce tip au fost introduse.

Se cere codul care va avea urmatorul output:
1. Eroare daca nu poate fi preparata bautura
2. Eroare daca nu sunt suficienti bani pt. plata acesteia
3. Eroare daca se incearca utilizarea masinii de mai multe persoane in acelasi timp
4. Cantitatile ramase din fiecare ingredient, si restul la suma introdusa daca se poate prepara bautura


## Implementat
1. Automatul isi stocheaza starea (ingrediente, cash disponibil) in variabile-membru
2. Validarea id-ului bauturii - eroare daca bautura cu id-ul respectiv nu poate fi preparata
3. Validarea sumei platite daca bautura se poate prepara
4. Validarea disponibilitatii ingredientelor pentru preparare
5. Cantitatile ramase dupa o comanda
6. Calculul restului de plata
7. Blocarea automatului la preluarea unei singure comenzi la un moment dat cu punerea in asteptare a comenzii urmatoare

## De implementat
1. Automatul de preparare sa isi stocheze starea in baza de date
