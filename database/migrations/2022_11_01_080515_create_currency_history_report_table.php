<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrencyHistoryReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currency_history_report', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('range', 45);
            $table->string('interval', 45)->nullable();
            $table->date('startDate')->nullable();
            $table->date('endDate')->nullable();
            $table->string('source', 3);
            $table->string('currency', 3);
            $table->json('report')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currency_history_report');
    }
}
