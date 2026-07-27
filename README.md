# Supply Chain Risk Intelligence Platform

Sistem Analitik dan Pemantauan Risiko Rantai Pasok Global Terpadu. Platform analitik berbasis web yang dirancang untuk memetakan dan memonitor berbagai potensi risiko dalam jaringan rantai pasok global.

**Live Demo (Production):** [https://supply-chain-risk.onrender.com/](https://supply-chain-risk.onrender.com/)

---

## 🚀 Fitur Utama

- **Dashboard Monitoring Global**: Menampilkan indikator penting dari ratusan negara, termasuk nilai tukar mata uang, kondisi cuaca, dan makroekonomi (GDP, Inflasi).
- **Kalkulator Skor Risiko Terpadu (Risk Scoring)**: Membobotkan risiko cuaca, inflasi, nilai tukar, dan politik/berita menjadi skor bahaya (Rendah hingga Kritis).
- **Peta Cuaca Geospasial**: Visualisasi cuaca dunia dan peringatan dini berbasis koordinat geografis.
- **Analisis Sentimen Berita**: Mengekstrak sentimen (positif/netral/negatif) terkait berita logistik dan politik antarnegara.
- **Direktori Pelabuhan**: Penelusuran data ekstensif lebih dari puluhan ribu pelabuhan di seluruh dunia.
- **Modul Komparasi Negara**: Membandingkan profil ekonomi dan risiko antar negara secara *head-to-head*.
- **Watchlist Pribadi**: Memantau portofolio negara-negara tertentu.
- **Manajemen Shipment & Tracking**: Estimasi pengiriman dan pelacakan status logistik.

## 🛠️ Teknologi yang Digunakan

**Backend**
- Laravel 12.x (PHP 8.2+)
- MySQL (Database)

**Frontend**
- Bootstrap 5.3
- Vanilla JS & jQuery 3.6
- Chart.js (Visualisasi Grafik)
- Leaflet.js (Pemetaan Peta Dunia)

**API Eksternal Terintegrasi**
- Open-Meteo API (Cuaca)
- World Bank API (Data Ekonomi)
- REST Countries API (Data Demografi)
- ExchangeRate API (Kurs Mata Uang)
- GNews API (Intelijen Berita)
- UN-LOCODE / Marine Traffic (Data Pelabuhan)

---

## ⚙️ Persyaratan Sistem (Local Development)

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL >= 5.7

## 📦 Instalasi & Menjalankan Proyek

1. **Clone repositori**
   ```bash
   git clone <url-repository>
   cd supply-chain-risk
   ```

2. **Install dependensi PHP & Node.js**
   ```bash
   composer install
   npm install
   ```

3. **Salin file environment**
   ```bash
   cp .env.example .env
   ```

4. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

5. **Konfigurasi Database**
   Buka file `.env` dan atur kredensial database Anda:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database_anda
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Migrasi Database & Seeding**
   ```bash
   php artisan migrate --seed
   ```

7. **Compile Asset Frontend**
   ```bash
   npm run build
   # atau untuk mode development
   npm run dev
   ```

8. **Jalankan Server Lokal**
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses di `http://127.0.0.1:8000`.

---

**Dosen Pengampu:** Muhammad Ikhwani, S.Pd.I., M.Sc.
**Disusun Oleh:** Muhammad Akbar Maulana (240180142)
**Fakultas Teknik - Sistem Informasi | Universitas Malikussaleh (2026)**
