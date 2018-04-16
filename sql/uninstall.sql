
SELECT @domainID := MIN(id) FROM civicrm_domain;
DELETE FROM civicrm_dashboard WHERE name = 'calendar' AND domain_id = @domainID;
