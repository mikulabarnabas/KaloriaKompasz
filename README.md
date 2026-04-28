
# Kalória Kompasz

Egy modern Laravel + Vue.js alapú kalória- és edzéskövető alkalmazás, teljes Docker környezetbe csomagolva az egyszerű hordozhatóság érdekében.


## Előfeltételek

A futtatáshoz csak a következő szoftverre van szükséged:
* **Docker Desktop** (Windows esetén győződj meg róla, hogy a WSL2 alapú motor engedélyezve van a beállításokban)
## Indítás

Kövesd az alábbi lépéseket a fejlesztői környezet elindításához a projekt mappájában:

### 1. Környezeti változók beállítása
A Docker indításához és a Laravel konfigurációjához szükség van az `.env` fájlra. Másold le az `.env.example` fájlt az alábbi paranccsal:

**Windows (PowerShell vagy CMD):**
Először el kell indíani a dockert
```powershell
docker compose up --build
```
Ez kifejezetten sok ideig is eltarthat (akár 10-15 percig is), mivel minden függőséget telepít, feltölti az adatbázist, stb... 
További indításoknál elegendő a
```powershell
docker compose up
```
parancsot futtatni

Ezután a weboldal elérhető a http://localhost:8000 oldalon.
