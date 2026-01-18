<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\RegisterUserController;
use App\Http\Controllers\Auth\LoginUserController;

use App\Livewire\Intern\PendingApproval;
use App\Livewire\Intern\AgencyApplication;
use App\Livewire\Intern\OrientationResources;
use App\Livewire\Intern\AttendancePortal;
use App\Livewire\Intern\AgencyReports;
use App\Livewire\Intern\Dashboard4;
use App\Livewire\Coordinator\UserManagement;
use App\Livewire\Coordinator\ProgressReport;
use App\Livewire\TrackingMap;
use App\Livewire\ManageSchedule;
use App\Livewire\Coordinator\CompanyManagement;
use App\Livewire\Coordinator\Dashboard;
use App\Livewire\Coordinator\Assignment;
use App\Livewire\Admin\Dashboard2;
use App\Livewire\Admin\CourseSemester;
use App\Livewire\Admin\CoordinatorManagement;
use App\Livewire\Admin\StudentManagement;
use App\Livewire\Admin\SupportagencyManagement;
use App\Livewire\Agency\Dashboard3;
use App\Livewire\Agency\ViewSemester;
use App\Livewire\Agency\StudentList;
use App\Livewire\Profile\UserAccount;
use App\Livewire\Message;

use App\Http\Controllers\LocationController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');





Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterUserController::class, 'register'])
    ->name('register');
    Route::post('/register', [RegisterUserController::class, 'store'])
    ->name('register.store');
    Route::get('/get-year-levels', [RegisterUserController::class, 'getYearLevels']);
    
    Route::get('/login', [LoginUserController::class, 'login'])
    ->name('login');
    Route::post('/login', [LoginUserController::class, 'store'])
    ->name('login.store');
});




Route::middleware('auth')->group(function () {
  
      Route::post('/update-location', [LocationController::class, 'update']);

  
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard', Dashboard2::class)->name('dashboard2.index');
        Route::get('/course-sem', CourseSemester::class)->name('record.coursesem');
        Route::get('/coordinator-management', CoordinatorManagement::class)->name('record.coordinator');
        Route::get('/student-management', StudentManagement::class)->name('record.student');
        Route::get('/company-management', SupportagencyManagement::class)->name('record.company');
    });
    
    Route::middleware('role:coordinator')->group(function () {
        Route::get('/', Dashboard::class)->name('dashboard.index');
        Route::get('/users', UserManagement::class)->name('users.index');
        Route::get('/companies', CompanyManagement::class)->name('companies.index');
        Route::get('/progress', ProgressReport::class)->name('progress.index');
        Route::get('/assignments', Assignment::class)->name('assign.company');
    });
    
    Route::middleware(['role:student,pending'])->group(function () {
        Route::get('/pending-approval', PendingApproval::class)->name('intern.pending');
    });
    
    Route::middleware(['role:student,approved'])->group(function () {
        Route::get('/application', AgencyApplication::class)->name('application.agency');
        Route::get('/resources', OrientationResources::class)->name('orientation.resources');
    });
    
    Route::middleware(['role:student,intern'])->group(function () {
        Route::get('/attendance', AttendancePortal::class)->name('attendance.portal');
        Route::get('/dashboardss', Dashboard4::class)->name('dashboard4.index');
        Route::get('/agency-reports', AgencyReports::class)->name('agency.reports');
    });

    
    Route::middleware(['role:agency'])->group(function () {
        Route::get('/dashboards', Dashboard3::class)->name('dashboard3.index');
        Route::get('/semester', ViewSemester::class)->name('view-semester');
        Route::get('/listofstudents', StudentList::class)->name('student-list');
    });
    
    Route::middleware(['role:admin,coordinator'])->group(function () {
       Route::get('/tracks', TrackingMap::class)->name('tracks.index');
       Route::get('/tracks/agency/{agency_id}', TrackingMap::class)->name('tracks.agency');
    });
    Route::middleware(['role:admin,coordinator,student'])->group(function () {
       Route::get('/message', Message::class)->name('message');
       Route::get('/profile', UserAccount::class)->name('user.account');
    });
    Route::middleware(['role:agency,admin,coordinator'])->group(function () {
       Route::get('/schedules', ManageSchedule::class)->name('schedules');
    });
    
    
    Route::post('/logout', [LoginUserController::class, 'logout'])->name('logout');
    Route::get('/logout', function () {
      return redirect("/");
    })->name('logout.get');
    
    
    
});






