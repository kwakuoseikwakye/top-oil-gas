<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container-fluid h-100" style="margin-top: 100px;">
    <div class="row h-100">
        <!-- Illustration Column -->
        <div class="col-md-7 d-flex justify-content-center align-items-center">
            <img src="{{asset('login.svg')}}" alt="Login Illustration" class="img-fluid">
        </div>
        
        <!-- Login Form Column -->
        <div class="col-md-5 d-flex justify-content-center align-items-center">
            
            <form method="POST" class="w-75" action="{{ route('login') }}">
                {{-- <img src="{{asset("img/petro.png")}}" style="width: 100px; height:100px;" alt=""> --}}
                <h2 class="text-center mb-4">Top Oil Admin Login</h2>
                @csrf
                <!-- Email Field -->
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email" name="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="Enter email">
                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                
                <!-- Password Field -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" class="@error('password') is-invalid @enderror form-control" id="password" placeholder="Password">
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                
                <!-- Login Button -->
                <button type="submit" class="btn btn-warning btn-block">Login</button>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
