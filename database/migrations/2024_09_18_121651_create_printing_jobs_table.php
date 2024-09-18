<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreatePrintingJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('printing_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('detail')->nullable();
            $table->string('origin')->nullable();
            $table->string('printer_name')->nullable();
            $table->string('printer_ip');
            $table->longText('header');
            $table->longText('lines');
            $table->longText('file')->nullable();
            $table->string('sent_by')->nullable();
            $table->longText('log')->nullable();
            $table->enum('status',['Completado','Pendiente','Eliminado'])->default('Pendiente');
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
        Schema::drop('printing_jobs');
    }
}
