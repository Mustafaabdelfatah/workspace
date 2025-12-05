<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Layout</title>
</head>
<body style="margin:0; padding:0; font-family: Arial, sans-serif; background-color: #f9f9f9;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 30px 0;">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="85%" style="
               background: linear-gradient(135deg, #faf7f0 0%, #dbeaf8 100%); 
                border-radius: 20px; padding: 40px; text-align: center;">
                    
                    <!-- Logo -->
                    <tr>
                        <td style="padding-bottom: 20px;">
                            <img src="https://laravel.nahidh.sa/assets/images/logonahid%201.png" alt="Nahidh Logo" width="120">
                        </td>
                    </tr>

                    <!-- Title -->
                    <tr>
                        <td style="font-size: 22px; font-weight: bold; color: #1f3b64; padding-bottom: 20px;">

                      {{ $subject ?? $options['subject'] ?? $options['Subject'] ?? ''}}
                        </td>
                    </tr>

                    <!-- Message -->
                    <tr>
                        <td style="font-size: 15px; line-height: 24px; color: #333; text-align: left;">
                            <br>
                           @yield('body')
                        </td>
                    </tr>

                    @if(isset($options['button_text']))
                    <!-- Button -->
                    <tr>
                        <td align="center">
                            <br>
                            <a href="{{ $options['button_url'] ?? '#' }}" 
                               style="display:block; padding: 14px 32px; background-color: #fff; color: #6D7171; 
                                      font-size: 16px; font-weight: bold; border-radius: 8px; text-decoration: none;">
                                {{ $options['button_text'] }}
                            </a>
                        </td>
                    </tr>
                    @endif

                    <!-- Footer -->
                  
                </table>
            </td>
            <tr>
                        <td align="center" style="padding-top: 40px; font-size: 13px; color: #1f3b64; line-height: 20px;">
                            Kingdom of Saudi Arabia - Riyadh - Salah Ad <br>
                            Din Ayyubi Rd, Al Malaz
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding-top: 20px;">
                            <img src="https://laravel.nahidh.sa/assets/images/logonahid%201.png" alt="Nahidh Logo" width="120">
                        </td>
                    </tr>

        </tr>
    </table>
</body>
</html>
