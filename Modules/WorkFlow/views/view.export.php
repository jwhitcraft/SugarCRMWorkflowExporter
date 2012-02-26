<?php

class CustomWorkFlowViewExport extends SugarView
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::SugarView();
    }

    public function display()
    {
        global $current_user;

        $this->ss->assign('workflow_modules', get_workflow_admin_modules_for_user($current_user));
    }
}