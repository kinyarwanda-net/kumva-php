<?php
/**
 * This file is part of Kumva.
 *
 * Kumva is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Kumva is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kumva.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Copyright Rowan Seymour 2010
 * 
 * Purpose: XML interface to query engine
 */
 
include_once '../inc/kumva.php';

$q = Request::getGetParam('q');
$source = Request::getGetParam('ref', 'xml');
$start = max((int)Request::getGetParam('start', 0), 0);
$limit = max((int)Request::getGetParam('limit', 0), 0);
$asEntries = (bool)Request::getGetParam('entries', false);

header("Content-type: text/xml");

Xml::header();

if ($q != '') {	
	$paging = ($limit > 0) ? new Paging($start, $limit) : null;			
	$search = new Search($q, $paging);
	$search->run($source);
	
	if ($asEntries) {
		// New entry/revision format for Kumva Andriod 1.4+
		echo '<entries query="'.htmlspecialchars($q).'" ';	
		if ($search->getSuggestionPattern())
			echo 'suggestion="'.$search->getSuggestionPattern().'" ';	
		echo '>';	
		foreach ($search->getResults() as $entry)
			Xml::entry($entry, false);			
		echo '</entries>';	
	}
	else {
		// Old format used by Kumva Android <= 1.3 and Nyaruka SMS
		echo '<definitions query="'.htmlspecialchars($q).'" ';	
		if ($search->getSuggestionPattern())
			echo 'suggestion="'.$search->getSuggestionPattern().'" ';	
		echo '>';	
		foreach ($search->getResults() as $entry)
			Xml::revision($entry->getHead(), false, true);			
		echo '</definitions>';	
	}
}

echo Xml::footer();

?>
