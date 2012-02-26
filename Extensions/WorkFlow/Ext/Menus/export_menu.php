<?php

global $current_user;

if($current_user->isAdmin())$module_menu[]=Array("index.php?module=Workflow&action=export", $mod_strings['LBL_EXPORT_WORKFLOWS'],"ExportWorkflowDefinition");