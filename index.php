<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KosKozy</title>
    <link
      href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css"
      rel="stylesheet"
    />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<style>
    body {
        font-family: 'Poppins', sans-serif;
    }
</style>

<body class="flex items-center justify-center min-h-screen bg-gradient-to-r from-indigo-500 to-blue-500">
    <div class="flex flex-col md:flex-row max-w-3xl w-full bg-white rounded-lg shadow-xl">
        <!-- Left Section - Login Form -->
        <div class="w-full md:w-1/2 p-8 justify-center">
            <h2 class="text-4xl font-bold text-center text-blue-600 mb-4">
                KOSKOZY
            </h2>

            <!-- Form Login -->
            <form action="login_process.php" method="POST">
                <div class="form-control mb-4">
                  <input type="text" name="username" class="w-full px-4 py-2 border border-blue-200 rounded-md shadow" placeholder="Username" required autocomplete="off"/>
                </div>
                <div class="form-control mb-4">
                  <input type="password" name="password" class="w-full px-4 py-2 border border-blue-200 rounded-md shadow" placeholder="Password" required autocomplete="off"/>
                </div>

                <!-- Tampilkan pesan kesalahan jika ada -->
                <?php if (isset($_GET['error'])): ?>
                    <p class="text-red-500 text-center mb-4"><?php echo htmlspecialchars($_GET['error']); ?></p>
                <?php endif; ?>

                <div class="form-control mt-6">
                  <button class="w-full bg-blue-500 hover:bg-blue-600 rounded-md px-4 py-2 text-white font-medium shadow">
                      Login
                  </button>
                </div>
                <!-- <p class="text-center mt-4">
                    <a href="#" class="text-sm text-blue-500 hover:underline">Lupa Password?</a>
                </p> -->
            </form>
        </div>

        <!-- Right Section - Image -->
        <div class="hidden md:block md:w-1/2 bg-blue-600 rounded-r-lg  items-center justify-center">
            <img
              src="assets/logo/Kozie.png"
              alt="KosKozy"
              class="object-cover h-full rounded-r-lg"
            />
        </div>
    </div>
</body>
</html>
