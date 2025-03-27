<html>
<head>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <title>Site Maintenance</title>
</head>
<body>
  <div class="hero bg-base-200 min-h-screen">
    <div class="hero-content text-center">
      <div class="max-w-md">
        <h1 class="text-5xl font-bold text-error">Under Maintenance</h1>
        <div class="py-4">
          <x-heroicon-s-exclamation-circle class="w-16 h-16 text-error m-auto" />
        </div>
        <p class="py-6">
          We're currently deploying some changes to improve your experience. 
          The site will be back online shortly. Please check back in a few minutes.
          We apologize for any inconvenience.
        </p>
        <button class="btn btn-outline" onclick="window.location.reload()">Refresh Page</button>
      </div>
    </div>
  </div>
</body>
</html>
