<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Redirecting to Payment...</title>
  <!-- Meta refresh redirect - không gây CORS issues -->
  <meta http-equiv="refresh" content="0;url={{ $stripeUrl }}">
</head>

<body>
  <div style="text-align: center; padding: 50px; font-family: Arial, sans-serif;">
    <div style="display: flex; justify-content: center; align-items: center; flex-direction: column; height: 50vh;">
      <div
        style="border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin-bottom: 20px;">
      </div>
      <h2>Redirecting to secure payment...</h2>
      <p>Please wait, you will be redirected automatically.</p>
      <p>If not redirected, <a href="{{ $stripeUrl }}">click here</a>.</p>
    </div>
  </div>

  <style>
    @keyframes spin {
      0% {
        transform: rotate(0deg);
      }

      100% {
        transform: rotate(360deg);
      }
    }
  </style>

  <script>
    // Backup JavaScript redirect
    setTimeout(function () {
      window.location.replace("{{ $stripeUrl }}");
    }, 500);
  </script>
</body>

</html>