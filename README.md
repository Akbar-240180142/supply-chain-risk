# 🌍 Global Supply Chain Risk Intelligence

A comprehensive full-stack web application for monitoring and analyzing global supply chain risks using real-time data from multiple international APIs.

## ✨ Features

### 📊 Dashboard Analytics
- **Global Risk Map**: Interactive world map showing risk levels for 20+ countries
- **Risk Score Charts**: Visual representation of country risk scores using Chart.js
- **Currency Exchange Rates**: Real-time currency tracking against USD

### 🗺️ Geospatial Visualization
- **Port Location Dashboard**: Interactive map of 15 major global ports
- **Country Risk Markers**: Color-coded markers (Green/Yellow/Red) based on risk levels

### 🤖 AI & Data Science
- **Sentiment Analysis**: Lexicon-based analysis of news articles (Positive/Negative/Neutral)
- **Risk Scoring Engine**: Weighted algorithm combining weather, economic, news, and currency data
- **News Intelligence**: Real-time news aggregation from Reuters, BBC, Bloomberg

### 🔍 Decision Support
- **Country Comparison**: Side-by-side comparison of any two countries with radar charts
- **Favorite Monitoring List**: Save and monitor specific countries of interest

### 📰 News Categories
- **Logistics**: Shipping costs, port congestion, infrastructure
- **Trade**: Tariffs, trade agreements, economic tensions
- **Shipping**: Container shortages, route disruptions, carrier profits
- **Economy**: GDP growth, inflation, interest rates, sanctions

## ️ Tech Stack

- **Backend**: Laravel 12 (PHP 8.2)
- **Frontend**: Bootstrap 5, Chart.js, Leaflet.js
- **Database**: MySQL
- **APIs Integrated**:
  - Open-Meteo (Weather)
  - World Bank (Economic Data)
  - ExchangeRate API (Currency)
  - GNews API (News)
  - REST Countries (Country Data)

##  Installation

### Prerequisites
- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js (optional)

### Setup Steps

1. **Clone the repository**
```bash
git clone https://github.com/yourusername/supply-chain-risk.git
cd supply-chain-risk