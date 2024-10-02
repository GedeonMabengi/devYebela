import React, { useState } from 'react';
import { Inertia } from '@inertiajs/inertia';
import ReactMarkdown from 'react-markdown'; // Importer ReactMarkdown

function GeminiDiagram() {
  const [requirements, setRequirements] = useState('');
  const [diagramType, setDiagramType] = useState('flowchart');
  const [mermaidCode, setMermaidCode] = useState('');
  const [diagramImage, setDiagramImage] = useState(null);
  const [darkMode, setDarkMode] = useState(false);
  const [viewMode, setViewMode] = useState('description'); // 'description' or 'image'
  const [responseTextState, setResponseTextState] = useState('');

  const handleSubmit = (e) => {
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

  const handleGenerateDiagram = async () => {
    try {
      const response = await fetch('/generateDiagram', {
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
      setResponseTextState(data.description);
      setDiagramImage(data.diagramImage);
    } catch (error) {
      console.error('Une erreur est survenue!', error);
      setDiagramImage(null);
    }
  };

  const toggleDarkMode = () => {
    setDarkMode(!darkMode);
  };

  const toggleViewMode = (mode) => {
    setViewMode(mode);
  };

  return (
    <div className={`min-h-screen p-4 ${darkMode ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-900'}`}>
      <header className="flex justify-between items-center mb-8">
        <h1 className="text-3xl font-bold">GeminiDiagram</h1>
        <div className="flex space-x-4">
          <button
            className="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
            onClick={() => toggleViewMode('description')}
          >
            Voir la description
          </button>
          <button
            className="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
            onClick={() => toggleViewMode('image')}
          >
            Voir l'image
          </button>
          <button
            className="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
            onClick={toggleDarkMode}
          >
            {darkMode ? (
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor" className="size-6">
                <path strokeLinecap="round" strokeLinejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
              </svg>
            ) : (
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor" className="size-6">
                <path strokeLinecap="round" strokeLinejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
              </svg>
            )}
          </button>
        </div>
      </header>
      <main className="max-w-3xl mx-auto">
        <section className="mb-8">
          <h2 className="text-2xl font-semibold mb-4">Enter Functional Requirements</h2>
          <form id="requirementsForm" onSubmit={handleSubmit}>
            <textarea
              className="w-full h-40 p-4 border border-gray-300 rounded bg-gray-100 text-gray-900"
              value={requirements}
              onChange={(e) => setRequirements(e.target.value)}
              placeholder="Enter your functional requirements here..."
            ></textarea>
            <section className="mb-8">
              <h2 className="text-2xl font-semibold mb-4">Select Diagram Type</h2>
              <select
                className="w-full p-4 border border-gray-300 rounded bg-white text-gray-900"
                value={diagramType}
                onChange={(e) => setDiagramType(e.target.value)}
              >
                <option value="flowchart">Flowchart</option>
                <option value="sequence">Sequence Diagram</option>
                <option value="class">Class Diagram</option>
              </select>
            </section>
            <section className="mb-8">
              <button
                className="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                onClick={handleGenerateDiagram}
              >
                Generate Diagram
              </button>
            </section>
          </form>
        </section>
        {responseTextState && viewMode === 'description' && (
          <section className="mb-8">
            <h2 className="text-2xl font-semibold mb-4">Mermaid Code</h2>
            <ReactMarkdown className="prose">{responseTextState}</ReactMarkdown>
          </section>
        )}
        {diagramImage && viewMode === 'image' && (
          <section>
            <h2 className="text-2xl font-semibold mb-4">Generated Diagram</h2>
            <div className="glassmorphism-card p-4 rounded">
              <img src={diagramImage} alt="Generated Diagram" className="w-full h-auto" />
            </div>
          </section>
        )}
      </main>
    </div>
  );
}

export default GeminiDiagram;