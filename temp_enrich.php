<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$name = "ca_listed_terrorists";
echo "Starting enrichment for $name...\n";

$ds = App\Models\SancoDataset::where("name", $name)->first();
$ftm = collect($ds->resources)->firstWhere("name", "entities.ftm.json");
$url = $ftm["url"];
$size = $ftm["size"] ?? 0;
echo "FTM URL: $url ($size bytes)\n";

$tmpFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "test_ftm_" . uniqid() . ".json";
$fh = fopen($tmpFile, "wb");
$ch = curl_init($url);
curl_setopt_array($ch, [CURLOPT_TIMEOUT => 120, CURLOPT_FILE => $fh, CURLOPT_FOLLOWLOCATION => true]);
curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
fclose($fh);
echo "Download: HTTP $code, size=" . filesize($tmpFile) . "\n";

$entityIds = DB::table("sanco_entities")->where("dataset_name", $name)->pluck("entity_id")->flip()->toArray();
echo "Entity IDs: " . count($entityIds) . "\n";

$lines = file($tmpFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
unlink($tmpFile);
echo "Lines: " . count($lines) . "\n";

$processed = 0; $enriched = 0;
foreach ($lines as $line) {
    $obj = json_decode($line, true);
    if (!$obj) continue;
    $schema = $obj["schema"] ?? "";
    $eid = $obj["id"] ?? "";
    if (!in_array($schema, ["Person", "Organization"])) continue;
    if (!isset($entityIds[$eid])) continue;
    $props = $obj["properties"] ?? [];
    $aliases = isset($props["alias"]) ? implode("; ", $props["alias"]) : null;
    $wa = isset($props["weakAlias"]) ? implode("; ", $props["weakAlias"]) : null;
    if ($aliases || $wa) {
        DB::table("sanco_entities")->where("entity_id", $eid)->update(["aliases" => $aliases, "weak_aliases" => $wa, "updated_at" => now()]);
        $enriched++;
    }
    $processed++;
}
echo "Done! Processed $processed, enriched $enriched\n";
