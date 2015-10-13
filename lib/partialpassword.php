<?php

/**
 * implements partial passwords with Shamirs' Secret Sharing Scheme.
 *
 * see http://www.smartarchitects.co.uk/news/9/15/Partial-Passwords---How.html
 * see https://en.wikipedia.org/wiki/Shamir%27s_Secret_Sharing
 * see https://en.wikipedia.org/wiki/Lagrange_polynomial
 *
 * @author     Timotheus Pokorra <tp@tbits.net>
 * @copyright  2015 TBits.net GmbH
 * @license    https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 */

class PartialPasswordWithShamir
{
	private $numberOfTestCharacters;
	private $hashedK; // 32 bit integer
	private $s; // array of 32 bit integers
	private $requestedIndexes; // array if indexes where we want the password characters

	public function toJson() {
		$array = array(
				'hashedK' => $this->hashedK,
				's' => $this->s,
				'numberOfTestCharacters' => $this->numberOfTestCharacters);
		if (!empty($this->requestedIndexes)) {
			$array['requestedIndexes'] = $this->requestedIndexes;
		}
		return json_encode($array);
	}

	public function fromJson($s) {
		$value = json_decode($s, true);
		$this->numberOfTestCharacters = $value['numberOfTestCharacters'];
		$this->hashedK = $value['hashedK'];
		$this->s = $value['s'];
		if (in_array('requestedIndexes', array_keys($value))) {
			$this->requestedIndexes = $value['requestedIndexes'];
		}
	}

	public function restorePasswordParameters($numberOfTestCharacters, $hashedK, $s) {
		$this->numberOfTestCharacters = $numberOfTestCharacters;
		$this->hashedK = $hashedK;
		$this->s = $s;
	}

	public function getNumberOfTestCharacters() {
		return $this->numberOfTestCharacters;
	}

	public function initPassword($password, $numberOfTestCharacters) {
		$N = $numberOfTestCharacters;
		$K = mt_rand(0, 4294967296 - 1);
		$R = array();
		for ($count = 1; $count < $N; $count++) {
			$R[$count] = mt_rand(0, 4294967296 - 1);
		}

		$k = strlen($password);
		$y = array();
		$s = array();
		for ($x = 1; $x <= $k; $x++) {
			$y[$x] = $K;
			$y[$x] += $R[1]*$x;
			for ($count = 2; $count < $N; $count++) {
				$y[$x] += $R[$count]*pow($x, $count);
			}
			$s[$x] = $y[$x] - ord($password[$x-1]);
		}

		$this->numberOfTestCharacters = $N;
		$this->hashedK = hash("sha256", $K);
		$this->s = $s;
	}

	function createQuestion() {
		$this->requestedIndexes = array();
		$startIndex = 0;
		for ($count = 1; $count <= $this->numberOfTestCharacters; $count++) {
			$startIndex = $this->requestedIndexes[] = mt_rand($startIndex+1, count($this->s) - $this->numberOfTestCharacters + $count);
		}
		return $this->requestedIndexes;
	}

	/// returns true if the answer to the question matches the information about the password
	function answerQuestion($letters) {
		if (count($letters) <> count($this->requestedIndexes)) {
			return false;
		}
		$pairs = array();
		for ($count = 0; $count < count($this->requestedIndexes); $count++) {
			$pairs[$this->requestedIndexes[$count]] = $letters[$count];
		}
		return $this->testPassword($pairs);
	}

        /// returns true if the characters match the password
	function testPassword($pairs) {
		$sum = 0.0;

		foreach ($pairs as $i => $letter) {
			// product of all but $i
			$temp1 = 1.0;
			foreach (array_keys($pairs) as $j) {
				if ($i != $j) {
					$temp1 *= (-1.0)*$j;
				}
			}
			$temp2 = 1.0;
			foreach (array_keys($pairs) as $j) {
				if ($i != $j) {
					$temp2 *= $i - $j;
				}
			}
			$sum += ($temp1 / $temp2) * ($this->s[$i]+ord($letter));
		}

		if (hash("sha256", $sum) == $this->hashedK) {
			return true;
		} else {
			return false;
		}
	}
}

?>
