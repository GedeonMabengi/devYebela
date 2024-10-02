<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DiagramController extends Controller
{
    public function lgenerateDiagram()
    {
        // 
    }

    public function generateDiagram(Request $request)
    {
        // Validation des données du formulaire
        $validatedData = $request->validate([
            'description' => 'required|string',
            'type' => 'required|string',
        ]);
    
        // Récupération des données validées
        $description = $validatedData['description'];
        $type = $validatedData['type'];
    
        // Construction du prompt initial
        $promptString = "Génère moi une description textuelle UML de type $type
            pour les descriptions des exigences suivantes : " . $description.
            "Présente ta réponse sans autres commentaires ou recommandations sur le sujet,
            Juste La réponse et rien d'autres.";
    
        // Envoi de la requête à Gemini pour la première demande
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key='
            . env('GEMINI_API_KEY'), [
                'contents' => [
                    'parts' => [
                        [
                            'text' => $promptString,
                        ],
                    ],
                ],
            ]);
    
            // Récupération de la réponse et extraction du texte
            $responseBody = $response->json();
            Log::info('Response Body:', $responseBody);
    
            if (isset($responseBody['candidates'][0]['content']['parts'][0]['text'])) {
                $promptDescription = $responseBody['candidates'][0]['content']['parts'][0]['text'];
            } else {
                Log::error('Unexpected response structure:', $responseBody);
                return response()->json(['error' => 'La structure de la réponse est inattendue.']);
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de la requête à Gemini:', ['exception' => $e]);
            return response()->json(['error' => 'Une erreur s\'est produite : ' . $e->getMessage()]);
        }
    
        // Construction du second prompt
        $promptString = "Génère moi du code mermaid pour les description textuelle UML suivante $promptDescription.
        Le code doit être entre les caractères <!-- et -->.";
    
        // Envoi de la requête à Gemini pour la seconde demande
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key='
            . env('GEMINI_API_KEY'), [
                'contents' => [
                    'parts' => [
                        [
                            'text' => $promptString,
                        ],
                    ],
                ],
            ]);
    
            // Récupération de la réponse et extraction du texte
            $responseBody = $response->json();
            Log::info('Response Body:', ['responseBody' => $responseBody]);
    
            if (isset($responseBody['candidates'][0]['content']['parts'][0]['text'])) {
                $mermaidCode = $responseBody['candidates'][0]['content']['parts'][0]['text'];
    
                // Filtrer le code Mermaid
                preg_match('/<!--(.*?)-->/s', $mermaidCode, $matches);
                if (isset($matches[1])) {
                    $mermaidCode = trim($matches[1]);
    
                    // Enregistrer le code Mermaid dans un fichier temporaire
                    $tempFilePath = storage_path('app/public/temp/mermaid_code.mmd');
                    file_put_contents($tempFilePath, $mermaidCode);
    
                    // Générer l'image du diagramme avec Mermaid CLI
                    $imagePath = storage_path('app/public/diagrams/' . uniqid() . '.png');
                    $command = "mmdc -i $tempFilePath -o $imagePath";
                    exec($command, $output, $return_var);
                    Log::info(['command' => $command, 'output' => $output, 'return_var' => $return_var]);
    
                    if ($return_var !== 0) {
                        Log::error('Mermaid CLI command failed:', ['command' => $command, 'output' => $output, 'return_var' => $return_var]);
                        return response()->json(['error' => 'La commande Mermaid CLI a échoué.']);
                    }
    
                    $promptData = [
                        'description' => $promptDescription,
                        'codeMermaid' => $mermaidCode,
                        'imagePath' => $imagePath,
                    ];
                
                    // Création de l'enregistrement
                    $prompt = Prompt::create($promptData);
                
                    // Renvoyer l'image et la description à la vue
                    return response()->json([
                        'description' => $prompt->description,
                        'diagramImage' => asset('storage/diagrams/' . basename($imagePath))
                    ]);
    
                } else {
                    Log::error('Mermaid code not found in response:', ['responseBody' => $responseBody]);
                    return response()->json(['error' => 'Le code Mermaid n\'a pas été trouvé dans la réponse.']);
                }
            } else {
                Log::error('Unexpected response structure:', ['responseBody' => $responseBody]);
                return response()->json(['error' => 'La structure de la réponse est inattendue.']);
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de la requête à Gemini:', ['exception' => $e]);
            return response()->json(['error' => 'Une erreur s\'est produite : ' . $e->getMessage()]);
        }
    }    
    
    public function showResponse()
    {
        // Récupérer le dernier enregistrement de la base de données
        $prompt = Prompt::latest()->first();
        
        if ($prompt) {
            return response()->json(['responseText' => $prompt->description]);
        } else {
            return response()->json(['responseText' => 'Aucune réponse enregistrée.']);
        }
    }
        
    public function sendFistRequest(Request $request)
    {
        // Validation des données du formulaire
        $validatedData = $request->validate([
            'description' => 'required|string',
            'type' => 'required|string',
        ]);
    
        // Récupération des données validées
        $description = $validatedData['description'];
        $type = $validatedData['type'];
    
        // Création d'une nouvelle instance de Prompt
        $prompt = new Prompt;
    
        // Construction du prompt initial
        $promptString = "Génère moi une description textuelle UML de type $type
            pour les descriptions des exigences suivantes : " . $description.
            "Présente ta réponse sans autres commentaires ou recommendation sur le sujet,
            Juste La reponse et rien d'autres.";
    
        // Envoi de la requête à Gemini
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key='
            .env('GEMINI_API_KEY'), [
                'contents' => [
                    'parts' => [
                        [
                            'text' => $promptString,
                        ],
                    ],
                ],
            ]);
    
            // Récupération de la réponse et extraction du texte
            $responseBody = $response->json();
            Log::info('Response Body:', $responseBody);
    
            if (isset($responseBody['candidates'][0]['content']['parts'][0]['text'])) {
                $prompt->description = $responseBody['candidates'][0]['content']['parts'][0]['text'];
    
                // Journaliser le contenu du modèle avant l'enregistrement
                // dd($prompt->toArray());
    
                $prompt->save();
    
                // Ajouter la réponse à la session
                session()->flash('responseText', $responseBody['candidates'][0]['content']['parts'][0]['text']);
    
                return redirect()->back();
            } else {
                Log::error('Unexpected response structure:', $responseBody);
                return redirect()->back()->with('error', 'La structure de la réponse est inattendue.');
            }
        } catch (\Exception $e) {
            // Log de l'erreur et redirection avec message d'erreur
            Log::error('Erreur lors de l\'envoi de la requête à Gemini:', ['exception' => $e]);
            return redirect()->back()->with('error', 'Une erreur s\'est produite : ' . $e->getMessage());
        }
    }
    
    public function secondRequest()
    {
            $prompt = Prompt::latest()->first();
    
            if ($prompt) {
                // Assurez-vous que $prompt1 est défini
                $textInPrompt = $prompt->description;
    
                // Construction du second prompt
                $promptString = "Génère moi du code mermaid pour les description textuelle UML suivante $textInPrompt.";
    
                // Envoi de la requête à Gemini
                try {
                    $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key='
                    . env('GEMINI_API_KEY'), [
                        'contents' => [
                            'parts' => [
                                [
                                    'text' => $promptString,
                                ],
                            ],
                        ],
                    ]);
    
                    // Récupération de la réponse et extraction du texte
                    $responseBody = $response->json();
                    Log::info('Response Body:', ['responseBody' => $responseBody]);
    
                    if (isset($responseBody['candidates'][0]['content']['parts'][0]['text'])) {
                        $prompt->code_mermaid = $responseBody['candidates'][0]['content']['parts'][0]['text'];
    
                        // Journaliser le contenu du modèle avant l'enregistrement
                        // dd($prompt->toArray());
    
                        $prompt->save();
    
                        // Ajouter la réponse à la session
                        session()->flash('responseText', $responseBody['candidates'][0]['content']['parts'][0]['text']);
    
                        return redirect()->back();
                    } else {
                        Log::error('Unexpected response structure:', ['responseBody' => $responseBody]);
                        return redirect()->back()->with('error', 'La structure de la réponse est inattendue.');
                    }
                } catch (\Exception $e) {
                    // Log de l'erreur et redirection avec message d'erreur
                    Log::error('Erreur lors de l\'envoi de la requête à Gemini:', ['exception' => $e]);
                    return redirect()->back()->with('error', 'Une erreur s\'est produite : ' . $e->getMessage());
                }
    
            } else {
                return response()->json(['responseText' => 'Aucune réponse enregistrée.']);
            }
    }
    
    public function generatePrompt()
    {
            $prompt = Prompt::latest()->first();
    
            if ($prompt) {
                // Assurez-vous que $prompt1 est défini
                $textInPrompt = $prompt->description;
    
                // Construction du second prompt
                $promptString = "Génère moi du code mermaid pour les description textuelle UML suivante $textInPrompt.
                Le code doit être entre les caractères <!-- et -->.";
    
                // Envoi de la requête à Gemini
                try {
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                    ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key='
                    . env('GEMINI_API_KEY'), [
                        'contents' => [
                            'parts' => [
                                [
                                    'text' => $promptString,
                                ],
                            ],
                        ],
                    ]);
    
                    // Récupération de la réponse et extraction du texte
                    $responseBody = $response->json();
                    Log::info('Response Body:', ['responseBody' => $responseBody]);
    
                    if (isset($responseBody['candidates'][0]['content']['parts'][0]['text'])) {
                        $mermaidCode = $responseBody['candidates'][0]['content']['parts'][0]['text'];
    
                        // Filtrer le code Mermaid
                        preg_match('/<!--(.*?)-->/s', $mermaidCode, $matches);
                        if (isset($matches[1])) {
                            $mermaidCode = trim($matches[1]);
    
                            // Enregistrer le code Mermaid dans un fichier temporaire
                            $tempFilePath = storage_path('app/public/temp/mermaid_code.mmd');
                            file_put_contents($tempFilePath, $mermaidCode);
    
                            // Enregistrer le code Mermaid dans la base de données
                            $prompt->code_mermaid1 = $mermaidCode;
                            $prompt->save();
    
                            // Générer l'image du diagramme avec Mermaid CLI
                            $imagePath = storage_path('app/public/diagrams/' . uniqid() . '.png');
                            $command = "mmdc -i $tempFilePath -o $imagePath";
                            exec($command, $output, $return_var);
    
                            if ($return_var !== 0) {
                                Log::error('Mermaid CLI command failed:', ['command' => $command, 'output' => $output, 'return_var' => $return_var]);
                                return response()->json(['error' => 'La commande Mermaid CLI a échoué.']);
                            }
    
                            // Enregistrer le chemin de l'image dans la base de données
                            $prompt->image_path = $imagePath;
                            $prompt->save();
    
                            // Renvoyer l'image à la vue
                            return response()->json(['diagramImage' => asset('storage/diagrams/' . basename($imagePath))]);
                        } else {
                            Log::error('Mermaid code not found in response:', ['responseBody' => $responseBody]);
                            return response()->json(['error' => 'Le code Mermaid n\'a pas été trouvé dans la réponse.']);
                        }
                    } else {
                        Log::error('Unexpected response structure:', ['responseBody' => $responseBody]);
                        return response()->json(['error' => 'La structure de la réponse est inattendue.']);
                    }
                } catch (\Exception $e) {
                    // Log de l'erreur et redirection avec message d'erreur
                    Log::error('Erreur lors de l\'envoi de la requête à Gemini:', ['exception' => $e]);
                    return response()->json(['error' => 'Une erreur s\'est produite : ' . $e->getMessage()]);
                }
            } else {
                return response()->json(['error' => 'Aucune réponse enregistrée.']);
            }
    }
    
        
    public function index()
    {
        $prompts = Prompt::all(); // Récupère tous les enregistrements de la table `prompts`
        return inertia('PromptList', [
            'prompts' => $prompts,
        ]);
    }
    
    public function show($id)
    {
            $prompt = Prompt::findOrFail($id);
    
            return response()->json($prompt);
    }
}
