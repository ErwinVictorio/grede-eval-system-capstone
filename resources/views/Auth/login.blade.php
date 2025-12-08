{{-- Login Page using Bootstrap --}}
<x-layouts.app>

    <div class="container d-flex justify-content-center align-items-center vh-100">

        <div class="card p-4 shadow-lg" style="width: 22rem; border-radius: 16px;">
            <h3 class="card-title text-center mb-3">Login</h3>

            {{-- Display Login Error (e.g., invalid credentials) --}}
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Display Validation Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.process') }}">
                @csrf

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="username" 
                        name="username" 
                        value="{{ old('username') }}"
                        required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="password" 
                        name="password" 
                        required>
                </div>

                <button type="submit" class="btn btn-primary w-100 mt-2">Login</button>
            </form>

        </div>

    </div>

</x-layouts.app>
