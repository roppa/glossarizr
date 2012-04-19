<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
	
*/

if (! function_exists('word_list'))
{
    function word_list ($words)
    {
		//process list of words
		$words = trim(strtolower($words));
		$cleanWordString = preg_replace("[^A-Za-z0-9]", " ", $words);
		
		//convert processed string to array
		$wordsArray = explode(" ", $cleanWordString);
		
		//get rid of duplicates and sort alphabetically
		$wordsArraySorted = array_unique($wordsArray);
		asort($wordsArraySorted);
	
		//get rid of any extraneous content
		foreach($wordsArraySorted as $key => $value)
		{
			if($value == "" || is_null($value))
				unset($wordsArraySorted[$key]);
		}
	
		$wordList = array_values($wordsArraySorted);
		return $wordList;
    }   
}