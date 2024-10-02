import React, { useState, useEffect } from 'react';// l'imoprtation de react
import { Inertia } from '@inertiajs/inertia';// l'importation de innertia js
import ReactMarkdown from 'react-markdown'; // Importer ReactMarkdown
import mermaid from 'mermaid';// Importation de mermaid

const DiagramDrawer = () => {
  const [requirements, setRequirements] = useState('');//la fonction qui modifie le composant qui affiche les exigences fonctionnelle
  const [diagramType, setDiagramType] = useState('class');
  const [responseTextState, setResponseTextState] = useState('');
  const [mermaidCode, setMermaidCode] = useState('');//la fonction qui modifie le composant qui affiche le code mermaid

  useEffect(() => {
    mermaid.initialize({ startOnLoad: true });
  }, []);

  useEffect(() => {
    if (mermaidCode) {
      mermaid.contentLoaded();
    }
  }, [mermaidCode]);

  const handleSubmit = (e) => {// est la fonction qui est appeler quand on envoie le formulaire
    e.preventDefault();

    Inertia.post('/firstRequest', {
      description: requirements,
      type: diagramType,
    }, {
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
      onSuccess: (page) => {
        setResponseTextState(''); // Réinitialiser la réponse après la soumission
      },
      onError: (errors) => {
        console.error('Une erreur est survenue!', errors);
      },
    });
  };

  const handleViewResponse = async () => {// la fonction appeller pour afficher la description textuelle
    try {
      const response = await fetch('/showResponse', {
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
      });
      const data = await response.json();
      setResponseTextState(data.responseText);
    } catch (error) {
      console.error('Une erreur est survenue!', error);
      setResponseTextState('Une erreur est survenue lors de la demande.');
    }
  };

  const handleGenerateDiagram = async () => {// la fonction appeller pour renvoyer le code mermaid a la vue enfin que le diagramme soit generer depuis la vue
    try {
        const response = await fetch('/generatePrompt', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({
                description: requirements,
                type: diagramType,
            }),
        });
        const data = await response.json();
        console.log('Response data:', data);
        setResponseTextState(data.description);
        setMermaidCode(data.mermaidCode);
    } catch (error) {
        console.error('Une erreur est survenue!', error);
        setMermaidCode('');
    }
  };

  return (
    <div className='bg-black absolute top-0 bottom-0 right-0 left-0 h-max'>

        <header className=" text-white p-2 z-30 fixed top-2 left-8 right-8 shadow backdrop-blur-md bg-gray-400/5
          rounded-custom mr-2 ml-2">
          <h1 className="text-4xl font-bold text-center" translate='no'>Mukolo code</h1>
        </header>

      <div className="text-gray-900 border-white border-8 mr-6 ml-6 mt-[70px] h-max">

        <div className='flex justify-center items-center mt-9'>
          <div className='backdrop-blur-md bg-gray-400/5 h-60 w-4/5 rounded-2xl overflow-y-scroll text-white'>
            
            <section>{/* la section qui affichera la description textuelle*/}
              <h2 className="text-2xl text-white font-semibold m-2">La description textuelle</h2>
            
              {responseTextState ? (
                <ReactMarkdown className="prose hi">{responseTextState}</ReactMarkdown> // Utilisation de ReactMarkdown
              ) : (
                <button onClick={handleViewResponse} className="m-2 bg-green-400 text-white py-2 px-4 rounded-md 
                  hover:bg-green-500">
                  Voir la description textuelle
                </button>
              )}
            </section>

            <section>
              <h2 className="text-2xl text-white font-semibold m-2">Le diagramme correspondant</h2>
                
              <button onClick={handleGenerateDiagram} className="m-2 bg-purple-400 text-white py-2 px-4 rounded-md hover:bg-purple-500">
                  Voir le diagramme
                </button>
                
                {mermaidCode && (
                  <div className="mermaid mt-4" translate='no'>
                    {mermaidCode}{/*le code mermaid est cencer s'afficher ici precisement*/} 
                  </div>
                )}
            </section>

          </div>
        </div>

        <main className="container mx-auto h-max">

          <form id="requirementsForm" className="mb-6 absolue bottom-1" onSubmit={handleSubmit}>
              
            <div className='flex items-center justify-center pr-5 pl-5 w-full'>
              <div className='w-3/4'>
              
            <select
              id="diagramType"
              name="diagramType"
              className="w-full mb-2 inline-block p-2 border border-gray-300 rounded-md
              backdrop-blur-md bg-gray-400/5 text-white mt-3"
              value={diagramType}
              onChange={(e) => setDiagramType(e.target.value)}
              >
              <option value="classe">Diagramme de classe</option>
              <option value="sequence">Diagramme de séquence</option>
              <option value="état">Diagramme d'état</option>
            </select>

              <textarea
                id="requirements"
                name="requirements"
                className=" w-full h-32 p-2 border border-gray-300 rounded-md backdrop-blur-md bg-gray-400/5 text-white"
                value={requirements}
                onChange={(e) => setRequirements(e.target.value)}
                placeholder='Entrer vos exigences fonctionnelle ici ...
                ex: -Calcul des cotes'
              ></textarea>
              </div>

                <button type="submit" className="block m-2 bg-blue-400 text-white py-2 px-4 rounded-md hover:bg-accent">
                  Soumettre
                </button>
                
            </div>

          </form>
        </main>
      </div>
    </div>
  );
};

export default DiagramDrawer;
