<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Component</title>
   <link
      href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css"
      rel="stylesheet"
   />
   <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
   <style>
      body {
         font-family: 'Montserrat', sans-serif;
      }
   </style>
</head>
<body class="m-5">
   <!-- Button -->
   <div class="flex gap-4 mb-5">
         <h2>Button</h2>
         <button class="w-1/5 bg-blue-500 hover:bg-blue-600 rounded-md px-4 py-2 text-white font-semibold shadow">Primary</button>
         <button class="w-1/5 bg-red-500 hover:bg-red-600 rounded-md px-4 py-2 text-white font-semibold shadow">Danger</button>
         <button class="w-1/5 bg-yellow-500 hover:bg-yellow-600 rounded-md px-4 py-2 text-white font-semibold shadow">Warning</button>

         <button class="flex items-center justify-center gap-2 w-32 bg-blue-500 hover:bg-blue-600 rounded-md px-4 py-2 text-white font-semibold shadow">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
               <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Primary
         </button>

         <button class="w-32 bg-transparent rounded-md px-4 py-2 text-blue-800 border-2 border-blue-500 font-semibold shadow">Primary</button>

   </div>
   <!-- Button -->

   <!-- Field Input -->
   <div class="flex gap-4 mb-5">
         <h2>Text Input</h2>
         <input type="text" name="username" class=" w-1/5 px-4 py-2 border border-blue-200 rounded-md shadow" placeholder="Username" required autocomplete="off"/>
         <input type="password" name="password" class=" w-1/5 px-4 py-2 border border-blue-200 rounded-md shadow" placeholder="Password" required autocomplete="off"/>
   </div>
   <div class="flex gap-4 mb-5">
      <h2>Label Input</h2>
         <div class="relative w-1/5">
            <label for="username" class="left-2 text-sm bg-white px-1 text-gray-600 font-medium">Username</label>
            <input 
               type="text" 
               id="username" 
               name="username" 
               class="w-full px-4 py-2 border border-blue-200 rounded-md shadow focus:outline-none focus:ring-2 focus:ring-blue-400" 
               placeholder="Enter your username" 
               required 
               autocomplete="off" 
            />
         </div>
         <div class="relative w-1/5">
            <label for="username" class=" left-2 text-sm bg-white px-1 text-gray-600">
               Username <span class="text-red-500">*</span>
            </label>
            <input 
               type="text" 
               id="username" 
               name="username" 
               class="w-full px-4 py-2 border border-blue-200 rounded-md shadow focus:outline-none focus:ring-2 focus:ring-blue-400" 
               placeholder="Enter your username" 
               required 
               autocomplete="off" 
            />
         </div>
   </div>
   <!-- Field Input -->

   <!-- Card -->
      <h2>Card</h2>
      <div class="grid grid-cols-3 gap-6 mt-5">
         <div class="bg-blue-500 text-white p-4 rounded-xl shadow-lg">
            <h3 class="text-lg font-bold">Total Users</h3>
            <p class="text-2xl mt-2">1,024</p>
         </div>
         <div class="bg-green-600 text-white p-4 rounded-xl shadow-lg">
            <h3 class="text-lg font-bold">Revenue</h3>
            <p class="text-2xl mt-2">$24,500</p>
         </div>
         <div class="bg-red-600 text-white p-4 rounded-xl shadow-lg">
            <h3 class="text-lg font-bold">Issues</h3>
            <p class="text-2xl mt-2">8</p>
         </div>
      </div>

   <!-- Card -->

   <!-- Modal -->


</body>
</html>