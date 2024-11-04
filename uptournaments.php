// The function to fetch events and parse mec_fields
add_filter('cs_looper_custom_uptournaments', function ($result) {
    $current_date = date('Ymd');
    global $wpdb;

    // Fetch events that are today or have repeating date equal to today or in the future
    // Make sure to edit the `AND NOT (p.post_title like '%minecraft%' OR p.post_title like 'Smash Summit%')` to remove any unwanted events
    $query = $wpdb->prepare("
        SELECT		
			p.ID, p.post_title, p.post_content, p.post_name,
			d.dstart, d.dend, d.tstart, d.tend,
			FROM_UNIXTIME(d.tstart) AS start_datetime,
			p2.guid AS featured_image,
			MAX(CASE WHEN pm3.meta_key = 'mec_fields' THEN pm3.meta_value END) AS mec_fields
		FROM 
			{$wpdb->posts} AS p
		INNER JOIN 
			{$wpdb->postmeta} AS pm ON p.ID = pm.post_id
		LEFT JOIN 
			{$wpdb->prefix}mec_dates AS d ON p.ID = d.post_id
		LEFT JOIN 
			{$wpdb->postmeta} AS pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_thumbnail_id'
		LEFT JOIN 
			{$wpdb->posts} AS p2 ON pm2.meta_value = p2.ID
		LEFT JOIN 
			{$wpdb->postmeta} AS pm3 ON p.ID = pm3.post_id AND pm3.meta_key LIKE 'mec_fields%'
		WHERE 
			d.dstart >= %s
			AND p.post_type = 'mec-events' 
			AND p.post_status = 'publish'
    		AND NOT (p.post_title like '%minecraft%' OR p.post_title like 'Smash Summit%')	
		GROUP BY 
			d.ID
		ORDER BY 
			d.dstart ASC, d.tstart ASC
		LIMIT 5
    ", $current_date);

    // Run the custom query
    $results = $wpdb->get_results($query, ARRAY_A);

    // Parse the serialized mec_fields data for each result
    // `parse_mec_fields` Refers to another snippet, make sure it is active
    foreach ($results as &$result) {
        if (isset($result['mec_fields'])) {
            $parsed_fields = parse_mec_fields($result['mec_fields']);
            $result = array_merge($result, $parsed_fields);
            unset($result['mec_fields']);
        }
    }

    return $results;
}, 10, 2);
