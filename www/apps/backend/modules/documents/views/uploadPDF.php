<h1>Documents</h1>
<p>Depuis cette page, vous pouvez uploader les fichiers PDF pour un package.</p>

<?php
    $form = new Form('', 'post');

    $choices = array();

    foreach($packages as $package)
        $choices[$package->getId()] = $package->getName('fr');

    $form->add('select', 'PackageList')
         ->label('Sélection du package : ')
         ->choices($choices);

    $form->add('file', 'PDFFile')
         ->label('Chemin du fichier PDF : ');

    $form->add('submit', 'Envoyer');

    echo $form->toString();
?>
