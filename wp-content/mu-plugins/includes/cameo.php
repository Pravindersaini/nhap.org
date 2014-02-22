<?php
	// returns Cameo code given a postcode
	function cameoCode($postcode) {
		$cameo = array (
			'1A' => 'opulent couples and singles in executive city and suburban areas',
			'1B' => 'wealthy singles in small city flats and suburban terraces',
			'1C' => 'urban living professional singles and couples',
			'1D' => 'wealthy and educated singles in student areas',
			'2A' => 'opulent older and retired households in spacious rural properties',
			'2B' => 'affluent mature families and couples in large exclusive detached homes',
			'2C' => 'affluent mature couples and singles some with school age children',
			'2D' => 'wealthy suburban professionals in mixed tenure',
			'3A' => 'wealthy older families in spacious suburban and rural detached and semi-detached houses',
			'3B' => 'young and mature couples and families in large rural dwellings',
			'3C' => 'well-off-older couples and families in large detached and semi-detached houses',
			'3D' => 'wealthy mixed households living in rural communities',
			'4A' => 'executive households in suburban terraces and semi-detached houses',
			'4B' => 'professional home owners in detached and semi suburbia',
			'4C' => 'white collar home owners in outer suburbs and coastal areas',
			'4D' => 'mature owner occupiers in rural and coastal neighbourhoods',
			'4E' => 'couples and families in modern rural and suburban developments',
			'4F' => 'mature couples and families in mortgaged detached and semi-detached houses',
			'5A' => 'singles, couples and school age families in mixed housing',
			'5B' => 'young and older single mortgagees and renters in terraces and flats',
			'5C' => 'mature and retired singles in areas of small mixed housing',
			'5D' => 'young and older households in coastal, rural and suburban areas',
			'5E' => 'mature households in scottish industrial suburbs and rural communities',
			'5F' => 'young and older households in areas of mixed tenure',
			'5G' => 'older couples and singles in suburban family semi-detached houses',
			'6A' => 'less affluent communities in areas of mixed tenure',
			'6B' => 'older and mature households in suburban semi-detached houses and terraces',
			'6C' => 'mixed households in mostly welsh suburban communities and rural areas',
			'6D' => 'couples and families with school age and older children in spacious semi-detached houses',
			'6E' => 'mature households in less affluent suburban and rural areas',
			'6F' => 'less affluent couples in suburban family neighbourhoods',
			'6G' => 'young single and family communities in small terraces and rented flats',
			'7A' => 'single mortgagees and renters in pre-school family neighbourhoods',
			'7B' => 'singles and families in ethnically mixed inner city and suburban areas',
			'7C' => 'young flat dwelling singles and couples in inner city student areas',
			'7D' => 'young singles, couples and students in urban areas',
			'7E' => 'young singles in privately rented and housing association properties',
			'8A' => 'poorer retired households in owned and rented accommodation',
			'8B' => 'older and mature households in suburban areas of mixed tenure',
			'8C' => 'older households with school age children in towns and suburbs',
			'8D' => 'poorer young singles in suburban family areas',
			'8E' => 'mixed mortgagees and council tenants in outer suburbs',
			'8F' => 'singles and couples in small terraced properties',
			'9A' => 'poorer singles in outer suburban family neighbourhoods',
			'9B' => 'poorer singles and families in mixed tenure',
			'9C' => 'suburban scottish households in small terraces and flats',
			'9D' => 'ethnically mixed young families and singles in terraced housing',
			'9E' => 'poorer couples and school age families in terraced and semi-detached houses',
			'9F' => 'flat dwellers in council and housing association accommodation',
			'9G' => 'young and older households in housing association and mortgaged homes',
			'10A' => 'hi-rise flat dwellers in cosmopolitan areas of mixed tenure',
			'10B' => 'council tenants and mortgagees in scottish suburbia',
			'10C' => 'poorer mortgagees and council renters in family neighbourhoods',
			'10D' => 'singles and single parents in suburban hi-rise flats',
			'10E' => 'mature households in small terraces and semi-detached houses',
			'10F' => 'poorer singles in local authority family neighbourhoods',
			'10G' => 'single renters in mixed age hi-rise communities',
			'XXX' => 'communal establishments in mixed neighbourhoods'
		);
		
		/*
		* Build the http POST request
		*/
		$postdata = http_build_query(array('postcode' => $postcode));
		$opts = array('http' =>
			array(
				'method'  => 'POST',
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'content' => $postdata
			)
		);
		$context = stream_context_create($opts);
		//$res = file_get_contents('http://www.checkmyarea.com/content/services/neighbourhood/checkpage.asp', false, $context);
		$res = file_get_contents("http://www.checkmyarea.com/" . preg_replace('/\s+/', '', $postcode) . ".htm", false, $context);
		//file_put_contents('/home/national/logs/debug.log', print_r($res, true), FILE_APPEND);
		$result = strip_tags($res);

		/*
		* Look in returned html for a match with a Cameo descriptor
		*/
		foreach ($cameo as $code=>$desc) {
			$cameoCode= 'unknown';
			$regexp = "/$desc/i";
			if(preg_match($regexp, $result)) {
				$cameoCode = $code;
				break;
			}
		}
		return $cameoCode;
	}
?>
