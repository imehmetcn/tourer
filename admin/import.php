<?php
declare(strict_types=1);
require __DIR__.'/_bootstrap.php';
require_login();
require_admin();

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
	$target = trim((string)($_POST['target'] ?? ''));
	$tmp = $_FILES['file']['tmp_name'] ?? '';
    $dry = isset($_POST['dry_run']) && $_POST['dry_run'] === '1';
	if ($target && is_uploaded_file($tmp)) {
		$rows = array_map('str_getcsv', file($tmp));
		if ($target === 'pricing') {
			// CSV: base_per_km; vehicle:multiplier pairs
			$data = [ 'base_per_km' => 1.2, 'vehicle_multipliers' => [], 'region_multipliers' => ['default'=>1.0] ];
			foreach ($rows as $r) {
				if (count($r) < 2) continue;
				$k = trim((string)$r[0]); $v = (float)$r[1];
				if (strtolower($k) === 'base_per_km') { $data['base_per_km'] = $v; continue; }
				$data['vehicle_multipliers'][$k] = $v;
			}
			if ($dry) {
                $msg = 'Dry‑run OK · base_per_km=' . $data['base_per_km'] . ' · vehicles=' . count($data['vehicle_multipliers']);
            } else {
                write_json($STORAGE.'/data/pricing.json', $data);
                $msg = 'Pricing imported.';
            }
		} elseif ($target === 'coupons') {
			// CSV: code,discount_type,discount_value,valid_from,valid_to,min_amount
			$out = []; $head = true; $errors = 0;
			foreach ($rows as $r) {
				if ($head) { $head = false; continue; }
				if (count($r) < 3) continue;
				$code = trim((string)$r[0]);
				$type = trim((string)$r[1]);
				$val = (float)$r[2];
				if (!in_array($type, ['percent','fixed'], true)) { $errors++; continue; }
				$out[] = [
					'code' => $code,
					'discount_type' => $type,
					'discount_value' => $val,
					'valid_from' => $r[3] ?? '',
					'valid_to' => $r[4] ?? '',
					'min_amount' => isset($r[5]) ? (float)$r[5] : 0
				];
			}
			if ($dry) {
                $msg = 'Dry‑run OK · rows=' . count($out) . ' · errors=' . $errors;
            } else {
                write_json($STORAGE.'/data/coupons.json', $out);
                $msg = 'Coupons imported. rows=' . count($out);
            }
		} elseif ($target === 'destinations') {
			// CSV: name,image,url
			$out = []; $head = true;
			foreach ($rows as $r) {
				if ($head) { $head = false; continue; }
				if (count($r) < 1) continue;
				$out[] = [ 'name' => $r[0] ?? '', 'image' => $r[1] ?? '', 'url' => $r[2] ?? '' ];
			}
			if ($dry) {
                $msg = 'Dry‑run OK · rows=' . count($out);
            } else {
                write_json($STORAGE.'/data/destinations.json', $out);
                $msg = 'Destinations imported. rows=' . count($out);
            }
		}
	}
}

ob_start();
?>
<div class="admin-card">
	<h3>Import</h3>
	<?php if ($msg): ?><div class="admin-muted" style="margin-bottom:8px"><?php echo htmlspecialchars($msg, ENT_QUOTES); ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data" class="admin-form-row">
		<select class="admin-select" name="target">
			<option value="pricing">Pricing</option>
			<option value="coupons">Coupons</option>
			<option value="destinations">Destinations</option>
		</select>
		<input class="admin-input" type="file" name="file" accept=".csv" />
        <label style="display:flex;align-items:center;gap:8px"><input type="checkbox" name="dry_run" value="1" /> Validate only (dry‑run)</label>
		<button class="admin-btn" type="submit">Upload</button>
	</form>
</div>

<div class="admin-card" style="margin-top:16px">
	<h3>Export</h3>
	<div class="admin-form-row">
		<a class="admin-btn" href="/mytransfers/admin/export.php?type=reservations">Reservations CSV</a>
		<a class="admin-btn" href="/mytransfers/admin/export.php?type=coupons">Coupons CSV</a>
		<a class="admin-btn" href="/mytransfers/admin/export.php?type=pricing">Pricing JSON</a>
		<a class="admin-btn" href="/mytransfers/admin/export.php?type=destinations">Destinations JSON</a>
	</div>
</div>
<?php
$content = ob_get_clean();
$layout = file_get_contents(__DIR__.'/_layout.php');
echo str_replace('<!-- PAGE_CONTENT -->', $content, $layout);


