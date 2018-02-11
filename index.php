<?php
/**
 * Created by PhpStorm.
 * User: Max
 * Date: 2017-12-29
 * Time: 10:00 PM
 */

include_once('src/Creature.php');

$name = isset($_GET['name']) ? str_replace('_', ' ', $_GET['name']) : false;
?>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>D&D Creature Hub</title>
</head>
<body>
<div class="header">
    <h1>D&D Creature Hub</h1>
</div>
<div class="display">
    <div class="sidebar">
        <div class="links card">
            <?php
            $dir = scandir("./creatures");
            foreach ($dir as $creature) {
                $creature = str_replace(['.json', '_'], ['', ' '], $creature);
                if ($creature !== '.' && $creature !== '..') {
                    echo "<a class='creature_link" . ($creature === $name ? " active" : "") . "' href='?name=$creature'>$creature</a>";
                }
            }
            ?>
        </div>
    </div>
    <div class="container">
    <?php

    if ($name) {
        $entity = new Creature();
        $entity->load($name);
        $entity->display();
    } else {
        $entity = new Creature();
        $entity->display();
        $entity->save();
    }

    ?>
    </div>
</div>
</body>
</html>

