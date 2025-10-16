   <!DOCTYPE html>
   <html lang="en">

   <head>
       <meta charset="UTF-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <meta http-equiv="X-UA-Compatible" content="ie=edge">
       <meta name="csrf-token" content="{{ csrf_token() }}">

       <title>Accept Conference Invitation</title>

       <!-- Bootstrap CSS -->
       <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

       <!-- Font Awesome -->
       <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

       <!-- SweetAlert2 CSS -->
       <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
   </head>

   <body>
       <div class="container py-5">
           <div class="row justify-content-center">
               <div class="col-md-6">
                   <div class="card shadow">
                       <div class="card-body text-center py-5">
                           <div class="mb-4">
                               <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                           </div>
                           <h4 class="card-title">Invitation Link Expired</h4>
                           <p class="card-text text-muted">{{ $message }}</p>
                           <p>If you believe this is an error, please contact the conference organizers.</p>
                           <a href="{{ route('home') }}" class="btn btn-primary">
                               <i class="fas fa-home me-2"></i>Return to Home
                           </a>
                       </div>
                   </div>
               </div>
           </div>
       </div>
       <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
       <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
       <!-- SweetAlert2 -->
       <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


   </body>

   </html>
