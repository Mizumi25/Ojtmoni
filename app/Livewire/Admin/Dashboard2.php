<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\DailyLog;
use App\Models\Semester;

class Dashboard2 extends Component
{
    public $totalStudents;
    public $pendingStudents;
    public $approvedStudents;
    public $internStudents;
    public $rejectedStudents;
    public $completedStudents;
    public $incompleteStudents;
    
    public $totalDailyLogs;
    public $missedLogs;
    public $pendingLogs;
    public $lateLogs;
    public $completedLogs;
    
    public $activeSemester;
    public $recentStudents;

    public function mount()
    {
        // Student counts by status
        $this->totalStudents = User::where('role', 'student')->count();
        $this->pendingStudents = User::where('role', 'student')->where('status', 'pending')->count();
        $this->approvedStudents = User::where('role', 'student')->where('status', 'approved')->count();
        $this->internStudents = User::where('role', 'student')->where('status', 'intern')->count();
        $this->rejectedStudents = User::where('role', 'student')->where('status', 'rejected')->count();
        $this->completedStudents = User::where('role', 'student')->where('status', 'completed')->count();
        $this->incompleteStudents = User::where('role', 'student')->where('status', 'incomplete')->count();
        $this->recentStudents = User::where('role', 'student')
        ->orderBy('created_at', 'desc')
        ->limit(5) // or whatever number you want to show
        ->get();
        
        // Daily log counts by status
        $this->totalDailyLogs = DailyLog::count();
        $this->missedLogs = DailyLog::where('status', 'missed')->count();
        $this->pendingLogs = DailyLog::where('status', 'pending')->count();
        $this->lateLogs = DailyLog::where('status', 'late')->count();
        $this->completedLogs = DailyLog::where('status', 'completed')->count();
        
        // Get active semester
        $this->activeSemester = Semester::where('status', 'active')->first();
    }

    public function render()
    {
        return view('livewire.admin.dashboard2', [
            'totalStudents' => $this->totalStudents,
            'pendingStudents' => $this->pendingStudents,
            'approvedStudents' => $this->approvedStudents,
            'internStudents' => $this->internStudents,
            'rejectedStudents' => $this->rejectedStudents,
            'completedStudents' => $this->completedStudents,
            'incompleteStudents' => $this->incompleteStudents,
            'totalDailyLogs' => $this->totalDailyLogs,
            'missedLogs' => $this->missedLogs,
            'pendingLogs' => $this->pendingLogs,
            'lateLogs' => $this->lateLogs,
            'completedLogs' => $this->completedLogs,
            'activeSemester' => $this->activeSemester,
        ])->layout('layouts.auth-layout');
    }
}