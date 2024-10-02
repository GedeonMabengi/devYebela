<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exigences</title>
</head>
<body>
    <h1>Entrer les Exigences</h1>
    <form action="{{ route('requirements.update') }}" method="POST">
        @csrf
        <textarea name="requirements" placeholder="Entrez les exigences ici..."></textarea>
        <button type="submit">Soumettre</button>
    </form>
</body>
</html>
