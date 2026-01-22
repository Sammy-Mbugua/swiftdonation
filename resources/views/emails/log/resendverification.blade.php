<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .logo-layer {
            background-color: #ffffff;
            padding: 20px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }

        .logo {
            margin-bottom: 20px;
            text-align: center;
        }

        .message {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .footer {
            background-color: #333333;
            color: #ffffff;
            padding: 10px;
        }
    </style>
</head>

<body>
    <table class="container" cellpadding="0" cellspacing="0">
        <tr>
            <td class="logo-layer">
                <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/lotus.webp"
                    alt="Your Logo" class="logo" width="150">
            </td>
        </tr>
        <tr>
            <td class="message">
                <h2>Greetings!</h2>

                <p>Please click the link below to activate your account. </p>
                    <br/>

                    <!-- button large and center -->
                    <a href="{!! $details['verification'] !!}"
                        style="display: block; width: 200px; margin: 0 auto; padding: 10px 20px; background-color: #333333; color: #ffffff; text-align: center; text-decoration: none; border-radius: 5px;">
                        Confirm Email
                    </a>
                    <!-- add link without button -->
                <p>
                    Or click/copy the following link: <a href="{!! $details['verification'] !!}">
                        {!! $details['verification'] !!} </a>
                </p>
                <br />
                <p>Our dedicated support team is readily available 24/7 to address any inquiries you may have. Feel free
                    to respond
                    directly to this email, or utilize our convenient live chat feature accessible within the control
                    panel.</p>

                <p>
                    Sincerely, <br />
                    The swiftoakdonations.com Team
                </p>
            </td>
        </tr>
        <tr>
            <td class="footer">
                &copy; {{ date('Y') }} Your Swift Oak Donations. All rights reserved.
            </td>
        </tr>
    </table>
</body>

</html>
