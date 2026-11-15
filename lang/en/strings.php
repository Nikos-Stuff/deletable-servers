<?php

return [
    'section' => [
        'heading' => 'Delete Server',
        'description' => 'Permanently delete this server and all associated data. This action cannot be undone.',
        'warning' => 'All files, databases, backups, and allocations will be removed. Make sure you have exported anything you need.',
    ],
    'action' => [
        'label' => 'Delete Server',
        'modal_heading' => 'Permanently delete this server?',
        'modal_description' => 'This action cannot be undone. The server, all its files, databases, and backups will be permanently removed. Type the server name below to confirm.',
        'submit' => 'Yes, permanently delete',
        'confirm_label' => 'Type the server name to confirm',
        'name_mismatch' => 'The server name does not match.',
    ],
    'notifications' => [
        'deleted_title' => 'Server deleted',
        'deleted_body' => 'The server has been permanently deleted.',
        'failed_title' => 'Failed to delete server',
        'not_allowed_title' => 'Deletion not available',
        'not_allowed_body' => 'Only free-tier servers can be deleted here.',
    ],
];
