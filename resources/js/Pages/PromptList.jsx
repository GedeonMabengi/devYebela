import React from 'react';

const PromptsList = ({ prompts }) => {
  return (
    <div className="bg-gray-100 text-gray-900 min-h-screen">
      <header className="bg-blue-500 text-white p-4">
        <h1 className="text-4xl font-bold text-center">Liste des Prompts</h1>
      </header>

      <main className="container mx-auto p-4">
        <div className="overflow-x-auto">
          <table className="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
            <thead className="bg-gray-800 text-white">
              <tr>
                <th className="py-2 px-4 border-b border-gray-700">ID</th>
                <th className="py-2 px-4 border-b border-gray-700">Description</th>
                <th className="py-2 px-4 border-b border-gray-700">Code Mermaid</th>
                <th className="py-2 px-4 border-b border-gray-700">Image</th>
                <th className="py-2 px-4 border-b border-gray-700">Image</th>
              </tr>
            </thead>
            <tbody>
              {prompts.map(prompt => (
                <tr key={prompt.id} className="hover:bg-gray-100 h-32">
                  <td className="py-2 px-4 border-b border-gray-200 h-32">{prompt.id}</td>
                  <td className="py-2 px-4 border-b border-gray-200 h-32">{prompt.description}</td>
                  <td className="py-2 px-4 border-b border-gray-200 h-32">{prompt.codeMermaid}</td>
                  <td className="py-2 px-4 border-b border-gray-200 h-32">{prompt.imagePath}</td>
                  <td className="py-2 px-4 border-b border-gray-200 h-32">
                    {prompt.image_path && (
                      <img src={prompt.image_path} alt="Diagramme" className="w-32 h-32" />
                    )}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </main>
    </div>
  );
};

export default PromptsList;
