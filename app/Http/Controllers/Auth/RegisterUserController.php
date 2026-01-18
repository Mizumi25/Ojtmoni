<?php

namespace App\Http\Controllers\Auth;

//imports
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;


use App\Models\User;
use App\Models\YearLevel;
use App\Models\Course; 
use App\Models\Semester;
use App\Models\CourseOffering;

//Class
class RegisterUserController extends Controller
{
    //function para e render or ipakita ang View/Register Page (ge tawag ni siya didto sa route)
    public function register()
    {
        $activeSemester = Semester::where('status', 'active')->with('courses')->first();
        return view('auth.register', compact('activeSemester'));
    }
    
    public function store(Request $request) {
        $request->validate([
            'first_name' => ['required', 'max:255'],
            'last_name' => ['required', 'max:255'],
            'email' => 'required|email|unique:users',
            'password' => ['required', 'min:8', 'confirmed', Password::defaults()],
            'student_id' => ['nullable', 'unique:users,student_id', 'required_if:role,student'],
            'course_id' => ['required_if:role,student,coordinator', 'nullable', 'exists:courses,id'],
            'year_level_id' => ['nullable', 'integer', 'required_if:role,student'],
            'school_id_image' => ['nullable', 'image', 'max:2048', 'required_if:role,student'], 
        ]);
        
        $activeSemester = Semester::where('status', 'active')->first();

        if (!$activeSemester) {
            return redirect()->back()->withErrors(['no_active_semester' => 'There is no current active semester. Registration is temporarily disabled.'])->withInput();
        } else {
        
        $imagePath = null;
        if ($request->hasFile('school_id_image')) {
            $image = $request->file('school_id_image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('uploads/ids', $filename, 'public'); // Store in storage/app/public/uploads/ids
        }

    
        $courseOffering = CourseOffering::where('semester_id', $activeSemester->id)
            ->where('course_id', $request->course_id)
            ->first();

        $user = User::create([
            'name' => trim($request->first_name . ' ' . $request->last_name),
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->input('role', 'student'),
            'student_id' => $request->input('student_id'),
            'course_id' => $request->input('course_id'),
            'year_level_id' => $request->input('year_level_id'),
            'course_offering_id' => $courseOffering ? $courseOffering->id : null, 
            'school_id_image' => $imagePath,
        ]);
        
        if ($user->role === 'student') {
            $user->update(['status' => 'pending']);
        }
    
        return redirect()->route('login')->with('success', 'Registration successful! Please log in.');
      }
    }
    
    
    
    public function getYearLevels(Request $request)
    {
        $courseId = $request->query('course_id');
        
        // Fetch year levels associated with the selected course
        $yearLevels = YearLevel::whereHas('courses', function($query) use ($courseId) {
            $query->where('course_id', $courseId);
        })->get();
    
        return response()->json($yearLevels);
    }


}
