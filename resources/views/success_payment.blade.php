<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
            40% {transform: translateY(-30px);}
            60% {transform: translateY(-15px);}
        }

        .fade-in {
            animation: fadeIn 2s;
        }

        .bounce {
            animation: bounce 2s infinite;
        }
    </style>
    <title>Payment Successful</title>
</head> 
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5 fade-in">
                    <div class="card-body">
                        <h2 class="text-center text-success"><i class="fas fa-check-circle"></i> Payment Successful!</h2>
                        <p class="text-center">Thank you for your purchase. Your transaction has been completed, and a receipt for your purchase has been emailed to you.</p>
                        <div class="d-flex justify-content-center">
                            <a href="#" class="btn btn-primary bounce"><i class="fas fa-tachometer-alt"></i> Go to Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>