<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Template\TemplateManager;
use Template\Entity\Template;
use Template\Entity\Lesson;

$faker = \Faker\Factory::create();

$template = new Template(
    1,
    'Votre leçon avec [lesson:instructor_name]',
    "
Bonjour [user:first_name] [user:last_name] [user:email],

Merci d'avoir réservé une leçon de conduite avec  [lesson:instructor_name] le [lesson:start_date] à [lesson:meeting_point].

Bien cordialement,

L'équipe Ornikar
");
$templateManager = new TemplateManager();

$start_at = $faker->dateTimeBetween("-1 month");
$end_at = $start_at->add(new DateInterval('PT1H'));


$message = $templateManager->getTemplateComputed(
    $template,
    [
        'lesson' => new Lesson($faker->randomNumber(), $faker->randomNumber(), $faker->randomNumber(), $start_at, $end_at)
    ]
);

echo $message->subject . "\n" . $message->content;
