<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ApiController extends Controller
{
    public function getRequirements()
    {
        return Inertia::render('Home');
    }

    public function sendRequirements(Request $request)
    {
        $description = $request->description;
        $type = $request->type;

        $prompt = "Génère moi une description textuelle UML de type $type pour les descriptions des exigences suivantes: " . $description;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
                ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key='
                    . env('GEMINI_API_KEY'), [
                    'contents' => [
                    'parts' => [
                    [
                        'text' => $prompt,
                    ],
                ],
            ],
        ]);

        $responseBody = $response->json();

        // Convertir le contenu de la réponse en JSON formaté pour une meilleure lisibilité
        $formattedResponse = json_encode($responseBody, JSON_PRETTY_PRINT);

        // Définir le chemin du fichier
        $filePath = 'public/response.txt';

        // Écrire le contenu dans le fichier
        Storage::put($filePath, $formattedResponse);

        // Retourner la réponse JSON
        return response()->json(['responseBody' => $responseBody]);
    }

    public function firstRequirement()
    {
    // Définir le chemin relatif du fichier par rapport au disque de stockage par défaut
    $filePath = 'public/response.txt';

    // Vérifier si le fichier existe
    if (Storage::exists($filePath)) {
        // Lire le contenu du fichier
        $content = Storage::get($filePath);

        // Retourner le contenu du fichier en JSON
        return response()->json(['content' => json_decode($content)]);
    } else {
        // Retourner une erreur si le fichier n'existe pas
        return response()->json(['error' => 'File not found'], 404);
    }
    }



    public function updateRequirements($requirements)
    {
        // Formater les exigences pour créer un prompt de bonne qualité
        $prompt = "Génère moi une description textuelle UML de type $type pour les descriptions des exigences suivantes: " . implode(', ', $requirements);

        return $prompt;
    }

    public function sendRequirementsXXX($prompt)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=' . env('GEMINI_API_KEY'), [
            'contents' => [
                'parts' => [
                    [
                        'text' => $prompt,
                    ],
                ],
            ],
        ]);

        $responseBody = $response->json();

        return response()->json(['responseBody' => $responseBody]);

        // Stocker l'ID de la requête pour suivre la progression
        $requestId = $response->json()['request_id'];

        return $requestId;
    }

    public function getResponse($requestId)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->get(env('GEMINI_API_KEY') . '/' . $requestId);

        // Extraire le code Mermaid et le stocker dans un fichier temporaire
        $mermaidCode = $response->json()['mermaid_code'];
        $tmpFilePath = $this->writeMermaidCodeToFile($mermaidCode);

        return $tmpFilePath;
    }

    public function sendRequirementsForEpuration($mermaidCode)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post(env('GEMINI_API_KEY') . '/epurate', [
            'mermaid_code' => $mermaidCode,
        ]);

        // Stocker l'ID de la requête pour suivre la progression
        $requestId = $response->json()['request_id'];

        return $requestId;
    }

    public function getRequirementsForEpuration($requestId)
    {
        // ... (similaire à getResponse)
    }

    public function writeMermaidCodeToFile($mermaidCode)
    {
        // Créer un nom de fichier temporaire unique
        $tmpFile = tempnam(sys_get_temp_dir(), 'mermaid_');

        // Écrire le code Mermaid dans le fichier
        file_put_contents($tmpFile, $mermaidCode);

        return $tmpFile;
    }

    public function mermaidWork($tmpFilePath)
    {
        // Initialiser Mermaid Live Editor
        $mermaid = new Mermaid();

        // Charger le code Mermaid depuis le fichier temporaire
        $mermaidCode = file_get_contents($tmpFilePath);

        // Générer l'image SVG
        $svg = $mermaid->render($mermaidCode);

        // Enregistrer l'image SVG dans un fichier (ou l'afficher directement)
        $svgFilePath = 'path/to/your/svg/file.svg';
        file_put_contents($svgFilePath, $svg);
    }
}
    // public function getRequirements(Request $request)
    // {
    //     // Récupérer les exigences de l'utilisateur depuis la requête
    //     $requirements = $request->input('requirements');

    //     // Valider les exigences (si nécessaire)
    //     // ...

    //     return $requirements;
    // }