<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Blog</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        html, body {
            height: 100%; /* Set height of html and body to 100% */
            margin: 0; /* Remove default margin */
            display: flex;
            flex-direction: column; /* Make the body a flex container */
        }

        .container {
            flex-grow: 1; /* Allow the content to take up remaining space */
        }

        footer {
            margin-top: auto; /* Push the footer to the bottom */
            background-color: #f8f9fa;
            padding-top: 15px;
            padding-bottom: 15px;
        }
    </style>
</head>

<body>

    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/posts') }}">Blog App</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    {{-- Show logged-in user's name --}}
                    @auth
                    <li class="nav-item">
                        <span class="navbar-text">Hello, {{ Auth::user()->name }}</span>
                    </li>
                    {{-- Logout functionality --}}
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link">Log Out</button>
                        </form>
                    </li>
                    @endauth

                    {{-- Login link for guests --}}
                    @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        {{-- Content Section --}}
        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="text-center">
        <p class="mb-0">&copy; {{ date('Y') }} Your Company. All rights reserved.</p>
    </footer>

    {{-- Bootstrap JS and JQuery --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

</body>

</html>
