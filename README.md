# MEC Upcoming Events snippet
Function to fetch MEC events and parse mec_fields then display in a looper with Themeco Pro Looper.

Creates the `cs_looper_custom_uptournaments` but you use `uptournaments` as the name of the `Looper Provider` Hook.

NOTE: 
--`parse_mec_fields` Refers to another snippet, make sure it is active
--Make sure to edit the `AND NOT (p.post_title like '%minecraft%' OR p.post_title like 'Smash Summit%')` to remove any unwanted events
