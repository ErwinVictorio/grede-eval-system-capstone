<x-layouts.app title="Teacher Dashboard">

    {{-- GOOGLE FONTS + GOOGLE ICONS --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" rel="stylesheet" />

    <style>
        /* Apply fonts globally */
        body {
            background: #f5f7fb;
            font-family: 'Inter', 'Poppins', sans-serif !important;
        }

        .material-symbols-rounded {
            font-variation-settings:
                'FILL' 0,
                'wght' 500,
                'GRAD' 0,
                'opsz' 40;
            vertical-align: middle;
            font-size: 22px;
            margin-right: 8px;
        }

        /* ===== SIDEBAR ===== */
        .sidebar-wrapper {
            background: #ffffff;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        }

        .sidebar-user-box {
            background: #f2f6ff;
            border: 1px solid #e4edff;
            padding: 16px;
            border-radius: 12px;
            font-weight: 600;
            text-align: center;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 10px 12px;
            border-radius: 8px;
            color: #4a4a4a;
            font-weight: 500;
            transition: 0.2s;
            text-decoration: none;
        }

        .sidebar-menu a:hover {
            background: #e8efff;
            color: #0d6efd;
            transform: translateX(4px);
        }

        /* ===== CARDS ===== */
        .card {
            border-radius: 18px;
            border: none;
            overflow: hidden;
            box-shadow: 0 6px 26px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            background: transparent;
            border: none;
            padding: 18px 22px;
        }

        .table thead th {
            background: #f3f6fb;
            border-bottom: 2px solid #edf1f7;
            font-weight: 600;
        }

        .btn-primary {
            border-radius: 12px;
            padding: 8px 20px;
            font-weight: 600;
        }

        .btn-outline-primary {
            border-radius: 10px;
            font-weight: 500;
        }
    </style>

    <div class="container-fluid p-4">

        <!-- Header -->
        <div class="p-3">
            <div class="col-md-6">
                <h3 class="fw-bold mb-0">
                    Good day , {{ Auth::user()->full_name ?? 'Teacher' }}
                </h3>
            </div>

        </div>

        @php
        $cards = [
        [
        'label' => 'Attendance',
        'value' => $percentage->attendance_weight ?? 0,
        'icon' => 'check_circle',
        'color' => '#28a745',
        ],
        [
        'label' => 'Quiz',
        'value' => $percentage->quiz_weight ?? 0,
        'icon' => 'edit_note',
        'color' => '#ffc107',
        ],
        [
        'label' => 'Exam',
        'value' => $percentage->exam_weight ?? 0,
        'icon' => 'assignment',
        'color' => '#dc3545',
        ],

        [
        'label' => 'Activities',
        'value' => $percentage->activity_weight ?? 0,
        'icon' => 'edit_note',
        'color' => '#dc3545',
        ],
        [
        'label' => 'Recitaion',
        'value' => $percentage->recitation_weight ?? 0,
        'icon' => 'check_circle',
        'color' => '#dc3545',
        ],

        [
        'label' => 'Projects',
        'value' => $percentage->project_weight ?? 0,
        'icon' => 'assignment',
        'color' => '#dc3545',
        ],
        ];
        @endphp


        <!-- Summary Cards -->
        <div class="row mb-4">

            <!-- Total Students -->
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Total Students</p>
                                <h4 class="fw-bold mb-0">24</h4>
                            </div>
                            <div style="font-size: 32px; color: #0d6efd;">
                                <span class="material-symbols-rounded">groups</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @foreach ($cards as $card)
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">{{ $card['label'] }}</p>
                                <h4 class="fw-bold mb-0">{{ $card['value'] }}%</h4>
                            </div>
                            <div style="font-size: 32px; color: {{ $card['color'] }};">
                                <span class="material-symbols-rounded">{{ $card['icon'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

        </div>


        <div class="row">

            <!-- SIDEBAR -->
            <aside class="col-lg-3 mb-4">
                <div class="sidebar-wrapper">
                    <div class="sidebar-user-box mb-4">
                        {{ Auth::user()->full_name ?? 'Teacher Name' }}
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="mb-4">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100" style="border-radius: 8px;">
                            <span class="material-symbols-rounded" style="font-size: 18px;">logout</span>
                            Logout
                        </button>
                    </form>

                    <div class="sidebar-menu">
                        <a href="{{ route('attendance.show') }}">
                            <span class="material-symbols-rounded">checklist</span>
                            Take Attendance
                        </a>

                        <a href="{{ route('quiz.show') }}">
                            <span class="material-symbols-rounded">edit_note</span>
                            Record Quiz
                        </a>

                        <a href="{{ route('exam.show') }}">
                            <span class="material-symbols-rounded">assignment</span>
                            Record Exam
                        </a>

                        <a href="{{ route('activity.show') }}">
                            <span class="material-symbols-rounded">menu_book</span>
                            Record Activity
                        </a>

                        <a href="{{ route('project.show') }}">
                            <span class="material-symbols-rounded">school</span>
                            Projects
                        </a>

                        <a href="{{ route('recitation.show') }}">
                            <span class="material-symbols-rounded">record_voice_over</span>
                            Record Recitation
                        </a>

                        <a href="{{ route('teacher.settings') }}">
                            <span class="material-symbols-rounded">tune</span>
                            Grade Allocation
                        </a>

                        <!-- Removed placeholder link that was using href="#" and caused confusion -->


                    </div>
                </div>
            </aside>

            <!-- MAIN CONTENT -->
            <section class="col-lg-9">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold">Student List</h5>
                        <a href="{{ route('add-student') }}" class="btn btn-sm btn-outline-primary">
                            <span class="material-symbols-rounded" style="font-size: 18px;">person_add</span>
                            Add Student
                        </a>
                    </div>

                    {{-- success message --}}
                    @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Section</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @forelse($students as $student)
                                    <tr>
                                        <td>{{ $student->full_name }}</td>
                                        <td>{{ $student->section }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('student.report', $student->id) }}"
                                                class="btn btn-sm btn-info me-2" title="View Report">
                                                <span class="material-symbols-rounded"
                                                    style="font-size: 16px;">assessment</span> Report
                                            </a>
                                            <a href="#" class="btn btn-sm btn-warning me-2">Edit</a>
                                            <a href="#" class="btn btn-sm btn-danger">Delete</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-5">
                                            No students found.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </section>

        </div>
    </div>




</x-layouts.app>