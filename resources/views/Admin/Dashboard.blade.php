<x-layouts.app title="Dashboard">

    <style>
        /* ===== Header Styling ===== */
        .dashboard-header {
            position: sticky;
            top: 0;
            z-index: 999;
            background: #ffffff;
            border-bottom: 1px solid #e5e5e5;
        }

        .dashboard-header .title {
            font-weight: 700;
            font-size: 2rem;
        }

        .stats-card {
            border-radius: 20px;
            transition: .2s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .table thead {
            background: #1e3c72;
            color: white;
        }
    </style>

    {{-- HEADER --}}
    <div class="dashboard-header p-3 shadow-sm d-flex justify-content-between align-items-center">
        <div>
            <h1 class="title mb-0">Dashboard</h1>
            <small class="text-muted">Welcome to the Grade Evaluation System</small>
        </div>

        <form method="POST" action={{route('logout')}}>
            @csrf
            <button type="submit" class="btn btn-outline-danger btn-sm">Logout</button
        </form>
    </div>

    <div class="container-fluid p-4">

        {{-- STATS CARDS --}}
        <div class="row g-4 mb-4">

            <div class="col-md-3">
                <div class="card stats-card bg-primary text-white shadow">
                    <div class="card-body">
                        <h6 class="text-uppercase fw-bold">Total Teachers</h6>
                        <p class="display-6 fw-bold mb-0">{{$countedTeacher}}</p>
                    </div>
                </div>
            </div>
        </div>

        {{--  success message here --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- TEACHER LIST TABLE --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Teacher List</h5>
                <a href="{{ route('create-teacher') }}" class="btn btn-primary btn-sm">Add New Teacher</a>
            </div>

            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Full Name</th>
                                <th>Username</th>
                                <th>Section</th>
                                <th>Subject</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @if ($teacher_list && $teacher_list)

                                @foreach ($teacher_list as $index => $teacher)
                                    <tr>
                                        <td>{{ $index + 1}}</td>
                                        <td>{{ $teacher->full_name }}</td>
                                        <td>{{ $teacher->username }}</td>
                                        <td>{{ $teacher->section }}</td>
                                        <td>{{ $teacher->subject }}</td>
                                        <td>
                                            <a href="#" class="btn btn-warning btn-sm me-1">Edit</a>
                                            <a href="#" class="btn btn-danger btn-sm">Delete</a>
                                        </td>
                                    </tr>
                                @endforeach
                                
                            @endif

                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
</x-layouts.app>
