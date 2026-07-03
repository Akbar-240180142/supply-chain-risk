<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('cca2', 2)->nullable();
            $table->string('cca3', 3)->nullable();
            $table->string('capital')->nullable();
            $table->string('region')->nullable();
            $table->string('currency_code')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });

        Schema::create('weather_cache', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->decimal('temperature', 5, 2)->nullable();
            $table->decimal('rain', 5, 2)->nullable();
            $table->decimal('wind_speed', 5, 2)->nullable();
            $table->boolean('is_storm')->default(false);
            $table->timestamp('fetched_at');
            $table->timestamps();
        });

        Schema::create('economic_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->year('year');
            $table->bigInteger('gdp')->nullable();
            $table->decimal('inflation_rate', 5, 2)->nullable();
            $table->bigInteger('population')->nullable();
            $table->bigInteger('exports')->nullable();
            $table->bigInteger('imports')->nullable();
            $table->timestamps();
            $table->unique(['country_id', 'year']);
        });

        Schema::create('risk_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->date('record_date');
            $table->decimal('weather_risk', 5, 2)->default(0);
            $table->decimal('inflation_risk', 5, 2)->default(0);
            $table->decimal('currency_risk', 5, 2)->default(0);
            $table->decimal('news_risk', 5, 2)->default(0);
            $table->decimal('total_risk_score', 5, 2);
            $table->enum('risk_level', ['Low', 'Medium', 'High', 'Critical']);
            $table->timestamps();
        });

        Schema::create('news_cache', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('url')->unique();
            $table->string('source')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->enum('sentiment', ['Positive', 'Neutral', 'Negative'])->nullable();
            $table->decimal('sentiment_score', 5, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('currency_rates', function (Blueprint $table) {
            $table->id();
            $table->string('base_currency', 3)->default('USD');
            $table->string('target_currency', 3);
            $table->decimal('rate', 15, 6);
            $table->date('record_date');
            $table->timestamps();
            $table->unique(['base_currency', 'target_currency', 'record_date']);
        });

        Schema::create('positive_words', function (Blueprint $table) {
            $table->id();
            $table->string('word')->unique();
            $table->integer('weight')->default(1);
            $table->timestamps();
        });

        Schema::create('negative_words', function (Blueprint $table) {
            $table->id();
            $table->string('word')->unique();
            $table->integer('weight')->default(1);
            $table->timestamps();
        });

        Schema::create('ports', function (Blueprint $table) {
            $table->id();
            $table->string('port_name');
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->string('country_name');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('harbor_size')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('watchlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id', 'country_id']);
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('articles');
        Schema::dropIfExists('watchlists');
        Schema::dropIfExists('ports');
        Schema::dropIfExists('negative_words');
        Schema::dropIfExists('positive_words');
        Schema::dropIfExists('currency_rates');
        Schema::dropIfExists('news_cache');
        Schema::dropIfExists('risk_scores');
        Schema::dropIfExists('economic_indicators');
        Schema::dropIfExists('weather_cache');
        Schema::dropIfExists('countries');
    }
};