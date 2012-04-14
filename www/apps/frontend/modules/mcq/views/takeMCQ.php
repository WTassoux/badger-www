<?php
    $methodLabel = 'getLabel'.ucfirst($lang);

    $form = new Form('', 'post');

    for($i=0; $i<count($questions); $i++)
    {
        $form->beginFieldset('Question ' . ($i + 1));

        $form->add('label', '')
             ->label($questions[$i]->$methodLabel());

        foreach($answers as $answer)
        {
            if($questions[$i]->getId() == $answer->getIdQuestion())
            {
                // Don't remove or change $answer->getId()! It is used to 
                // retrieve the answers of the user
                $form->add('checkbox', $answer->getId())
                     ->label($answer->$methodLabel());
            }
        }

        $form->endFieldset();
    }

    $form->add('hidden', 'isSubmitted')
         ->value('on');

    $form->add('submit', $TEXT['MCQ_SubmitAnswers']);

    // TODO: Ajouter une fenêtre de confirmation en javascript si possible (cf. confirm() )

    echo $form->toString();
?>

