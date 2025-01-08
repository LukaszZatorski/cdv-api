# Krótki opis

Baza jest w pliku data.db w głównym katalogu

Dla tworzonych na devie użytkowników ustawia się hasło "t4jn3h4slo" (na prodzie byłoby oczywiście losowo generowane i wysyłane za pomocą notifications przez wybrany kanał)

Zrobiłem AuthController jako że było to w opisie zadania ale wolałbym go pominąć i załatwić logowanie przez security.yaml (z użyciem LexikJWTAuthenticationBundle którego i tak używam). 
Żeby wygenerować klucze do jwt należy wykonać tą komendę "php bin/console lexik:jwt:generate-keypair"

Zakładam że ten jeden kontroler z punktu drugiego wymagań "jeden zabezpieczony zawierający metody C,R,U,D..." to ma być ten od lekcji z punktu pierwszego