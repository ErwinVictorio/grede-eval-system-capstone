<x-layouts.app title="Teacher Dashboard">
   <div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Grade Weight Settings</h5>
                    <small>Set percentage allocation (Total must be 100%)</small>
                </div>

                <div class="card-body">
                    
                    {{-- Validation Error --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('teacher.settings.update') }}">
                        @csrf
        

                        {{-- Quiz --}}
                        <div class="mb-3">
                            <label class="form-label">Quiz (%)</label>
                            <input type="number" class="form-control"
                                   name="quiz_weight"
                                   value="{{ old('quiz_weight', $settings->quiz_weight) }}"
                                   min="0" max="100" required>
                        </div>

                        {{-- Exam --}}
                        <div class="mb-3">
                            <label class="form-label">Exam (%)</label>
                            <input type="number" class="form-control"
                                   name="exam_weight"
                                   value="{{ old('exam_weight', $settings->exam_weight) }}"
                                   min="0" max="100" required>
                        </div>

                        {{-- Activity --}}
                        <div class="mb-3">
                            <label class="form-label">Activity (%)</label>
                            <input type="number" class="form-control"
                                   name="activity_weight"
                                   value="{{ old('activity_weight', $settings->activity_weight) }}"
                                   min="0" max="100" required>
                        </div>

                        {{-- Project --}}
                        <div class="mb-3">
                            <label class="form-label">Project (%)</label>
                            <input type="number" class="form-control"
                                   name="project_weight"
                                   value="{{ old('project_weight', $settings->project_weight) }}"
                                   min="0" max="100" required>
                        </div>

                        {{-- Recitation --}}
                        <div class="mb-3">
                            <label class="form-label">Recitation (%)</label>
                            <input type="number" class="form-control"
                                   name="recitation_weight"
                                   value="{{ old('recitation_weight', $settings->recitation_weight) }}"
                                   min="0" max="100" required>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">
                                Total must equal <strong>100%</strong>
                            </span>
                            <button type="submit" class="btn btn-primary">
                                Save Settings
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
</x-layouts.app>