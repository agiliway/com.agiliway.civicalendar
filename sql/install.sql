
SELECT @domainID := MIN(id) FROM civicrm_domain;


SELECT @sql := IF ( EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='civicrm_dashboard' AND column_name='cache_minutes' AND TABLE_SCHEMA = database())
			,'REPLACE INTO civicrm_dashboard 
				(domain_id, name, label, url, permission, permission_operator, is_active, fullscreen_url, is_reserved, cache_minutes)
			 VALUES
				(@domainID, ''calendar'', ''Calendar'', ''civicrm/dashlet/calendar?reset=1'', ''access CiviCRM'', NULL, 1, ''civicrm/dashlet/calendar?reset=1&context=dashletFullscreen'', 1, 60);'
			,'REPLACE INTO civicrm_dashboard 
				(domain_id, name, label, url, permission, permission_operator, is_active, fullscreen_url, is_reserved)
			 VALUES
				(@domainID, ''calendar'', ''Calendar'', ''civicrm/dashlet/calendar?reset=1'', ''access CiviCRM'', NULL, 1, ''civicrm/dashlet/calendar?reset=1&context=dashletFullscreen'', 1);');
	
PREPARE dynamic_statement FROM @sql;
EXECUTE dynamic_statement;
DEALLOCATE PREPARE dynamic_statement;
