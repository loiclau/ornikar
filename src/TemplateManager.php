<?php

namespace Template;

use Template\Entity\Lesson;
use Template\Entity\Template;
use Template\Entity\Learner;
use Template\Context\ApplicationContext;
use Template\Repository\LessonRepository;
use Template\Repository\MeetingPointRepository;
use Template\Repository\InstructorRepository;

class TemplateManager
{
    private $applicationContext;

    private $lessonRepository;
    private $MeetingPointRepository;
    private $InstructorRepository;

    public function __construct()
    {
        $this->applicationContext = ApplicationContext::getInstance();
    }

    /**
     * @param Template $tpl
     * @param array $data
     * @return Template
     */
    public function getTemplateComputed(Template $tpl, array $data): Template
    {
        $replaced = clone($tpl);
        $replaced->subject = $this->computeText($replaced->subject, $data);
        $replaced->content = $this->computeText($replaced->content, $data);

        return $replaced;
    }

    /**
     * @param Lesson $lesson
     */
    public function initLesson(Lesson $lesson): void
    {
        $oLesson = LessonRepository::getInstance();
        $this->lessonRepository = $oLesson->getById($lesson->id);

        $oMeetingPoint = MeetingPointRepository::getInstance();
        $this->MeetingPointRepository = $oMeetingPoint->getById($lesson->meetingPointId);

        $oInstructor = InstructorRepository::getInstance();
        $this->InstructorRepository = $oInstructor->getById($lesson->instructorId);
    }

    /**
     * @param $text
     * @param array $data
     * @return string
     */
    private function computeText($text, array $data): string
    {
        $lesson = (isset($data['lesson']) && $data['lesson'] instanceof Lesson)
            ? $data['lesson']
            : null;

        $user = (isset($data['user']) && ($data['user'] instanceof Learner))
            ? $data['user']
            : $this->applicationContext->getCurrentUser();

        if ($lesson) {
            $this->initLesson($lesson);
            $text = $this->computeSummary($text);

            if (strpos($text, '[lesson:start_date]') !== false) {
                $text = str_replace('[lesson:start_date]', $lesson->start_time->format('d/m/Y'), $text);
            }
            if (strpos($text, '[lesson:start_time]') !== false) {
                $text = str_replace('[lesson:start_time]', $lesson->start_time->format('H:i'), $text);
            }
            if (strpos($text, '[lesson:end_time]') !== false) {
                $text = str_replace('[lesson:end_time]', $lesson->start_time->format('H:i'), $text);
            }
        }

        $text = $this->computeInstructor($text);

        if ($lesson->meetingPointId) {
            $text = $this->computeMeetingPoint($text);
        }
        $text = $this->computeUser($text, $user);

        return $text;
    }

    /**
     * @param $text
     * @return string
     */
    private function computeSummary($text): string
    {
        $containsSummaryHtml = strpos($text, '[lesson:summary_html]');
        $containsSummary = strpos($text, '[lesson:summary]');

        if ($containsSummaryHtml !== false || $containsSummary !== false) {
            if ($containsSummaryHtml !== false) {
                $text = str_replace(
                    '[lesson:summary_html]',
                    Lesson::renderHtml($this->lessonRepository),
                    $text
                );
            }
            if ($containsSummary !== false) {
                $text = str_replace(
                    '[lesson:summary]',
                    Lesson::renderText($this->lessonRepository),
                    $text
                );
            }
        }
        return $text;
    }

    /**
     * @param $text
     * @return string
     */
    private function computeInstructor($text): string
    {
        if (strpos($text, '[lesson:instructor_name]') !== false) {
            $text = str_replace('[lesson:instructor_name]', $this->InstructorRepository->firstname, $text);
        }

        if (!empty($this->InstructorRepository)) {
            $text = str_replace(
                '[lesson:link]',
                $this->MeetingPointRepository->url . '/' . $this->InstructorRepository->id .
                '/lesson/' . $this->lessonRepository->id,
                $text
            );
        } else {
            $text = str_replace('[lesson:link]', '', $text);
        }
        return $text;
    }

    /**
     * @param $text
     * @param $user
     * @return string
     */
    private function computeUser($text, $user): string
    {
        if ($user && (strpos($text, '[user:first_name]') !== false)) {
            $text = str_replace('[user:first_name]', ucfirst(mb_strtolower($user->firstname)), $text);
        }
        return $text;
    }

    /**
     * @param $text
     * @return string
     */
    private function computeMeetingPoint($text): string
    {
        if (strpos($text, '[lesson:meeting_point]') !== false) {
            $text = str_replace('[lesson:meeting_point]', $this->MeetingPointRepository->name, $text);
        }
        return $text;
    }
}
