<?php
class CourseService {
    public function getAllCourses() {
        $course = new Course();
        return $course->getAll();
    }
    
    public function getCourseById($id) {
        $course = new Course();
        return $course->getById($id);
    }
}