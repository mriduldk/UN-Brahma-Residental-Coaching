<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$form = App\Models\ApplicationForm::first();

if (!$form) {
    echo "No form found.\n";
    exit;
}

$config = $form->field_config ?? [];

// The user is using 'parents' in the view, so we must ensure 'parents' has all the fields.
// We'll take the complete list we previously assigned to 'parent' and assign it to 'parents'.

$allParentFields = [
    'father_name', 'father_mobile', 'father_occupation', 'father_annual_income',
    'mother_name', 'mother_mobile', 'mother_occupation',
    'guardian_name', 'guardian_relation', 'guardian_mobile', 'guardian_address'
];

$config['parents']['enabled'] = true;
$config['parents']['fields'] = $allParentFields;

// Keep 'parent' synced just in case other parts use it
$config['parent']['enabled'] = true;
$config['parent']['fields'] = $allParentFields;

$form->field_config = $config;
$form->save();

echo "Updated 'parents' config to include all fields.\n";
