<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEmpty;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertTrue;

class DTRTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    public function getXpath()
    {
        // Store the uploaded file
        $file = "Roster_CrewConnex_test.html";
        $htmlContent = Storage::get("./Roster_CrewConnex_test.html");
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true); // Disable libxml errors
        $dom->loadHTML($htmlContent);
        libxml_clear_errors(); // Clear any previous libxml errors
        // Read the uploaded HTML file
        $xpath = new \DOMXPath($dom);

        return $xpath;
    }
    static public function getPropertyByClassInDocProvider()
    {
        return [
            ['activityTableStyle', null],
        ];
    }
    /**
     * @dataProvider getPropertyByClassInDocProvider
     */
    function testGetPropertyByClassInDoc(String $class, $xpath)
    {
        if ($xpath == null) {
            // Store the uploaded file
            $file = "Roster_CrewConnex_test.html";
            //$htmlContent = file_get_contents("./Roster_CrewConnex_test.html");

            $htmlFilePath = './tests/additional_files/Roster_CrewConnex_test.html';

            // Check if the file exists
            $this->assertFileExists($htmlFilePath);

            $htmlContent = file_get_contents($htmlFilePath);
            $this->assertNotEmpty($htmlContent);
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true); // Disable libxml errors
            $dom->loadHTML($htmlContent);
            libxml_clear_errors(); // Clear any previous libxml errors
            // Read the uploaded HTML file
            $xpath = new \DOMXPath($dom);
            $this->assertNotEmpty($xpath);
        }
        assertTrue($class == 'activityTableStyle');

        $className = $class; // Replace 'classname' with the actual class name
        $elementsByClass = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $className ')]");
        $parsedElementsByClass = [];
        foreach ($elementsByClass as $element) {
            $parsedElementsByClass[] = $element->textContent;
        }
        assertTrue(count($parsedElementsByClass) > 0);
        return $parsedElementsByClass;
    }

    static public function GetPropertyDayWeekPrefixProvider()
    {
        return [
            ["10 Mon"]
        ];
    }
    /**
     * @dataProvider GetPropertyDayWeekPrefixProvider
     */
    function testGetDayWeekPrefix(String $dwpref)
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

        assertNotNull($dwpref);
        return $dwpref;
    }


    /**
     * @dataProvider getPropertyByClassInDocProvider
     */
    public function testGetCurrentYearFromDropdown(String $class, $xpath)
    {
        if($xpath==null){
                // Store the uploaded file
                $file = "Roster_CrewConnex_test.html";
                //$htmlContent = file_get_contents("./Roster_CrewConnex_test.html");

                $htmlFilePath = './tests/additional_files/Roster_CrewConnex_test.html';
                // Check if the file exists
                $this->assertFileExists($htmlFilePath);
    
                $htmlContent = file_get_contents($htmlFilePath);
                $this->assertNotEmpty($htmlContent);
                $dom = new \DOMDocument();
                libxml_use_internal_errors(true); // Disable libxml errors
                $dom->loadHTML($htmlContent);
                libxml_clear_errors(); // Clear any previous libxml errors
                // Read the uploaded HTML file
                $xpath = new \DOMXPath($dom);
                $this->assertNotEmpty($xpath);
        }
        // Specify the ID of the <select> element you want to query
        $selectId = 'ctl00_Main_periodSelect'; // Replace 'your_select_id' with the actual ID of the <select> element

        // Query for the <select> element with the specified ID
        $selectElement = $xpath->query("//*[@id='$selectId']")->item(0);
        assertNotEmpty($selectElement);
        $selectedValue = [];
        // Check if the <select> element was found
        if ($selectElement) {
            // Get the value of the selected option
            $selectedOption = $xpath->query(".//option[@selected]", $selectElement)->item(0);
            assertNotEmpty($selectedOption);
            // Check if a selected option was found
            if ($selectedOption) {
                // Get the value attribute of the selected option
                $selectedValue = $selectedOption->getAttribute('value');
                assertNotEmpty($selectedValue);
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
            assertNotEmpty($rangeSplitted);
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
