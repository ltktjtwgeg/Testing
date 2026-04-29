/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import React, { useState } from 'react';
import { Download, FileCode, CheckCircle, Database, LayoutTemplate, ShieldCheck, TerminalSquare } from 'lucide-react';

export default function App() {
  const [activeTab, setActiveTab] = useState('instructions');

  const files = [
    'README.txt',
    'database.sql',
    'config.php',
    'index.php',
    'dashboard.php',
    'logout.php',
    'deposit.php',
    'withdraw.php',
    'game.php',
    'api_balance.php',
    'admin/index.php',
    'admin/deposits.php',
    'admin/withdrawals.php',
    'admin/users.php',
    'admin/settings.php',
    'style.css'
  ];

  return (
    <div className="min-h-screen bg-gray-50 flex flex-col font-sans">
      {/* Header */}
      <header className="bg-blue-900 text-white p-6 shadow-md">
        <div className="max-w-5xl mx-auto flex justify-between items-center">
          <div>
            <h1 className="text-3xl font-bold flex items-center gap-2">
              <FileCode className="w-8 h-8" /> Aviator PHP Platform
            </h1>
            <p className="text-blue-200 mt-2">Ready to deploy to Hostinger or any shared hosting.</p>
          </div>
          <div className="flex bg-blue-800 rounded-lg p-3 items-center gap-3 border border-blue-700">
             <Download className="text-blue-300" />
             <div>
               <p className="text-sm text-blue-200 uppercase font-bold tracking-wider">How to export</p>
               <p className="text-sm">Click "Export to ZIP" in settings.</p>
             </div>
          </div>
        </div>
      </header>

      {/* Main content */}
      <main className="max-w-5xl mx-auto w-full flex-grow p-6 flex flex-col md:flex-row gap-6 mt-4">
        
        {/* Sidebar */}
        <aside className="w-full md:w-64 flex flex-col gap-2 shrink-0">
          <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4">
            <h3 className="font-semibold text-gray-800 uppercase tracking-wider text-sm mb-3">Project Overview</h3>
            <ul className="text-sm text-gray-600 space-y-2">
              <li className="flex gap-2 items-center"><CheckCircle className="w-4 h-4 text-green-500"/> PHP & MySQL</li>
              <li className="flex gap-2 items-center"><LayoutTemplate className="w-4 h-4 text-green-500"/> HTML/CSS Frontend</li>
              <li className="flex gap-2 items-center"><TerminalSquare className="w-4 h-4 text-green-500"/> Frontend JS Game logic</li>
              <li className="flex gap-2 items-center"><ShieldCheck className="w-4 h-4 text-green-500"/> Provably Fair System</li>
              <li className="flex gap-2 items-center"><Database className="w-4 h-4 text-green-500"/> Safe SQL injection setup</li>
            </ul>
          </div>
          
          <h3 className="font-semibold text-gray-700 uppercase tracking-wider text-sm mb-2 px-2">Navigation</h3>
          <button 
            onClick={() => setActiveTab('instructions')}
            className={`text-left px-4 py-2 rounded-md font-medium transition-colors ${activeTab === 'instructions' ? 'bg-blue-100 text-blue-800' : 'text-gray-600 hover:bg-gray-100'}`}
          >
            Setup Instructions
          </button>
          <div className="text-xs font-bold text-gray-400 mt-4 mb-2 uppercase px-2 tracking-wider">Generated Files</div>
          <div className="flex flex-col gap-1 overflow-y-auto max-h-96">
            {files.map(file => (
              <button 
                key={file}
                onClick={() => setActiveTab(file)}
                className={`text-left px-4 py-2 rounded-md text-sm transition-colors tabular-nums ${activeTab === file ? 'bg-blue-100 text-blue-800 font-medium' : 'text-gray-600 hover:bg-gray-100'}`}
              >
                <FileCode className="w-4 h-4 inline-block mr-2 text-gray-400" />
                {file}
              </button>
            ))}
          </div>
        </aside>

        {/* Content Area */}
        <section className="flex-grow bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col">
          {activeTab === 'instructions' ? (
            <div className="p-8 max-w-none">
              <h2 className="text-2xl font-bold text-gray-800 border-b pb-4 mb-6">Deployment Guide</h2>
              
              <div className="bg-yellow-50 border border-yellow-200 rounded p-4 mb-8">
                <h4 className="font-bold text-yellow-800 flex items-center gap-2 mb-2">
                  <Download className="w-5 h-5"/> Read This First!
                </h4>
                <p className="text-yellow-900 text-sm">
                  The AI Studio environment here runs <strong>Node.js</strong>. However, since you requested <strong>PHP and MySQL</strong>, I have generated the exact source code required in a folder called <code>php_app</code>.
                  <br/><br/>
                  To use your app, download it using the AI Studio <strong>Export to ZIP</strong> feature (found in the settings menu of this preview window), then follow these steps:
                </p>
              </div>

              <h3 className="text-xl font-bold text-gray-800 mb-3">Step 1: Database Setup</h3>
              <ol className="list-decimal pl-5 space-y-2 text-gray-700">
                <li>Log in to your Hostinger hPanel.</li>
                <li>Go to <strong>Databases</strong> → <strong>MySQL Databases</strong>.</li>
                <li>Create a new database and note the <em>Database Name</em>, <em>Username</em>, and <em>Password</em>.</li>
                <li>Open <strong>phpMyAdmin</strong> for your new database.</li>
                <li>Go to the <strong>Import</strong> tab. Select the <code>database.sql</code> file (found in the zip file under the <code>php_app</code> folder) and execute it.</li>
              </ol>

              <h3 className="text-xl font-bold text-gray-800 mt-8 mb-3">Step 2: Configuration</h3>
              <ol className="list-decimal pl-5 space-y-2 text-gray-700">
                <li>Extract the downloaded ZIP file on your computer.</li>
                <li>Open <code>php_app/config.php</code> using a text editor.</li>
                <li>Update the credentials with your Hostinger database details:
                  <pre className="bg-gray-800 text-green-400 p-3 rounded mt-2 text-sm">
$host = 'localhost';
$db_name = 'u918090917_Jalwa_369';
$username = 'u918090917_Jalwa_369';
$password = 'your_database_password';
                  </pre>
                </li>
              </ol>

              <h3 className="text-xl font-bold text-gray-800 mt-8 mb-3">Step 3: Upload Code</h3>
              <ol className="list-decimal pl-5 space-y-2 text-gray-700">
                <li>Go to Hostinger <strong>File Manager</strong>.</li>
                <li>Navigate to your website's <code>public_html</code> folder.</li>
                <li>Upload <strong>all files</strong> from inside the <code>php_app</code> folder directly into <code>public_html</code> (so that index.php is at the root).</li>
                <li>Create a new directory named <code>uploads</code> inside <code>public_html</code>. Set its permissions to <code>777</code> to allow image uploads.</li>
              </ol>

              <h3 className="text-xl font-bold text-gray-800 mt-8 mb-3">Step 4: Admin Access</h3>
              <p className="text-gray-700">
                Visit your domain in a browser. Log in with the default admin credentials:
              </p>
              <ul className="list-disc pl-5 mt-2 text-gray-700 font-mono bg-gray-100 p-4 rounded inline-block">
                <li>Username: admin</li>
                <li>Password: admin123</li>
              </ul>
              <p className="text-gray-700 mt-4 text-sm font-bold">
                ⚠️ IMPORTANT: Change the admin password or username in the database directly after logging in for the first time for security purposes! Also set your UPI ID in the Admin Panel -&gt; Settings.
              </p>
            </div>
          ) : (
            <div className="flex flex-col h-full">
              <div className="bg-gray-100 px-4 py-3 border-b border-gray-200 flex justify-between items-center text-sm font-mono text-gray-600">
                <span>php_app/{activeTab}</span>
              </div>
              <div className="p-6 overflow-y-auto flex-grow bg-gray-50 flex items-center justify-center">
                  <div className="text-center text-gray-500">
                      <FileCode className="w-16 h-16 mx-auto text-gray-300 mb-4" />
                      <p className="text-lg font-medium text-gray-700 mb-2">File Generated</p>
                      <p className="text-sm max-w-sm">The <code>{activeTab}</code> file was successfully generated in the project root.</p>
                      <p className="text-sm text-blue-600 font-bold mt-4">Use the "Export to ZIP" option in AI Studio to download it!</p>
                  </div>
              </div>
            </div>
          )}
        </section>
      </main>
    </div>
  );
}

