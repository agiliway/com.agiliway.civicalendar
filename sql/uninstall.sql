
SELECT @domainID := id FROM civicrm_domain WHERE name = 'Default Domain Name';
DELETE FROM civicrm_dashboard WHERE name = 'calendar' AND domain_id = @domainID;
