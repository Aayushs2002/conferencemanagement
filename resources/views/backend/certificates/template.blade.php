<!DOCTYPE html>
<html>

<head>
    <!-- styles same as before -->
</head>

<body>
    <div class="certificate">
        <div class="title">Certificate of Participation</div>
        <div>This certifies that</div>
        <div class="name">{{ $registrant->user->fullName($registrant->user) }}</div>
        <div>has participated in the conference</div>
        <div class="conference-name">{{ $conference->conference_name }}</div>
        {{-- <div class="footer">{{ $conference->end_date->format('F j, Y') }}</div> --}}
    </div>
</body>

</html>
