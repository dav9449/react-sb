<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    use HasFactory;
    
    /**
     * @var string $table
     */
    protected $table="events";

     /**
     * @var array $fillable
     */
    protected $fillable = [
        'id',
        'date', 
        'Rev', 
        'DC', 
        'checkinlt', 
        'checkinutc', 
        'checkoutlt', 
        'checkoututc', 
        'Activity', 
        'Remark', 
        'From', 
        'stdlt', 
        'stdutc', 
        'To', 
        'stalt', 
        'stautc', 
        'AC_Hotel', 
        'BLH', 
        'Flight_Time', 
        'Night_Time', 
        'Dur', 
        'Ext', 
        'Pax_booked<', 
        'ACReg', 
        'CrewMeal', 
        'Resources', 
        'CC', 
        'Name', 
        'Pos', 
        'Work_Phone', 
        'DH_Crew', 
        'DH_Name', 
        'DH_Seat', 
        'Remark', 
        'Fdp_Time', 
        'Max_Fdp', 
        'Rest_Compl', 
    ];
}
