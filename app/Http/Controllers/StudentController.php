<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        return view('students.index');
    }

    public function list(Request $request)
    {
        $limit = 5;
        $page = $request->input('page', 1);
        $sortColumn = $request->input('sort', 0);
        $direction = $request->input('direction', 'asc');
        
        $columns = ['sid', 'fname', 'lname', 'birthplace', 'birthDate'];
        $sortBy = $columns[$sortColumn] ?? 'sid';
        
        $students = Student::orderBy($sortBy, $direction)
            ->paginate($limit);
            
        return response()->json([
            'students' => $students->items(),
            'totalPages' => $students->lastPage(),
            'currentPage' => $students->currentPage()
        ]);
    }

    public function search(Request $request)
    {
        $query = Student::query();

        if ($request->filled('sid')) {
            $query->where('sid', $request->sid);
        }
        if ($request->filled('fname')) {
            $query->where('fname', 'like', '%' . $request->fname . '%');
        }
        if ($request->filled('lname')) {
            $query->where('lname', 'like', '%' . $request->lname . '%');
        }
        if ($request->filled('birthplace')) {
            $query->where('birthplace', 'like', '%' . $request->birthplace . '%');
        }
        if ($request->filled('birthDate')) {
            $query->where('birthDate', $request->birthDate);
        }

        $students = $query->get();
        
        return response()->json([
            'students' => $students,
            'totalPages' => ceil($students->count() / 5)
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fname' => 'required',
            'lname' => 'required',
            'birthplace' => 'required',
            'birthDate' => 'required|date'
        ]);

        $student = Student::create($validated);
        
        return response()->json([
            'success' => true
        ]);
    }

    public function update(Request $request, $sid)
    {
        $validated = $request->validate([
            'fname' => 'required',
            'lname' => 'required',
            'birthplace' => 'required',
            'birthDate' => 'required|date'
        ]);

        $student = Student::findOrFail($sid);
        $student->update($validated);
        
        return response()->json([
            'success' => true
        ]);
    }

    public function destroy($sid)
    {
        $student = Student::findOrFail($sid);
        $student->delete();
        
        return response()->json([
            'success' => true
        ]);
    }
}
