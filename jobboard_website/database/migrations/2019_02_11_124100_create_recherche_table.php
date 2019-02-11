<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRechercheTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recherche', function (Blueprint $table) {
            $table->increments('idRecherche')->primary();
            
            $table->string('souhait');
            $table->string('dureeStage');
            $table->date('dateDebut');
            $table->date('dateFin');
            $table->string('mobilite');
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
        Schema::dropIfExists('recherche');
    }
}
