<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting to ConnectIPS...</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 500px;
            width: 100%;
        }
        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
        }
        h2 {
            color: #333;
            margin-bottom: 10px;
        }
        p {
            color: #666;
            margin-bottom: 30px;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .info {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 14px;
            color: #666;
        }
        .info strong {
            color: #333;
        }
        .debug-info {
            margin-top: 20px;
            padding: 15px;
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 8px;
            font-size: 12px;
            color: #856404;
            text-align: left;
            max-height: 200px;
            overflow-y: auto;
        }
        .debug-info code {
            display: block;
            margin: 5px 0;
            padding: 5px;
            background: white;
            border-radius: 4px;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">🔒</div>
        <h2>Redirecting to ConnectIPS</h2>
        <p>Please wait while we securely redirect you to the payment gateway...</p>
        <div class="spinner"></div>
        <div class="info">
            <strong>Conference:</strong> {{ $conference->conference_name }}<br>
            <strong>Amount:</strong> NPR {{ number_format($formData['TXNAMT'] / 100, 2) }}<br>
            <strong>Transaction ID:</strong> {{ $formData['TXNID'] }}
        </div>
        
        <div class="debug-info">
            <strong>Debug Information:</strong><br>
            <code>Gateway URL: {{ $formData['action_url'] }}</code>
            <code>Merchant ID: {{ $formData['MERCHANTID'] }}</code>
            <code>App ID: {{ $formData['APPID'] }}</code>
            <code>Reference ID: {{ $formData['REFERENCEID'] }}</code>
        </div>
    </div>

    <!-- ConnectIPS Payment Form -->
    <form id="connectipsForm" action="{{ $formData['action_url'] }}" method="POST" style="display: none;">
        <input type="hidden" name="MERCHANTID" value="{{ $formData['MERCHANTID'] }}">
        <input type="hidden" name="APPID" value="{{ $formData['APPID'] }}">
        <input type="hidden" name="APPNAME" value="{{ $formData['APPNAME'] }}">
        <input type="hidden" name="TXNID" value="{{ $formData['TXNID'] }}">
        <input type="hidden" name="TXNDATE" value="{{ $formData['TXNDATE'] }}">
        <input type="hidden" name="TXNCRNCY" value="{{ $formData['TXNCRNCY'] }}">
        <input type="hidden" name="TXNAMT" value="{{ $formData['TXNAMT'] }}">
        <input type="hidden" name="REFERENCEID" value="{{ $formData['REFERENCEID'] }}">
        <input type="hidden" name="REMARKS" value="{{ $formData['REMARKS'] }}">
        <input type="hidden" name="PARTICULARS" value="{{ $formData['PARTICULARS'] }}">
        <input type="hidden" name="TOKEN" value="{{ $formData['TOKEN'] }}">
    </form>

    <script>
        console.log('ConnectIPS Payment Details:');
        console.log('Action URL:', '{{ $formData['action_url'] }}');
        console.log('Merchant ID:', '{{ $formData['MERCHANTID'] }}');
        console.log('App ID:', '{{ $formData['APPID'] }}');
        console.log('Transaction ID:', '{{ $formData['TXNID'] }}');
        console.log('Amount (Paisa):', '{{ $formData['TXNAMT'] }}');
        console.log('Reference ID:', '{{ $formData['REFERENCEID'] }}');
        
        // Try to submit the form
        setTimeout(function() {
            console.log('Attempting to submit form to ConnectIPS...');
            try {
                document.getElementById('connectipsForm').submit();
            } catch (error) {
                console.error('Form submission error:', error);
                alert('Error submitting form. Please check the console for details.\n\nThe ConnectIPS UAT server might be down or your network might be blocking the connection.\n\nGateway URL: {{ $formData['action_url'] }}');
            }
        }, 2000);
    </script>
</body>
</html>
