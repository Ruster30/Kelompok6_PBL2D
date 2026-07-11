<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

$events = App\Models\Event::all();
foreach ($events as $e) {
    $rabCount = App\Models\Rab::where("event_id", $e->id)->count();
    $total = App\Models\Rab::where("event_id", $e->id)->sum("subtotal_biaya");
    echo "Event #{$e->id}: {$e->nama_event} - RAB: {$rabCount} items - Total: {$total}\n";
}
