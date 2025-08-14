<?php
declare(strict_types=1);
require __DIR__.'/_bootstrap.php';
require_login();

$path = $STORAGE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'destinations.json';
$curr = is_file($path) ? file_get_contents($path) : "";

ob_start();
?>
<div class="admin-card">
    <h3>Popular Destinations (JSON array)</h3>
    <div class="admin-form-row" style="margin-bottom:8px">
        <input class="admin-input" id="filterDest" placeholder="Search by name..." oninput="filterJson()" />
        <button class="admin-btn" type="button" onclick="formatJson()">Format JSON</button>
    </div>
    <form method="post" action="save_destinations.php">
        <input class="admin-input" type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES); ?>" />
        <textarea class="admin-textarea" name="destinations"><?php echo htmlspecialchars($curr, ENT_QUOTES); ?></textarea>
        <div style="margin-top:16px"><button class="admin-btn" type="submit">Save</button></div>
    </form>
    <p class="admin-muted">Örnek:
    <pre>[{
"name": "Madrid Airport",
"image": "/assets/mytransfersweb/prod/images/airports/1095.jpg",
"url": "/en/destination/spain/madrid-airport-barajas-mad/"
}]</pre></p>
</div>
<script>
function formatJson(){
  try{
    const ta=document.querySelector('textarea[name="destinations"]');
    const obj=JSON.parse(ta.value); ta.value=JSON.stringify(obj,null,2);
  }catch(e){ alert('Invalid JSON: '+e.message); }
}
function filterJson(){
  try{
    const q=(document.getElementById('filterDest').value||'').toLowerCase();
    const ta=document.querySelector('textarea[name="destinations"]');
    const src = <?php echo json_encode($curr ?: '[]'); ?>;
    const data = JSON.parse(src);
    if(!q){ ta.value = JSON.stringify(data,null,2); return; }
    const filtered = data.filter(x => (x.name||'').toLowerCase().includes(q));
    ta.value = JSON.stringify(filtered,null,2);
  }catch(e){ /* ignore */ }
}
</script>
<?php
$content = ob_get_clean();
$layout = file_get_contents(__DIR__.'/_layout.php');
echo str_replace('<!-- PAGE_CONTENT -->', $content, $layout);


