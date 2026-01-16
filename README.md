# TugApp

Aplikacja do zarządzania zgłoszeniami i użytkownikami zbudowana w Laravel z wykorzystaniem panelu administracyjnego Filament.

## Funkcjonalności

### Panel Administracyjny

Aplikacja wykorzystuje Filament jako panel administracyjny, zapewniający intuicyjny interfejs do zarządzania danymi oraz bezpieczny dostęp do funkcji systemu.

### Zarządzanie Użytkownikami

**Dostęp:** Tylko dla użytkowników z rolą administratora

- **Tworzenie kont użytkowników** - możliwość dodawania nowych użytkowników do systemu
- **Edycja danych użytkowników** - modyfikacja informacji o użytkownikach, w tym adresu email i roli
- **Zarządzanie hasłami** - bezpieczne hashowanie haseł przy użyciu bcrypt, opcjonalna zmiana hasła podczas edycji
- **Przypisywanie ról** - system ról z dwoma poziomami dostępu:
  - **Admin** - pełny dostęp do wszystkich funkcji systemu, w tym zarządzania użytkownikami
  - **Użytkownik** - standardowy dostęp do podstawowych funkcji
- **Usuwanie użytkowników** - możliwość usuwania kont z systemu
- **Lista użytkowników** - przeglądanie wszystkich użytkowników z informacjami o loginie, emailu, roli oraz datach utworzenia i aktualizacji

### Zarządzanie Zgłoszeniami (Incydentami)

- **Tworzenie zgłoszeń** - rejestrowanie nowych incydentów związanych z pojazdami
- **Edycja zgłoszeń** - modyfikacja istniejących zgłoszeń
- **Usuwanie zgłoszeń** - usuwanie zgłoszeń z systemu (pojedynczo lub masowo)
- **Informacje o pojeździe** - przechowywanie szczegółowych danych pojazdu:
  - Numer rejestracyjny
  - Numer VIN
  - Marka pojazdu
  - Model pojazdu
  - Typ pojazdu
- **Opis zgłoszenia** - szczegółowy opis incydentu lub problemu
- **Statusy zgłoszeń** - system statusów z kolorowymi oznaczeniami:
  - **Otwarty** - nowo utworzone zgłoszenie
  - **W realizacji** - zgłoszenie aktualnie przetwarzane
  - **Oczekujący** - zgłoszenie oczekujące na podjęcie działań
  - **Wstrzymany** - zgłoszenie tymczasowo wstrzymane
  - **Zakończony** - zgłoszenie pomyślnie zakończone
  - **Odwołany** - zgłoszenie anulowane
- **Lista zgłoszeń** - przeglądanie wszystkich zgłoszeń z informacjami o numerze rejestracyjnym, statusie oraz datach utworzenia i aktualizacji

## Bezpieczeństwo

- Hasła użytkowników są automatycznie hashowane przy użyciu bcrypt przed zapisem do bazy danych
- Kontrola dostępu oparta na rolach użytkowników
- Panel administracyjny dostępny tylko dla autoryzowanych użytkowników

## Technologie

- Laravel
- Filament (Panel administracyjny)
- MySQL


