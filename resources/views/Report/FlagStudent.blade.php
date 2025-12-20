<x-layouts.app>
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0">
                        <li class="breadcrumb-item"><a href={{route('Dashboard.teacher')}} class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item active fw-bold" aria-current="page text-primary">Flag for Counseling</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mt-2">Counseling Referral</h3>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <span class="material-symbols-rounded text-primary" style="font-size: 40px;">person</span>
                        </div>
                        <h5 class="fw-bold mb-1">{{ $student->full_name ?? "No Student Name" }}</h5>
                        <p class="text-muted small mb-3">Section: {{ $student->section ?? "No Section" }}</p>
                    
                        <hr class="my-4 opacity-25">
                        
                        <div class="text-start">
                            <label class="small text-muted d-block mb-1">Current Subject</label>
                            <div class="d-flex align-items-center">
                                <span class="material-symbols-rounded me-2 text-warning">menu_book</span>
                                <span class="fw-semibold">{{ Auth::user()->subject ?? "No Subject" }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold mb-0">Evaluation Details</h5>
                        <p class="text-muted small">Please provide the reasons for this referral.</p>
                    </div>
                    <div class="card-body p-4">
                        <form action={{route('flag.submit')}} method="POST">
                            @csrf
                            <input type="hidden" name="student_id" value="{{ $student->id ?? "No Student Id" }}">
                            <input type="hidden" name="teacher_id" value="{{ Auth::id() }}">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Referral Category</label>
                                    <select name="category" class="form-select border-0 bg-light rounded-3 p-3 shadow-none" required>
                                        <option value="" selected disabled>Select Category</option>
                                        <option value="Academic Performance">Academic Performance</option>
                                        <option value="Behavioral Issue">Behavioral Issue</option>
                                        <option value="Attendance">Attendance</option>
                                        <option value="Personal / Family">Personal / Family</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Urgency Status</label>
                                    <div class="d-flex gap-2 mt-1">
                                        <input type="radio" class="btn-check" name="status" id="low" value="Low" autocomplete="off">
                                        <label class="btn btn-outline-success border-2 rounded-pill grow" for="low">Low</label>

                                        <input type="radio" class="btn-check" name="status" id="mid" value="Mid" autocomplete="off" checked>
                                        <label class="btn btn-outline-warning border-2 rounded-pill grow" for="mid">Mid</label>

                                        <input type="radio" class="btn-check" name="status" id="high" value="High" autocomplete="off">
                                        <label class="btn btn-outline-danger border-2 rounded-pill grow" for="high">High</label>
                                    </div>
                                </div>

                                <div class="col-12 mt-4">
                                    <label class="form-label fw-semibold">Specific Factors / Observations</label>
                                    <textarea name="comments" rows="4" class="form-control border-0 bg-light rounded-3 p-3 shadow-none" 
                                        placeholder="Example: Student is constantly sleeping in class or using phone during quiz..." required></textarea>
                                </div>

                                <div class="col-12 mt-4 d-flex justify-content-end gap-2">
                                    <a href="/dashboard" class="btn btn-light rounded-pill px-4 fw-semibold text-muted">Cancel</a>
                                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold d-flex align-items-center">
                                        <span class="material-symbols-rounded me-2" style="font-size: 18px;">send</span>
                                        Submit Referral
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        body { background-color: #f8f9fa; }
        .card { transition: transform 0.2s ease; }
        .form-select, .form-control { border: 1px solid #eee !important; }
        .form-select:focus, .form-control:focus { background-color: #fff !important; border-color: #0d6efd !important; }
        .btn-primary { background-color: #0d6efd; border: none; }
        .btn-check:checked + .btn-outline-danger { background-color: #dc3545; color: white; }
        .btn-check:checked + .btn-outline-warning { background-color: #ffc107; color: #000; }
        .btn-check:checked + .btn-outline-success { background-color: #198754; color: white; }
    </style>
</x-layouts.app>