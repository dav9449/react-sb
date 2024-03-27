<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvents extends Migration
{
    //ALREADY ADDED NOT FULLY IMPLEMENTED DUE TO LACK OF RESOURCES AND MODELS OF INPUTS:
    // 'date', 
    // 'Rev', 
    // 'DC', 
    // 'C/I(L)', 
    // 'C/I(Z)', 
    // 'C/O(L)', 
    // 'C/O(Z)', 
    // 'Activity', 
    // 'Remark', 
    // 'From', 
    // 'STD(L)', 
    // 'STD(Z)', 
    // 'To', 
    // 'STA(L)', 
    // 'STA(Z)', 
    // 'AC/Hotel', 
    // 'BLH', 
    // 'Flight_Time', 
    // 'Night_Time', 
    // 'Dur', 
    // 'Ext', 
    // 'Pax_booked<', 
    // 'ACReg', 
    // 'CrewMeal', 
    // 'Resources', 
    // 'CC', 
    // 'Name', 
    // 'Pos', 
    // 'Work_Phone', 
    // 'DH_Crew', 
    // 'DH_Name', 
    // 'DH_Seat', 
    // 'Remark', 
    // 'Fdp_Time', 
    // 'Max_Fdp', 
    // 'Rest_Compl', 
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable(true);
            $table->string('rev')->nullable(true);
            $table->string('dc')->nullable(true);
            $table->decimal('checkinlt',4,0)->nullable(true);
            $table->decimal('checkinutc',4,0)->nullable(true);
            $table->decimal('checkoutlt',4,0)->nullable(true);
            $table->decimal('checkoututc',4,0)->nullable(true);
            $table->string('Activity',7)->nullable(true);
            $table->string('From',3)->nullable(true);
            $table->decimal('stdlt',10,0)->nullable(true);
            $table->decimal('stdutc',10,0)->nullable(true);
            $table->string('To',3)->nullable(true);
            $table->decimal('stalt',10,0)->nullable(true);
            $table->decimal('stautc',10,0)->nullable(true);
            $table->string('AC_Hotel',3)->nullable(true);
            $table->string('BLH',3)->nullable(true);
            $table->string('Flight_Time',3)->nullable(true);
            $table->string('Night_Time',3)->nullable(true);
            $table->string('Dur',3)->nullable(true);
            $table->string('Ext',3)->nullable(true);
            $table->string('Pax_booked',3)->nullable(true);
            $table->string('ACReg',10)->nullable(true);
            $table->string('CrewMeal',10)->nullable(true);
            $table->string('Resources',10)->nullable(true);
            $table->string('CC',10)->nullable(true);
            $table->string('Name',10)->nullable(true);
            $table->string('Pos',10)->nullable(true);
            $table->string('Work_Phone',20)->nullable(true);
            $table->string('DH_Crew',3)->nullable(true);
            $table->string('DH_Name')->nullable(true);
            $table->integer('DH_Seat')->nullable(true);
            $table->string('Remark')->nullable(true);
            $table->string('Fdp_Time')->nullable(true);
            $table->string('Max_Fdp')->nullable(true);
            $table->string('Rest_Compl')->nullable(true);
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
        Schema::dropIfExists('events');
    }
}
