<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('apiauth')->get('/user', function (Request $request) {
    return $request->user();
});


	Route::post('login', 'Api\LoginController@login');
	Route::post('signup', 'Api\LoginController@signup');
	Route::post('useredit', 'Api\LoginController@useredit');
	Route::post('check_notification', 'Api\ClassController@checkNotification');
	Route::post('check_above_version', 'Api\AppVersionController@nextVersion');
	Route::post('add_update_app_version', 'Api\AppVersionController@addUpdateAppVersion');
        Route::post('get_schools', 'Api\SchoolController@getSchoolsByParentSchool');

Route::group(['middleware' => ['apiauth']], function() {
	Route::post('login-details', 'Api\LoginController@loginDetails');
	Route::post('management', 'Api\ManagementController@studentAndTeacherDetails');
	Route::post('set_as_read', 'Api\ClassController@setRead');
	Route::post('logout', 'Api\LoginController@logout');
	Route::post('change_password', 'Api\LoginController@changePassword');

	Route::post('get-notice', 'Api\NoticeController@notice');
	Route::post('add-session', 'Api\SessionController@addSession');
	Route::post('update-session', 'Api\SessionController@updateSession');
	Route::post('get_my_all_session', 'Api\SessionController@getMyAllSession');
	Route::post('get_upcoming_session', 'Api\SessionController@getUpcomingClassSessions');

	Route::post('time-table', 'Api\TimeTableController@timeTable');
	Route::post('get-class-section', 'Api\ClassSectionController@getClassSection');
	Route::post('get-subject', 'Api\SubjectMasterController@getSubjectList');
	// Route::post('get-subject', 'Api\SubjectController@getSubjectList');

	Route::post('add-study-material', 'Api\StudyMaterialController@addStudyMaterial');
	Route::post('get-study-material', 'Api\StudyMaterialController@getStudyMaterial');
        
       


	Route::post('image-upload', 'Api\ImageController@uploadImage');

	Route::post('add-assignment', 'Api\AssignmentController@addAssignment');
	Route::post('student-submit-assignment', 'Api\AssignmentController@submitAssignmentByStudent');

	Route::post('get_my_created_assingment', 'Api\AssignmentController@getMyCreatedAssingment');
	Route::post('get_assingment_submitted_student_list', 'Api\AssignmentController@getAssingmentSubmittedStudentList');
	Route::post('get_submitted_assigment_student_detail', 'Api\AssignmentController@getsubmittedAssigmentStudentDetail');

	Route::post('get_assignment_list', 'Api\AssignmentController@getAssignmentList');
	Route::post('get_submitted_assignment_list', 'Api\AssignmentController@getSubmittedAssignmentList');

	Route::post('add_forum_post', 'Api\ForumController@addForumPost');
	Route::post('get_forum_post', 'Api\ForumController@getForumPost');
	Route::post('get_forum_last', 'Api\ForumController@getForumLast');

	Route::post('update-assignment', 'Api\AssignmentController@addAssignment');
	Route::post('add_update_quiz_table', 'Api\QuizController@addUpdateQuizTable');
        Route::post('add_publish_quiz_table', 'Api\QuizController@addPublishQuizTable');
	Route::post('add_update_quiz_detail_table', 'Api\QuizController@addUpdateQuizDetail');
	Route::post('add_update_quiz_result_table', 'Api\QuizController@addUpdateQuizResult');	
	Route::post('get_quiz', 'Api\QuizController@getQuiz');
	Route::post('get_quiz_result', 'Api\QuizController@getQuizResult');
	Route::post('get_quiz_table', 'Api\QuizController@getTableQuiz');

	Route::post('add_update_session_attendance', 'Api\SessionAttendanceController@addUpdateSessionAttendance');
	Route::post('get_session_attendance', 'Api\SessionAttendanceController@getSessionAttendance');

	Route::post('add_class_section', 'Api\ClassSectionController@addClassSection');
	Route::post('update_class_section', 'Api\ClassSectionController@updateClassSection');
	Route::post('add_notice_board', 'Api\NoticeController@addNotice');
	Route::post('update_notice_board', 'Api\NoticeController@updateNotice');
	Route::post('add_update_subject_class', 'Api\SubjectClassController@addUpdateSubjectClass');
	Route::post('add_subject_master', 'Api\SubjectMasterController@addSubject');
	Route::post('update_subject_master', 'Api\SubjectMasterController@updateSubject');
	Route::post('add_teacher_class_subject', 'Api\TeacherClassSubjectController@addTeacherClassSubject');
	Route::post('add_update_time_table', 'Api\TimeTableController@addTimeTable');
	Route::post('delete_assignment', 'Api\AssignmentController@deleteAssignment');
	Route::post('delete_assignment_submittted', 'Api\AssignmentController@deleteAssignmentSubmittted');
	Route::post('delete_class_section', 'Api\ClassSectionController@deleteClassSection');
	Route::post('delete_forum', 'Api\ForumController@deleteForumPost');
	Route::post('delete_notice', 'Api\NoticeController@deleteNotice');
	Route::post('delete_session', 'Api\SessionController@deleteSession');
	Route::post('delete_study_material', 'Api\StudyMaterialController@deleteStudyMaterial');
	Route::post('delete_subject_class', 'Api\SubjectClassController@deleteSubjectClass');
	Route::post('delete_subject', 'Api\SubjectMasterController@deleteSubject');
	Route::post('delete_teacher_class_subject', 'Api\TeacherClassSubjectController@deleteTeacherClassSubject');
	Route::post('delete_time_table', 'Api\TimeTableController@deleteTimeTable');
		
	Route::post('time-table-form', 'Api\TimeTableController@timeTableForm');
	Route::post('class-section-form', 'Api\ClassSectionController@classSectinForm');
	Route::post('notice-form', 'Api\NoticeController@noticeForm');
	Route::post('subject-class-form', 'Api\SubjectClassController@subjectClassForm');
	Route::post('subject-master-form', 'Api\SubjectMasterController@subjectMasterForm');
	Route::post('app-version-form', 'Api\AppVersionController@appVersionForm');
	Route::post('quiz-form', 'Api\QuizController@quizForm');
        Route::post('quiz-question-form/{id}', 'Api\QuizController@quizQuestionForm');
        Route::post('quiz-publish-form/{id}', 'Api\QuizController@quizPublishForm');
        Route::post('quiz-question-editform/{id}', 'Api\QuizController@quizQuestionEditForm');
	Route::post('quiz-detail-form', 'Api\QuizController@quizDetailForm');
	Route::post('session-form', 'Api\SessionController@sessionForm');
	Route::post('study-material-form', 'Api\StudyMaterialController@studymaterialForm');
        Route::post('library-study-material-form', 'Api\StudyMaterialController@libraryStudymaterialForm');
        // Route::post('library-assignment-form', 'Api\AssignmentController@libraryAssignmentForm');
	Route::post('assignment-form', 'Api\AssignmentController@assignmentForm');
        Route::post('library-assignment-form', 'Api\AssignmentController@libraryAssignmentForm');
	Route::post('forum-add-in-form/{teacher_class_subject_id}', 'Api\ForumController@forumForm');
	Route::post('study-material-download-form', 'Api\StudyMaterialController@StudymaterialDownloadForm');
	Route::post('assignment-download-form', 'Api\AssignmentController@AssignmentDownloadForm');



	Route::post('add-study-material-web-panel', 'Api\StudyMaterialController@addStudyMaterialWebPanel');
	Route::post('update-study-material-web-panel', 'Api\StudyMaterialController@addStudyMaterialWebPanel');
	Route::post('update-assignment-web-panel', 'Api\AssignmentController@addAssignmentWebPanel');
	Route::post('add-assignment-web-panel', 'Api\AssignmentController@addAssignmentWebPanel');
	Route::post('add_forum_post_web_panel', 'Api\ForumController@addForumPostWebPanel');

 
});




