<?php

/**
 * testing the partial password implementation.
 *
 * @author     Timotheus Pokorra <tp@tbits.net>
 * @copyright  2015 TBits.net GmbH
 * @license    https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 */

require_once('../lib/partialpassword.php');

// a test 
$myPassAuth = new PartialPasswordWithShamir(); 
$password = "topsecret";
$myPassAuth->initPassword($password, 2);
$requestedIndexes = $myPassAuth->createQuestion();
print_r($myPassAuth);
$answer = array();
foreach ($requestedIndexes as $index) {
        $answer[] = $password[$index-1];
}
echo ($myPassAuth->answerQuestion($answer)?"true":"false")."\n";

echo ($myPassAuth->testPassword ( array(3 => 'p', 6 => 'c', 7 => 'r'))?"true":"false")."\n";
echo ($myPassAuth->testPassword ( array(3 => 'p', 6 => 'c'))?"true":"false")."\n";
echo ($myPassAuth->testPassword ( array(1 => 't', 4 => 's'))?"true":"false")."\n";
echo ($myPassAuth->testPassword ( array(1 => 't', 8 => 'e'))?"true":"false")."\n";
echo "should fail: \n";
echo ($myPassAuth->testPassword ( array(3 => 'p', 6 => 'f'))?"true":"false")."\n";
echo ($myPassAuth->testPassword ( array(3 => 'p', 6 => 'd'))?"true":"false")."\n";
echo ($myPassAuth->testPassword ( array(3 => 'p', 7 => 'c'))?"true":"false")."\n";
?>
