<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;

abstract class Airline extends Controller
{
    //constructor
    abstract public function __construct();
    //give all events between date x and y.
    abstract public function getAllEventsByDate(Request $request);
    //give all flights for the next week (current date can be set to 14 Jan 2022)
    abstract public function getAllFlightsForNextWeek(Request $request);
    //give all Standby events for the next week (current date can be set to 14 Jan 2022
    abstract public function setAllStandbyEventsForNextWeek(Request $request);
    //give all flights that start on the given location.
    abstract public function getAllFlightsEventsByGivenLocation(Request $request);
    //ability to upload the roster by giving a file as input.
    abstract public function uploadRoasterFromFile(Request $request);
}
