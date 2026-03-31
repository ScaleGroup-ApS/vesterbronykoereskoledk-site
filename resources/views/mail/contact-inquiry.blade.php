<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ny henvendelse</title>
</head>
<body style="font-family: ui-sans-serif, system-ui, sans-serif; line-height: 1.5; color: #0f172a;">
    <p style="margin: 0 0 1rem;">Der er en ny henvendelse fra kontaktformularen på websitet.</p>
    <table style="border-collapse: collapse; width: 100%; max-width: 32rem;">
        <tr>
            <td style="padding: 0.35rem 0; font-weight: 600; vertical-align: top; width: 10rem;">Navn</td>
            <td style="padding: 0.35rem 0;">{{ $inquiry->name }}</td>
        </tr>
        <tr>
            <td style="padding: 0.35rem 0; font-weight: 600; vertical-align: top;">E-mail</td>
            <td style="padding: 0.35rem 0;"><a href="mailto:{{ $inquiry->email }}">{{ $inquiry->email }}</a></td>
        </tr>
        @if($inquiry->phone)
            <tr>
                <td style="padding: 0.35rem 0; font-weight: 600; vertical-align: top;">Telefon</td>
                <td style="padding: 0.35rem 0;">{{ $inquiry->phone }}</td>
            </tr>
        @endif
        <tr>
            <td style="padding: 0.35rem 0; font-weight: 600; vertical-align: top;">Pakke</td>
            <td style="padding: 0.35rem 0;">{{ $inquiry->offer?->name ?? '—' }}</td>
        </tr>
        <tr>
            <td style="padding: 0.35rem 0; font-weight: 600; vertical-align: top;">Ønsket holdstart</td>
            <td style="padding: 0.35rem 0;">{{ $holdStartLabel ?? '—' }}</td>
        </tr>
    </table>
    @if($inquiry->message)
        <p style="margin: 1.25rem 0 0.35rem; font-weight: 600;">Besked</p>
        <p style="margin: 0; white-space: pre-wrap;">{{ $inquiry->message }}</p>
    @endif
</body>
</html>
