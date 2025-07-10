<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Request for Presentation Type Change</title>
</head>

<body>
    <div>
        <h3>Dear {{ $data['namePrefix'] . ' ' . $data['presenter_name'] }},</h3>
    </div>
    <br>
    <div>
        <p>We sincerely thank you for submitting your abstract for the
            {{ $data['presentation_type'] == 1 ? 'Poster' : 'Oral' }} Presentation at the upcoming
            {{ $data['conferenceTheme'] }}.</p>
        <p>
            We regret to inform you that your abstract has not been selected for an
            {{ $data['presentation_type'] == 1 ? 'poster' : 'oral' }} presentation.
            However, we encourage you to present it as a {{ $data['presentation_type'] == 1 ? 'oral' : 'poster' }}
            presentation. If you are willing to do so,
            please let us know within 24 hours. If you choose to proceed with the
            {{ $data['presentation_type'] == 1 ? 'oral' : 'poster' }} presentation, we
            will send you the relevant guidelines. If we do not receive a response from you within the
            given timeframe, we will assume that you have opted not to present.
        </p>
        {{-- <p>
            If you decide not to present your paper as a {{ $data['presentation_type'] == 1 ? 'oral' : 'poster' }}, we
            still encourage you to attend the
            summit in person. You may register through our website. If you are unable to join us
            physically, we also offer the option to attend the summit virtually. You can register through
            our online portal.
        </p> --}}
        {{-- <p>For any queries, feel free to contact us at <a href="mailto:summit@nhrc.gov.np">summit@nhrc.gov.np</a></p> --}}
    </div>
    <br>
    <div>
        <p>Best Regards,</p>
        <p>SAFOGCON 2025</p>

        {{-- <p>Email: <a href="mailto:summit@nhrc.gov.np">summit@nhrc.gov.np</a></p> --}}
    </div>
</body>

</html>
