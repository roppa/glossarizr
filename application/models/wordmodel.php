<?php 
class Wordmodel extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

	function process_entries ($entry)
	{
		$html = '';
		$pron_gb;
		$pron_us;
		$sound_effects;
		$dvd_pic;
		$baseUrl = 'https://api.pearson.com/longman/dictionary/0.1';
		$apiKey = '1766cba83f05e0a627fbe111ab8ae039';
		
/*		
<li>
	<dl>
		<dt data-symbol="the" class="word">the</dt>
		<dd class="meta"><b class="descriptor">noun</b> </dd>
		<dd>Used when referring to a specific person, thing, group, time, etc.
			<div class="examples">
				<h1>Examples</h1>
				<ul>
					<li><b>the</b> large ball</li>
					<li><b>the</b> children</li>
					<li><b>the</b> king</li>
				</ul>
			</div>
		</dd>
		<dd class="derivation"><b>Origin</b>: before 900; Middle English, Old English.</dd>
	</dl>
</li>
*/

		//if the value is an array, it means it is a definition. if it isnt, need to break the loop
		if (isset($entry['@id'])) {
		
			//get multimedia
			if (isset($entry['multimedia']) && is_array($entry['multimedia'])) {
				foreach ($entry['multimedia'] as $media) {
				
					if ($media['@type'] == 'US_PRON') {
						$pron_us = '<dd>US Pronunciation: <audio preload="none" controls>
							<source src="' . $baseUrl . $media['@href'] . '" type="audtio/mpeg" />
							<a href="' . $baseUrl . $media['@href'] . '?apikey=' . $apiKey . '">US Pron</a>
						</audio></dd>';
					} else if ($media['@type'] == 'GB_PRON') {
						$pron_gb = '<dd>GB Pronunciation: <audio preload="none" controls>
							<source src="' . $baseUrl . $media['@href'] . '" type="audtio/mpeg" />
							<a href="' . $baseUrl . $media['@href'] . '?apikey=' . $apiKey . '">GB Pron</a>
						</audio></dd>';
					} else if ($media['@type'] == 'SOUND_EFFECTS') {
						$sound_effects = '<dd>Sound effect: <audio preload="none" controls>
							<source src="' . $baseUrl . $media['@href'] . '" type="audtio/mpeg" />
							<a href="' . $baseUrl . $media['@href'] . '?apikey=' . $apiKey . '">Sound effect</a>
						</audio></dd>';
					} else if ($media['@type'] == 'DVD_PICTURES') {
						$dvd_pic = '<dd>' . $entry['Head']['HWD']['#text'] . ' image: <img src="' . $baseUrl . $media['@href'] . '?apikey=' . $apiKey . '" /></dd>';
					}
				}
			}
			
			$html .= '<li' . ' id="' . $entry['Head']['HWD']['#text'] . '"' . '><dl>';
			$html .= '<dt data-symbol="' . $entry['Head']['HWD']['#text'] . '" class="word">' . $entry['Head']['HWD']['#text'];
			if (isset($entry['Head']['PronCodes']['PRON']['#text']))
				$html .= '<span class="pron">' . $entry['Head']['PronCodes']['PRON']['#text'] . '</span>';
			$html .= '</dt>';
				
			if (isset($entry['Head']['POS']['#text']))
				$html .= '<dd class="meta"><b class="descriptor">' . $entry['Head']['POS']['#text'] . '</b></dd>';

			if (isset($pron_gb)) 
				$html .= $pron_gb;
			
			if (isset($pron_us))
				$html .= $pron_us;
			
			if (isset($sound_effects))
				$html .= $sound_effects;
			
			if (isset($dvd_pic))
				$html .= $dvd_pic;
				
			//var attr = $(from).attr('multimedia');
			//if () {
			//	$html .= multimedia(from.multimedia);
			//}
			//$html .= '<dd>';					
			$html .= $this->get_sense($entry['Sense']);
			//$html .= '</dd>';
			
			$html .= '</dl></li>';	

		} else {
			foreach ($entry as $entry) {
				$html .= $this->process_entries ($entry);
			}
		}
		return $html;

	}
	
	function get_sense ($entry) {
		$html = '';
		for ($i = 0; $i < sizeof($entry); $i++) {
			if (isset($entry[$i]['DEF']))
				$html .= '<dd>' . $entry[$i]['DEF']['#text'];
				if (isset($entry[$i]['EXAMPLE'])) {
					$html .= '<div class="examples">';
					$html .= '<h1>Examples</h1>';
					$html .= '<ul>';
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
					$html .= '</div>';
				}
					
				$html .= '</dd>';
		}
		return $html;
	}				

}

?>