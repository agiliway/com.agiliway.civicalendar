
SELECT @domainID := id FROM civicrm_domain WHERE name = 'Default Domain Name';
REPLACE INTO civicrm_dashboard 
    (domain_id, name, label, url, permission, permission_operator, is_active, fullscreen_url, is_reserved, cache_minutes)
 VALUES
    (@domainID, 'calendar', 'Calendar', 'civicrm/dashlet/calendar?reset=1', 'access CiviCRM', NULL, 1, 'civicrm/dashlet/calendar?reset=1&context=dashletFullscreen', 1, 60);