<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\Classroom;

class GoogleClassroomService
{
    private Classroom $classroom;

    /**
     * @param string $impersonateEmail Email admin Workspace yang di-"pinjam" identitasnya
     *                                 lewat domain-wide delegation (biasanya super admin Workspace).
     */
    public function __construct(string $impersonateEmail)
    {
        $client = new GoogleClient();
        $client->setAuthConfig(storage_path('app/google-service-account.json'));
        $client->setSubject($impersonateEmail);
        $client->addScope([
            Classroom::CLASSROOM_COURSES_READONLY,
            Classroom::CLASSROOM_ROSTERS_READONLY,
            Classroom::CLASSROOM_COURSEWORK_STUDENTS_READONLY,
            Classroom::CLASSROOM_COURSEWORKMATERIALS_READONLY,
        ]);

        $this->classroom = new Classroom($client);
    }

    public function countTeachers(string $courseId): int
    {
        return count($this->classroom->courses_teachers->listCoursesTeachers($courseId)->getTeachers() ?? []);
    }

    public function countStudents(string $courseId): int
    {
        return count($this->classroom->courses_students->listCoursesStudents($courseId)->getStudents() ?? []);
    }

    /** Jumlah classwork (tugas) yang dibuat guru dalam rentang tanggal tertentu */
    public function countTaskInRange(string $courseId, \DateTime $start, \DateTime $end): int
    {
        $count = 0;
        $pageToken = null;

        do {
            $response = $this->classroom->courses_courseWork->listCoursesCourseWork($courseId, [
                'courseWorkStates' => 'PUBLISHED',
                'pageToken'        => $pageToken,
            ]);
            foreach ($response->getCourseWork() ?? [] as $cw) {
                $created = new \DateTime($cw->getCreationTime());
                if ($created >= $start && $created <= $end) $count++;
            }
            $pageToken = $response->getNextPageToken();
        } while ($pageToken);

        return $count;
    }

    /** Jumlah classwork material (materi/berkas) yang diunggah guru dalam rentang tanggal tertentu */
    public function countMateriUploadInRange(string $courseId, \DateTime $start, \DateTime $end): int
    {
        $count = 0;
        $pageToken = null;

        do {
            $response = $this->classroom->courses_courseWorkMaterials->listCoursesCourseWorkMaterials($courseId, [
                'pageToken' => $pageToken,
            ]);
            foreach ($response->getCourseWorkMaterial() ?? [] as $m) {
                $created = new \DateTime($m->getCreationTime());
                if ($created >= $start && $created <= $end) $count++;
            }
            $pageToken = $response->getNextPageToken();
        } while ($pageToken);

        return $count;
    }
}