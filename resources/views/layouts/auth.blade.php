<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-image: url('https://images.unsplash.com/photo-1542838132-92d4037c77b9?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
        }
        .auth-container {
            background-color: rgba(255, 255, 255, 0.9); 
            min-height: 100vh;
            padding: 20px;
        }
        .login-container { 
            max-width: 800px; 
            margin: 50px auto; 
            background-color: #fff; 
            border-radius: 10px; 
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .keypad-btn { 
            width: 90px; 
            height: 90px; 
            font-size: 18px; 
            margin: 5px; 
			color: #222222;
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }
        .keypad-btn:hover {
            background-color: #e9ecef;
        }
        .input-group { 
            margin-bottom: 15px; 
        }
        .input-group-text { 
            font-size: 1.2rem; 
            width: 130px; 
            background-color: #f1f3f5;
        }
        .form-control { 
            font-size: 1.2rem; 
            height: 50px; 
            background-color: #fff;
        }
        .btn-login { 
            background-color: #28a745; 
            border-color: #28a745; 
            font-size: 1.2rem; 
        }
        .btn-close { 
            background-color: #ff6f91; 
            border-color: #ff6f91; 
            color: #fff; 
            font-size: 1.2rem; 
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="auth-container">
        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Password toggle functionality
            $('.password-toggle').on('click', function() {
                const input = $(this).siblings('input');
                const icon = $(this).find('i');
                
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Keypad functionality
            let selectedInput = null;
            document.querySelectorAll('input').forEach(input => {
                input.addEventListener('focus', () => selectedInput = input);
            });

            window.keypadInput = function(key) {
                if (!selectedInput) return;
                if (key === 'SİL') {
                    selectedInput.value = '';
                } else if (key === '←') {
                    selectedInput.value = selectedInput.value.slice(0, -1);
                } else {
                    selectedInput.value += key;
                }
            };
        });
    </script>
    @stack('scripts')
</body>
</html>