<?php

namespace App\Http\Controllers\Api;

use App\Models\Events;
use DateTime;
use DateTimeZone;
use DOMXPath;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DTR extends Airline
{


    public $helper_week = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

    private $zuluCurrentTimestamp;
    private $zuluCurrentWeekNumber;
    private $currentDate;
    public function __construct()
    {
        date_default_timezone_set('UTC');
        // Create the date object and set it to January 14, 2024
        $date = strtotime('2022-01-14');
        // Convert the date to Zulu (UTC) timestamp
        $this->zuluCurrentTimestamp = date('c', $date);
    }
    /**
     *
     * @param Request $request
     */
    //give all events between date x and y.
    function getAllEventsByDate(Request $request)
    {
        // Validate request parameters
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
        ]);

        // Retrieve events based on date range
        $events = Events::whereBetween('date', [$request->from, $request->to])->get();

        return response()->json($events);
    }
    //give all flights for the next week (current date can be set to 14 Jan 2022)
    /**
     *
     * @param Request $request
     */
    function getAllFlightsForNextWeek(Request $request)
    {
        $from = $this->zuluCurrentTimestamp;
        $to = $this->zuluCurrentTimestamp;
        // Retrieve events based on date range
        // Define the initial date in UTC
        $date = new DateTime($this->currentDate, new DateTimeZone('UTC'));
        // Modify the date to the next Monday
        $this->currentDate->modify('next monday');
        // Convert the DateTime object to a string in the format 'Y-m-d'
        $nextMonday = $this->currentDate->format('Y-m-d');
        $events = Events::whereBetween('date', [$this->zuluCurrentTimestamp, $nextMonday])->get();

        dd($events);
        return response()->json($events);
    }

    //give all flights that start on the given location.
    /**
     *
     * @param Request $request
     */
    function getAllFlightsEventsByGivenLocation(Request $request)
    {
        // Validate request parameters
        $request->validate([
            'location' => 'required|string',
        ]);

        // Retrieve events based on date range
        $events = Events::where('Activty',"FLT")->where("from",$request->location)->get();

        return response()->json($events);
    }

    //give all Standby events for the next week (current date can be set to 14 Jan 2022)
    /**
     *
     * @param Request $request
     */
    function setAllStandbyEventsForNextWeek(Request $request)
    { 
        $from = $this->zuluCurrentTimestamp;
        $to = $this->zuluCurrentTimestamp;
        // Retrieve events based on date range
        // Define the initial date in UTC
        $date = new DateTime($this->currentDate, new DateTimeZone('UTC'));
        // Modify the date to the next Monday
        $this->currentDate->modify('next monday');
        // Convert the DateTime object to a string in the format 'Y-m-d'
        $nextMonday = $this->currentDate->format('Y-m-d');
        Events::whereBetween('date', [$this->zuluCurrentTimestamp, $nextMonday])->update(['status' => 'SBY']);
        return response()->json(['message' => 'Events by request setAllStandbyEventsForNextWeek updated successfully']);
    }

    //FunctionToUploadAndParseFile
    public function uploadRoasterFromFile(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'file' => 'required|mimes:html,htm|max:2048', // adjust the allowed file types and size as needed
        ]);

        if ($request->file('file')->isValid()) {
            // Store the uploaded file
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads', $fileName);
            $htmlContent = Storage::get($filePath);
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true); // Disable libxml errors
            $dom->loadHTML($htmlContent);
            libxml_clear_errors(); // Clear any previous libxml errors
            // Read the uploaded HTML file

            $xpath = new \DOMXPath($dom);


            // Extract data from the parsed HTML
            $parsedData = [];

            //GET COUNT DATA FROM HTML TABLE ROWS ACTIVITIES
            $countedRows = $this->getCountActivityTableStyle($xpath);

            $getCurrentYearParsed = $this->getCurrentYearFromDropdown($xpath);
            $dateRange = $this->getRangeDateFromDropdown($xpath);
            if ($countedRows == 0) {
                return response()->json(['error' => 'Nothing to do'], 400);
            }
            //For each counted row iterate
            for ($i = 0; $i < $countedRows; $i++) {
            }
            // Extract data based on specific classes
            $responseActivities = [];
            $classNames = [
                'activitytablerow-date',
                'activitytablerow-revision',
                'activitytablerow-dc',
                'activitytablerow-checkinlt',
                'activitytablerow-checkinutc',
                'activitytablerow-checkoutlt',
                'activitytablerow-checkoututc',
                'activitytablerow-activity',
                'activitytablerow-activityRemark',
                'activitytablerow-fromstn',
                'activitytablerow-stdlt',
                'activitytablerow-stdutc',
                'activitytablerow-tostn',
                'activitytablerow-stalt',
                'activitytablerow-stautc',
                'activitytablerow-AC/Hotel',
                'activitytablerow-blockhours',
                'activitytablerow-flighttime',
                'activitytablerow-nighttime',
                'activitytablerow-duration',
                'activitytablerow-counter1',
                'activitytablerow-Paxbooked',
                'activitytablerow-Tailnumber',
                'activitytablerow-CrewMeal',
                'activitytablerow-Resources',
                'activitytablerow-crewcodelist',
                'activitytablerow-fullnamelist',
                'activitytablerow-positionlist',
                'activitytablerow-BusinessPhoneList',
                'activitytablerow-OtherDHCrewCode',
                'activitytablerow-DHFullNameList',
                'activitytablerow-DHSeatingList',
                'activitytablerow-remarks',
                'activitytablerow-ActualFdpTime',
                'activitytablerow-MaxFdpTime',
                'activitytablerow-RestCompletedTime',
            ]; // Replace 'classname' with the actual class name
            $iterationCounter = 0;
            //GET ALL VAIRABLES FROM ROWS TO ARRAY OF ARRAYS
            foreach ($classNames as $className) {
                $iterationCounter++;
                $responseActivities[$iterationCounter] = $this->getPropertyByClassInDoc($className, $xpath);
            }
            //ITERATE OVER PARSED ARRAYS UNTIL LOWER THAN NUMER OF ROWS IN TABLE

            $rows = [];
            $i = 0;
            $j = 0;
            if (count($responseActivities) != $countedRows) {
                return response()->json(['error' => "Error when parsing file countedRows!=parsedRows $countedRows" . count($responseActivities) . ""], 400);
            } else {
                $numberOfElements = count($responseActivities[1]);


                $allParsedElements = [];
                $rowData = [];
                for ($j = 0; $j < $countedRows; $j++) {
                    $rowData = [];
                    for ($i = 1; $i < $numberOfElements; $i++) {
                        $rowData[] = $responseActivities[$i][$j];
                    }
                    $allParsedElements[] = $rowData;
                }
            }
            $withoutHeaderParsed = $allParsedElements;
            array_shift($withoutHeaderParsed);
            $this->pushToDb($withoutHeaderParsed, $getCurrentYearParsed, $dateRange);
            // Return parsed data or do further processing
            return response()->json(['message' => 'File uploaded and parsed successfully', 'data' => $withoutHeaderParsed]);
        } else {
            // Return error response if the file is not valid
            return response()->json(['error' => 'Invalid file'], 400);
        }
    }
    /**
     *
     * @param Request $request
     * @return array
     */
    function getPropertyByClassInDoc(String $class, $xpath)
    {
        $className = $class; // Replace 'classname' with the actual class name
        $elementsByClass = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $className ')]");
        $parsedElementsByClass = [];
        foreach ($elementsByClass as $element) {
            $parsedElementsByClass[] = $element->textContent;
        }
        return $parsedElementsByClass;
    }
    function getCountActivityTableStyle($xpath, String $class = 'activityTableStyle')
    {
        // Specify the class name you want to query
        $className = $class; // Replace 'classname' with the actual class name

        // Query for elements with the specified class name
        $elementsByClass = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $className ')]");

        // Initialize a counter for total <tr> elements
        $totalTrCount = 0;

        // Iterate over the matched elements
        foreach ($elementsByClass as $classElement) {
            // Count the number of <tr> elements within each matched element
            $trCount = $xpath->query(".//tr", $classElement)->length;

            // Increment the total count by the number of <tr> elements found
            $totalTrCount += $trCount;
        }

        // Output the total count of <tr> elements
        return $totalTrCount;
    }
    private function getDayWeekPrefix($dwpref)
    {
        if (strpos($dwpref, "Mon") !== false) {
            $dwpref = 'Mon';
        } else if (strpos($dwpref, "Tue") !== false) {
            $dwpref = 'Tue';
        } else if (strpos($dwpref, "Wed") !== false) {
            $dwpref = 'Wed';
        } else if (strpos($dwpref, "Thu") !== false) {
            $dwpref = 'Thu';
        } else if (strpos($dwpref, "Fri") !== false) {
            $dwpref = 'Fri';
        } else if (strpos($dwpref, "Sat") !== false) {
            $dwpref = 'Sat';
        } else if (strpos($dwpref, "Sun") !== false) {
            $dwpref = 'Sun';
        }
        return $dwpref;
    }
    private function dayOfWeek(String $stringDayFormat)
    {
        switch ($stringDayFormat) {
            case "Mon":
                return 0;
            case "Tue":
                return 1;
            case "Wed":
                return 2;
            case "Thu":
                return 3;
            case "Fri":
                return 4;
            case "Sat":
                return 5;
            case "Sun":
                return 6;
        }
    }
    private function pushToDb($allPostParsedRows, $currentYearParsed, $dateRange)
    {
        $wasMonday = false;
        $wasMondayTwice = false;
        $dataSplittedParts = explode('-', $dateRange[0]);

        $currentYear = $dataSplittedParts[0];
        $currentYear = $dataSplittedParts[0];
        $currentDayOfWeek = $dataSplittedParts[0];
        foreach ($allPostParsedRows as $row) {
            $modelOfEvent = new \App\Models\Events;
            $stringDayDate = trim($row[0], "\n");
            if (strpos($stringDayDate, "Mon") !== false && ($wasMonday == true)) {
                $wasMondayTwice = true;
                $currentYear = str_split($dateRange[1], 4);
            }
            if (strpos($stringDayDate, "Mon") !== false) {
                $wasMonday = true;
            }
            $date_str = $dateRange[0];
            $new_date = new DateTime($date_str);
            // Set the timezone to UTC
            $new_date->setTimezone(new DateTimeZone('UTC'));;
            if ($this->dayOfWeek($this->getDayWeekPrefix($stringDayDate)) > 0) {
                $new_date->modify('+' . $this->dayOfWeek($this->getDayWeekPrefix($stringDayDate)) . ' days');
            }
            $formatted_date = $new_date->format('Y-m-d');
            $modelOfEvent->date = $formatted_date;
            $modelOfEvent->Rev = trim($row[1], "\n");
            $modelOfEvent->DC = trim($row[2], "\n");
            $modelOfEvent->checkinlt = trim($row[3], "\n");
            $modelOfEvent->checkinutc = trim($row[4], "\n");
            $modelOfEvent->checkoutlt = trim($row[5], "\n");
            $modelOfEvent->checkoututc = trim($row[6], "\n");
            $modelOfEvent->Activity = trim($row[7], "\n");
            $modelOfEvent->Remark = trim($row[8], "\n");
            $modelOfEvent->From = trim($row[9], "\n");
            $modelOfEvent->stdlt = trim($row[10], "\n");
            $modelOfEvent->stdutc = trim($row[11], "\n");
            $modelOfEvent->To = trim($row[12], "\n");
            $modelOfEvent->stalt = trim($row[13], "\n");
            $modelOfEvent->stautc = trim($row[14], "\n");
            $modelOfEvent->AC_Hotel = trim($row[15], "\n");
            $modelOfEvent->BLH = trim($row[16], "\n");
            $modelOfEvent->Flight_Time = trim($row[17], "\n");
            $modelOfEvent->Night_Time = trim($row[18], "\n");
            $modelOfEvent->Dur = trim($row[19], "\n");
            $modelOfEvent->Ext = trim($row[20], "\n");
            $modelOfEvent->Pax_booked = trim($row[21], "\n");
            $modelOfEvent->ACReg = trim($row[22], "\n");
            $modelOfEvent->CrewMeal = trim($row[23], "\n");
            $modelOfEvent->Resources = trim($row[24], "\n");
            $modelOfEvent->CC = trim($row[25], "\n");
            $modelOfEvent->Name = trim($row[26], "\n");
            $modelOfEvent->Pos = trim($row[27], "\n");
            $modelOfEvent->Work_Phone = trim($row[28], "\n");
            $modelOfEvent->DH_Crew = trim($row[29], "\n");
            $modelOfEvent->DH_Name = trim($row[30], "\n");
            $modelOfEvent->DH_Seat = trim($row[31], "\n");
            $modelOfEvent->Remark = trim($row[32], "\n");
            $modelOfEvent->Fdp_Time = trim($row[33], "\n");
            $modelOfEvent->Max_Fdp = trim($row[34], "\n");
            $modelOfEvent->save();
        }
    }
    private function getRangeDateFromDropdown($xpath)
    {
        // Specify the ID of the <select> element you want to query
        $selectId = 'ctl00_Main_periodSelect'; // Replace 'your_select_id' with the actual ID of the <select> element

        // Query for the <select> element with the specified ID
        $selectElement = $xpath->query("//*[@id='$selectId']")->item(0);
        $selectedValue = [];
        // Check if the <select> element was found
        if ($selectElement) {
            // Get the value of the selected option
            $selectedOption = $xpath->query(".//option[@selected]", $selectElement)->item(0);

            // Check if a selected option was found
            if ($selectedOption) {
                // Get the value attribute of the selected option
                $selectedValue = $selectedOption->getAttribute('value');
            } else {
                // No option is selected within the <select> element
                echo "No option selected within <select> element with ID '$selectId'";
            }
        } else {
            // <select> element with the specified ID was not found
            echo "Element with ID '$selectId' not found";
        }
        if (!empty($selectedValue)) {
            $rangeSplitted = explode('|', $selectedValue);
            $range = ["from" => $rangeSplitted[0], "to" => $rangeSplitted[1]];
        } else {
            // Return error response if the file is not valid
            return response()->json(['error' => 'Invalid file - cant load dropdown date range'], 400);
        }
        if (count(str_split($rangeSplitted[0], 4)) == 0) {
            return response()->json(['error' => 'Invalid file - cant load dropdown date range'], 400);
        } else {
            return $rangeSplitted;
        }
    }

    private function getCurrentYearFromDropdown($xpath)
    {
        // Specify the ID of the <select> element you want to query
        $selectId = 'ctl00_Main_periodSelect'; // Replace 'your_select_id' with the actual ID of the <select> element

        // Query for the <select> element with the specified ID
        $selectElement = $xpath->query("//*[@id='$selectId']")->item(0);
        $selectedValue = [];
        // Check if the <select> element was found
        if ($selectElement) {
            // Get the value of the selected option
            $selectedOption = $xpath->query(".//option[@selected]", $selectElement)->item(0);

            // Check if a selected option was found
            if ($selectedOption) {
                // Get the value attribute of the selected option
                $selectedValue = $selectedOption->getAttribute('value');
            } else {
                // No option is selected within the <select> element
                echo "No option selected within <select> element with ID '$selectId'";
            }
        } else {
            // <select> element with the specified ID was not found
            echo "Element with ID '$selectId' not found";
        }
        if (!empty($selectedValue)) {
            $rangeSplitted = explode('|', $selectedValue);
            $range = ["from" => $rangeSplitted[0], "to" => $rangeSplitted[1]];
        } else {
            // Return error response if the file is not valid
            return response()->json(['error' => 'Invalid file - cant load dropdown date range'], 400);
        }
        if (count(str_split($rangeSplitted[0], 4)) == 0) {
            return response()->json(['error' => 'Invalid file - cant load dropdown date range'], 400);
        } else {
            return str_split($rangeSplitted[0], 4)[0];
        }
    }
}
