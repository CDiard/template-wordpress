<?php
$twig = $GLOBALS['twig'];

echo $twig->render('pages/page.twig', [
    'test' => 'Hello World!'
]);
