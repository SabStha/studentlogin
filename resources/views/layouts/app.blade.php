<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AIWA FARM')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
        }
        .header {
            background-color: #fff;
            padding: 15px 30px;
            border-bottom: 2px solid #0066cc;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header a {
            color: #0066cc;
            text-decoration: none;
            margin-left: 20px;
            font-size: 14px;
        }
        .header a:hover {
            text-decoration: underline;
        }
        .nav-links {
            display: flex;
            gap: 20px;
        }
        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
            padding: 5px;
        }
        .hamburger span {
            width: 25px;
            height: 3px;
            background-color: #0066cc;
            margin: 3px 0;
            transition: 0.3s;
        }
        .mobile-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background-color: #fff;
            border-bottom: 2px solid #0066cc;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        .mobile-menu.active {
            display: block;
        }
        .mobile-menu a {
            display: block;
            padding: 15px 30px;
            color: #0066cc;
            text-decoration: none;
            border-bottom: 1px solid #f0f0f0;
        }
        .mobile-menu a:hover {
            background-color: #f5f5f5;
        }
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            .hamburger {
                display: flex;
            }
            .header {
                position: relative;
            }
        }
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .card {
            background-color: #fff;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background-color: #0066cc;
            color: white;
        }
        .btn-primary:hover {
            background-color: #0052a3;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn-pink {
            background-color: #ff6b9d;
            color: white;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .student-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .student-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 10px;
            background-color: #e0e0e0;
        }
        .student-info {
            font-size: 14px;
            line-height: 1.6;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-2year { background-color: #0066cc; color: white; }
        .status-1year { background-color: #28a745; color: white; }
        .status-fail { background-color: #dc3545; color: white; }
        .status-waiting { background-color: #ffc107; color: #333; }
        .search-form {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .search-form .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            align-items: end;
        }
        .search-form .form-group {
            flex: 1;
            margin-bottom: 0;
        }
        .statistics {
            display: flex;
            gap: 30px;
            margin: 20px 0;
        }
        .stat-item {
            font-size: 16px;
            color: #333;
        }
        .legend {
            display: flex;
            gap: 20px;
            margin: 20px 0;
            flex-wrap: wrap;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .checkbox-group input[type="checkbox"] {
            width: auto;
        }
    </style>
</head>
<body>
    @auth
    <div class="header">
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">ログアウト</a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
        <div class="nav-links">
            <a href="{{ route('dashboard') }}">生徒検索</a>
            <a href="{{ route('students.create') }}">生徒登録</a>
            <a href="{{ route('schools.index') }}">学校検索</a>
            <a href="{{ route('schools.create') }}">学校登録</a>
        </div>
        <div class="hamburger" id="hamburger-menu" onclick="toggleMobileMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="mobile-menu" id="mobile-menu">
            <a href="{{ route('dashboard') }}" onclick="closeMobileMenu()">生徒検索</a>
            <a href="{{ route('students.create') }}" onclick="closeMobileMenu()">生徒登録</a>
            <a href="{{ route('schools.index') }}" onclick="closeMobileMenu()">学校検索</a>
            <a href="{{ route('schools.create') }}" onclick="closeMobileMenu()">学校登録</a>
        </div>
    </div>
    @endauth

    <div class="container">
        @if(session('success'))
            <div class="card" style="background-color: #d4edda; border-color: #c3e6cb; color: #155724;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="card" style="background-color: #f8d7da; border-color: #f5c6cb; color: #721c24;">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('active');
        }

        function closeMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.remove('active');
        }

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            const hamburger = document.getElementById('hamburger-menu');
            const menu = document.getElementById('mobile-menu');
            
            if (!hamburger.contains(event.target) && !menu.contains(event.target)) {
                menu.classList.remove('active');
            }
        });
    </script>
</body>
</html>

