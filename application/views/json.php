<?php

	//pass in parameter, either single word or array. If array, loop through
	$apiKey = '1766cba83f05e0a627fbe111ab8ae039';
	$baseUrl = 'https://api.pearson.com/longman/dictionary/0.1/';
	$dataFmt = '.json';
	$searchUrl = $baseUrl . 'entry' . $dataFmt;
	$searchUrl .= '?apikey=' . $apiKey . '&q=' . 'cat';
	
	// Start session (also wipes existing/previous sessions)
	$this->curl->create($searchUrl);
	// Options
	$this->curl->options(array(CURLOPT_BUFFERSIZE => 10, CURLOPT_SSL_VERIFYPEER => FALSE));
	
	// Execute - returns responce
	$json = $this->curl->execute();
	$entries = json_decode($json, true);
	echo process_entries ($entries['Entries']['Entry']);
	//json object returls Entries->Entries - if only one entry it returns head, body etc. Otherwise it returns {},{},{}

	function process_entries ($entry) {
	
		$html = '';
		$pron_gb;
		$pron_us;
		$sound_effects;
		$dvd_pic;
		$baseUrl = 'https://api.pearson.com/longman/dictionary/0.1';
		
		//if the value is an array, it means it is a definition. if it isnt, need to break the loop
		if (isset($entry['@id'])) {
			//get multimedia
			if (isset($entry['multimedia']) && is_array($entry['multimedia'])) {
				foreach ($entry['multimedia'] as $media) {
				
					if ($media['@type'] == 'US_PRON') {
						$pron_us = '<audio preload="none" controls>
							<source src="' . $baseUrl . $media['@href'] . '" type="audtio/mpeg" />
							<a href="' . $baseUrl . $media['@href'] . '?apikey=' . $apiKey . '">US Pron</a>
						</audio>';
					} else if ($media['@type'] == 'GB_PRON') {
						$pron_gb = '<audio preload="none" controls>
							<source src="' . $baseUrl . $media['@href'] . '" type="audtio/mpeg" />
							<a href="' . $baseUrl . $media['@href'] . '?apikey=' . $apiKey . '">GB Pron</a>
						</audio>';
					} else if ($media['@type'] == 'SOUND_EFFECTS') {
						$sound_effects = '<audio preload="none" controls>
							<source src="' . $baseUrl . $media['@href'] . '" type="audtio/mpeg" />
							<a href="' . $baseUrl . $media['@href'] . '?apikey=' . $apiKey . '">Sound effect</a>
						</audio>';
					} else if ($media['@type'] == 'DVD_PICTURES') {
						$dvd_pic = '<img src="' . $baseUrl . $media['@href'] . '?apikey=' . $apiKey . '" />';
					}
				}
			}
			
			if (isset($pron_gb)) {
				$html .= $pron_gb;
			}
			
			if (isset($pron_us)) {
				$html .= $pron_us;
			}
			
			if (isset($sound_effects)) {
				$html .= $sound_effects;
			}
			
			if (isset($dvd_pic)) {
				$html .= $dvd_pic;
			}
			
			//debugLog.append("Processing Entry: " + from.Head.HWD['#text'] + '<br/>');
			$html .= '<li><a>';		
			$html .= $entry['Head']['HWD']['#text'];
			
			if (isset($entry['Head']['PronCodes']['PRON']['#text']))
				$html .= '<span class="pron">' . $entry['Head']['PronCodes']['PRON']['#text'] . '</span>';
			$html .= '<span>(' . $entry['Head']['POS']['#text'] . ')</span>';
			$html .= '</a><div>';

			//var attr = $(from).attr('multimedia');
			//if () {
			//	$html .= multimedia(from.multimedia);
			//}
			$html .= '<ol id="sense">';					
			$html .= get_sense($entry['Sense']);
			$html .= '</ol>';
			$html .= '</div></li>';	
		} else {
			foreach ($entry as $entry) {
				$html .= process_entries ($entry);
			}
		}
		return $html;
	}
	
	function get_sense ($entry) {
		$html = '';
		for ($i = 0; $i < sizeof($entry); $i++) {
			if (isset($entry[$i]['DEF']))
				$html .= '<li>' . $entry[$i]['DEF']['#text'];
				if (isset($entry[$i]['EXAMPLE'])) {
					$html .= '<ul>';
					//$html .= '<li>' . $entry[$i]['DEF']['#text'];
					if (! isset($entry[$i]['EXAMPLE']['@id'])) {
						foreach ($entry[$i]['EXAMPLE'] as $example) {
							if (! is_array($example['#text']))
								$html .= '<li>' . $example['#text'] . '</li>';
						}
					} else {
						if (! is_array($entry[$i]['EXAMPLE']['#text']))
							$html .= '<li>' . $entry[$i]['EXAMPLE']['#text'] . '</li>';
					}
					$html .= '</ul>';
				}
					
				$html .= '</li>';
		}
		return $html;
	}				
?>