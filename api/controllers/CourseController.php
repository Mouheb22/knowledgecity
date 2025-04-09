<?php
class CourseController {
    private $courseService;
    
    public function __construct() {
        $this->courseService = new CourseService();
    }
    
    public function getAllCourses() {
        $courses = $this->courseService->getAllCourses();
        
        if ($courses) {
            header('Content-Type: application/json');
            return json_encode($courses);
        } else {
            http_response_code(404);
            return json_encode(['message' => 'No courses found']);
        }
    }
    
    public function getCourseById($id) {
        $course = $this->courseService->getCourseById($id);
        
        if ($course) {
            header('Content-Type: application/json');
            return json_encode($course);
        } else {
            http_response_code(404);
            return json_encode(['message' => 'Course not found']);
        }
    }
}