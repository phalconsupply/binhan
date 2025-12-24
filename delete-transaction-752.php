<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Transaction;

$t = Transaction::find(752);
if ($t) {
    echo "Xoa GD #752: {$t->note}\n";
    $t->delete();
    echo "Da xoa!\n";
} else {
    echo "Khong tim thay GD #752\n";
}
