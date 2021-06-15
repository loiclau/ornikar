<?php

use Template\TemplateManager;
use Template\Entity\Lesson;
use Template\Entity\Template;
use Template\Repository\MeetingPointRepository;
use Template\Repository\InstructorRepository;
use Template\Context\ApplicationContext;

class TemplateManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Init the mocks
     */
    public function setUp()
    {
    }

    /**
     * Closes the mocks
     */
    public function tearDown()
    {
    }

    /**
     * @test
     */
    public function test()
    {
        $faker = \Faker\Factory::create();

        $expectedInstructor = InstructorRepository::getInstance()->getById($faker->randomNumber());
        $expectedMeetingPoint = MeetingPointRepository::getInstance()->getById($faker->randomNumber());
        $expectedUser = ApplicationContext::getInstance()->getCurrentUser();
        $start_at = $faker->dateTimeBetween("-1 month");
        $end_at = $start_at->add(new DateInterval('PT1H'));

        $lesson = new Lesson($faker->randomNumber(), $faker->randomNumber(), $faker->randomNumber(), $start_at, $end_at);

        $template = new Template(
            1,
            'Votre leçon de conduite avec [lesson:instructor_name]',
            "
Bonjour [user:first_name],

La reservation du [lesson:start_date] de [lesson:start_time] à [lesson:end_time] avec [lesson:instructor_name] a bien été prise en compte!
Voici votre point de rendez-vous: [lesson:meeting_point].

Bien cordialement,

L'équipe Ornikar
");
        $templateManager = new TemplateManager();

        $message = $templateManager->getTemplateComputed(
            $template,
            [
                'lesson' => $lesson
            ]
        );

        $this->assertEquals('Votre leçon de conduite avec ' . $expectedInstructor->firstname, $message->subject);
        $this->assertEquals("
Bonjour " . $expectedUser->firstname . ",

La reservation du " . $start_at->format('d/m/Y') . " de " . $start_at->format('H:i') . " à " . $end_at->format('H:i') . " avec " . $expectedInstructor->firstname . " a bien été prise en compte!
Voici votre point de rendez-vous: " . $expectedMeetingPoint->name . ".

Bien cordialement,

L'équipe Ornikar
", $message->content);
    }
}
