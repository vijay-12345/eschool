<?php
    
    Route::match(['get','post'],'/','BackController@index');
    
    Route::group(['middleware'=>['school']],function(){

        Route::get('/dashboard','BackController@dashboard');
        Route::get('/teacherdashboard','BackController@dashboard');
        Route::get('/profile','BackController@profile');
        Route::get('/logout','BackController@logout');
        Route::resource('class','ClassManagement');
        Route::resource('subject','subjectManagement');
        Route::resource('student','studentManagement');
        Route::post('/student','studentManagement@index');
        Route::resource('teacher','teacherManagement');
        Route::resource('mapping','subjectMaping');
        Route::resource('timetable','timetableManagement');
        Route::resource('notice','NoticeManagement');
        Route::resource('school','SchoolManagement');
        Route::post('/import','studentManagement@importExcel');
        Route::post('/importTeachers','teacherManagement@excelUpload');
        Route::match(['get','post'],'/app-version','AppvesionController@index');
        // Route::get('/delete_appversion','AppvesionController@delete');
        Route::delete('/delete_appversion/{id}', 'AppvesionController@delete');


        Route::resource('quiz','QuizController');
        Route::get('/quiz/publish/{id}','QuizController@publish');
        Route::resource('quiz-detail','QuizDetailController');
        
        Route::post('/quiz-detail','QuizDetailController@index');
        Route::get('/quiz-detail/create/{id}','QuizDetailController@create');
        Route::get('/quiz-detail/addQuestion/{id}','QuizDetailController@addQuestion');
        Route::post('/import-quiz-detail/{quiz_table_id}','QuizDetailController@importExcel');
        Route::resource('quiz-result','Quiz_ResultController');
        Route::post('/quiz-result','Quiz_ResultController@index');
        Route::get('quiz-results/{id}','Quiz_ResultController@quizresult');
        Route::post('/quiz-result/getsubject','Quiz_ResultController@getsubject');
        Route::post('/quiz-result/getquize','Quiz_ResultController@getquize');
        Route::post('/quiz-result/getquizeDetail','Quiz_ResultController@getquizeDetail');
        Route::post('/quiz-result-view','Quiz_ResultController@resultview');

        Route::resource('session','SessionController');
        Route::resource('study-material','StudyMaterialController');
        Route::post('/study-material','StudyMaterialController@index');
        Route::resource('assignment','AssignmentController');
        Route::post('/assignment','AssignmentController@index');
        Route::resource('forum','ForumController');
        Route::get('/forum','ForumController@index');
        Route::get('/forum/{id}/view','ForumController@forumdataview');
        Route::get('/add-in-forum','ForumController@forumdataview');
        Route::post('/add-in-forum','ForumController@forumdataview');
        Route::get('/change-password/{user_id}','BackController@changePassword');
        Route::get('/session-attendance','SessionAttendanceController@index');
        Route::post('/session-attendance','SessionAttendanceController@index');
        Route::get('/session-attendance/view/{session_table_id}/{date_from?}/{date_to}','SessionAttendanceController@attendanceView');
        Route::get('/session-attendance/view/{session_table_id}','SessionAttendanceController@attendanceView');
        
        Route::get('/session-attendance-by-student','SessionAttendanceController@sessionAttendanceStudentWise');
        Route::post('/session-attendance-by-student','SessionAttendanceController@sessionAttendanceStudentWise');

        Route::get('/session','SessionController@index');
        Route::post('/session','SessionController@index');
        //Route::post('/session/getsubject','SessionController@getsubject');
        Route::resource('library-study-material','LibraryStudyMaterialController');
        Route::get('library-study-material/download/{id}','LibraryStudyMaterialController@download');
        Route::resource('library-assignment','LibraryAssignmentController');
        Route::get('library-assignment/download/{id}','LibraryAssignmentController@download');



        Route::post('/class_section-using-school','ClassManagement@getClassSection');

        Route::post('/delete_attachment/{id}', 'AttachmentController@deleteRecord');
    });
