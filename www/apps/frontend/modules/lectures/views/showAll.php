<p>Liste des conférences</p>

<?php
    $lang;

    echo '<ul>';

    foreach($lectures as $lecture)
    {
        $methodName = 'getName'.ucfirst($lang);
        $methodDescription = 'getDescription'.ucfirst($lang);
?>
        <li>
        <p>
            <h> <?php echo $lecture->$methodName(); ?> </h>
            <p class="lectureDescription"><?php echo $lecture->$methodDescription(); ?></p>
        </p>
        </li>
<?php
    }

    echo '</ul>';
?>
