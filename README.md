# 🌍 Global Supply Chain Risk Intelligence Platform

Platform Monitoring Risiko Rantai Pasok Global Berbasis Multi-API dan Analitik Data.

## 📋 Deskripsi Project

Sistem ini dibangun untuk membantu perusahaan memantau risiko rantai pasok global dengan mengintegrasikan berbagai indikator:
- 🌦️ **Cuaca ekstrem** (hujan, badai, angin kencang)
- 💱 **Fluktuasi nilai tukar mata uang**
- 📰 **Berita geopolitik & ekonomi** dengan sentiment analysis
- 🚢 **Kemacetan pelabuhan**
- 📊 **Inflasi & kondisi ekonomi** negara

## 🚀 Fitur Utama

### 1. Global Country Dashboard
Dashboard utama dengan informasi GDP, inflasi, populasi, mata uang, dan cuaca real-time untuk setiap negara.

### 2. Risk Scoring Engine
Algoritma **Weighted Risk Model** yang menghitung risiko berdasarkan:
- Weather Risk (30%)
- Inflation Risk (20%)
- Political News Risk (40%)
- Currency Risk (10%)

### 3. Global Weather Monitoring
Peta dunia interaktif dengan marker cuaca (hujan, badai, angin kencang) menggunakan **Open-Meteo API**.

### 4. Currency Impact Dashboard
Visualisasi nilai tukar mata uang real-time dengan **Chart.js**.

### 5. News Intelligence
Berita ekonomi & logistik dengan **Lexicon-Based Sentiment Analysis** (Positive/Neutral/Negative).

### 6. Port Location Dashboard
Peta interaktif lokasi pelabuhan dunia dengan fitur search & filter.

### 7. Data Visualization Dashboard
4 grafik tren:
- 📈 GDP Trend
- 📉 Inflation Trend
- 💱 Currency Trend
- 📊 Risk Trend

### 8. Country Comparison Engine
Bandingkan 2 negara side-by-side (GDP, Inflation, Risk, Weather, Currency).

### 9. Favorite Monitoring List
Simpan negara yang dipantau dalam watchlist personal.

### 10. Admin Dashboard
CRUD untuk:
- 👥 User Management
- 📰 News Articles
- 🚢 Port Dataset

## 🛠️ Teknologi

### Backend
- **PHP 8.2** dengan **Laravel Framework**
- **MySQL** Database

### Frontend
- **Bootstrap 5** - UI Framework
- **AJAX & JavaScript ES6** - Interaktivitas
- **Chart.js** - Visualisasi data
- **Leaflet.js** - Peta interaktif

### API Eksternal
- **Open-Meteo API** - Data cuaca global
- **World Bank API** - Data GDP, inflasi, populasi
- **REST Countries API** - Data negara & mata uang
- **ExchangeRate API** - Kurs real-time
- **GNews API** - Berita ekonomi & logistik
- **World Port Index** - Data pelabuhan dunia

## 📦 Instalasi

### Prasyarat
- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL/MariaDB
- Git

### Langkah Instalasi

1. **Clone repository:**
```bash
git clone https://github.com/username/supply-chain-risk.git
cd supply-chain-risk
```

2. **Install dependencies:**
```bash
composer install
npm install
```

3. **Setup environment:**
```bash
copy .env.example .env
php artisan key:generate
```

4. **Konfigurasi database di `.env`:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=supply_chain_risk
DB_USERNAME=root
DB_PASSWORD=

# API Keys (opsional)
GNEWS_API_KEY=your_gnews_api_key
```

5. **Jalankan migration & seeder:**
```bash
php artisan migrate
php artisan db:seed
```

6. **Jalankan server:**
```bash
php artisan serve
```

Akses di: `http://127.0.0.1:8000`

## 🌐 REST API Endpoints

| Endpoint | Method | Deskripsi |
|----------|--------|-----------|
| `/api/countries` | GET | Daftar semua negara |
| `/api/risk` | GET | Data risk score |
| `/api/ports` | GET | Daftar pelabuhan |
| `/api/news` | GET | Berita dengan sentiment |
| `/api/currency` | GET | Kurs mata uang |
| `/api/economic-trends` | GET | Tren GDP & inflasi |
| `/api/dashboard-data` | GET | Data dashboard utama |
| `/api/watchlist` | GET | Watchlist user |

## 📊 Fitur AI / Data Science

### 1. Lexicon-Based Sentiment Analysis
Analisis sentimen berita menggunakan kamus kata positif & negatif:
- **Positive words**: growth, increase, profit, stable, improve
- **Negative words**: war, crisis, inflation, delay, disaster

### 2. Supply Chain Risk Prediction
Algoritma **Weighted Risk Model** untuk prediksi risiko:
```
Total Risk = (Weather × 30%) + (Inflation × 20%) + (News × 40%) + (Currency × 10%)
```

## 🗄️ Database Schema

- `users` - Data pengguna
- `countries` - Data negara
- `risk_scores` - Skor risiko per negara
- `economic_indicators` - Data GDP, inflasi, populasi
- `currency_rates` - Kurs mata uang
- `news_cache` - Cache berita
- `ports` - Data pelabuhan
- `watchlists` - Watchlist user
- `positive_words` - Kamus kata positif
- `negative_words` - Kamus kata negatif

## 📸 Screenshots

### Dashboard Utama
![Dashboard](screenshots/dashboard.png)

### Country Comparison
![Comparison](screenshots/comparison.png)

### Port Locations
![Ports](screenshots/ports.png)

### News Intelligence
![News](screenshots/news.png)

## 👨‍💻 Developer

**[Muhammad Akbar Maulana]**  
Information Systems Student  
📧 Email: Muhammadakbarmaulana@gmail.com  
🔗 LinkedIn:

## 📄 License

Project ini dibuat untuk tugas akhir akademik.

## 🙏 Acknowledgments

- [Open-Meteo API](https://open-meteo.com/)
- [World Bank API](https://data.worldbank.org/)
- [REST Countries API](https://restcountries.com/)
- [GNews API](https://gnews.io/)
- [Leaflet.js](https://leafletjs.com/)
- [Chart.js](https://www.chartjs.org/)

---

**Built with ❤️ using Laravel & Bootstrap**git init
