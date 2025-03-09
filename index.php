<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KosKozy - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-r from-indigo-500 to-blue-500 px-4">
    <div class="flex flex-col md:flex-row max-w-3xl w-full bg-white rounded-lg shadow-2xl overflow-hidden">
        <!-- Left Section - Login Form -->
        <div class="w-full md:w-1/2 p-10 flex flex-col justify-center">
            <h2 class="text-4xl font-bold text-center text-blue-600 mb-6 uppercase">
                KosKozy
            </h2>
            <form action="login_process.php" method="POST">
                <div class="mb-4">
                    <input type="text" name="username" class="w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Username" required autocomplete="off"/>
                </div>
                <div class="mb-4">
                    <input type="password" name="password" class="w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Password" required autocomplete="off"/>
                </div>
                
                <!-- Tampilkan pesan kesalahan jika ada -->
                <?php if (isset($_GET['error'])): ?>
                    <p class="text-red-500 text-center mb-4"><?php echo htmlspecialchars($_GET['error']); ?></p>
                <?php endif; ?>
                
                <div class="mt-6">
                    <button class="w-full bg-blue-500 hover:bg-blue-600 transition duration-300 rounded-md px-4 py-3 text-white font-semibold shadow-md">
                        Login
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Right Section - Image -->
        <div class="hidden md:flex md:w-1/2 items-center justify-center">
            <img src="assets/logo/Kozie.png" alt="KosKozy" class="object-contain h-64 w-auto">
        </div>
    </div>
</body>
</html>
