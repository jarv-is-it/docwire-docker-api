<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jarv.is.it - DocWire REST Api</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: rgb(23, 25, 35);
            color: white;
        }

        .container {
            width: 768px;
            margin: 30px auto;
        }

        .text-center {
            text-align: center;
        }

        blockquote {
            margin-left: 2em;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-center">Jarv.is.it - DocWire REST Api</h1>
        <p>You can access the API without authentication.<br>

            A sync conversion can be executed using the following details:</p>

        <p>
            <b>Url:</b> <em>/api/v1/convert/sync</em><br>
            <b>Method:</b> <em>POST</em><br>
            <b>Headers:</b>
        <blockquote>
            <em>Content-Type: application/json</em><br>
            <em>Accept: application/json</em><br>
        </blockquote>
        <b>JSON payload:</b>
        <blockquote>
            <b>filename</b> <em>required</em> The filename to be converted (e.g. sample.pdf).<br>
            <b>content</b> <em>required</em> The base64 encoded content of the file.<br>
            <b>output</b> <em>optional</em> The expected output. Accepted values: plain_text, html, csv or metadata. Default value: plain_text<br>
            <b>language</b> <em>optional</em> The ISO 639-3 identifiers like used by the OCR (e.g. ita, eng, fra, spa, deu, etc.). Default value: eng<br>
        </blockquote>
        <b>Example payload:</b>
        <blockquote>
            <pre>
{
    "filename": "example.txt",
    "content": "SGVsbG8gd29ybGQh",
    "output": "plain_text",
    "language": "eng"
}
        </pre>
        </blockquote>
        </p>

        <p>An async conversion can be executed using the following details:</p>

        <p>
            <b>Url:</b> <em>/api/v1/convert/async</em><br>
            <b>Method:</b> <em>POST</em><br>
            <b>Headers:</b>
        <blockquote>
            <em>Content-Type: application/json</em><br>
            <em>Accept: application/json</em><br>
        </blockquote>
        <b>JSON payload:</b>
        <blockquote>
            <b>filename</b> <em>required</em> The filename to be converted (e.g. sample.pdf).<br>
            <b>content</b> <em>required</em> The base64 encoded content of the file.<br>
            <b>output</b> <em>optional</em> The expected output. Accepted values: plain_text, html, csv or metadata. Default value: plain_text<br>
            <b>language</b> <em>optional</em> The ISO 639-3 identifiers like used by the OCR (e.g. ita, eng, fra, spa, deu, etc.). Default value: eng<br>
            <b>identifier</b> <em>required</em> A unique identifier you provide for the conversion (e.g. 7561b5ec-fd69-46e1-9356-220782a6b312).<br>
            <b>webhook</b> <em>required</em> A webhook url that will be called after the conversion is completed (e.g. https://www.example.com/webhook/conversion).<br>
        </blockquote>
        <b>Example payload:</b>
        <blockquote>
            <pre>
{
    "filename": "example.txt",
    "content": "SGVsbG8gd29ybGQh",
    "output": "plain_text",
    "language": "eng",
    "identifier": "7561b5ec-fd69-46e1-9356-220782a6b312",
    "webhook": "https://www.example.com/webhook/conversion"
}
        </pre>
        </blockquote>
        </p>
    </div>
</body>

</html>
