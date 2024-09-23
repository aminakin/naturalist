<?php

$isWater = false;

if (isset($_GET['water'])) {
    $isWater = true;
}
?>

<script>
    window.isWater = '<?= $isWater ?>';
</script>