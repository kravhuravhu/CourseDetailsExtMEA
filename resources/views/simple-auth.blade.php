<!DOCTYPE html>
<html>
<head>
    <title>CourseDetailsExtMEA - API Authentication Test</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            max-width: 900px; 
            margin: 0 auto; 
            padding: 20px; 
            background: white;
            min-height: 100vh;
            color: #333;
        }
        .container { 
            background: white; 
            padding: 30px; 
            border-radius: 10px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        h1 { 
            color: #2d3748; 
            border-bottom: 3px solid #667eea; 
            padding-bottom: 10px; 
            margin-bottom: 30px;
        }
        .card { 
            border: 1px solid #e2e8f0; 
            padding: 25px; 
            margin: 25px 0; 
            border-radius: 8px; 
            background: #f8fafc;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        input, textarea, select { 
            width: 100%; 
            padding: 12px; 
            margin: 8px 0; 
            border: 1px solid #cbd5e0; 
            border-radius: 4px; 
            font-size: 16px;
            box-sizing: border-box;
        }
        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        button { 
            padding: 12px 24px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s;
            margin-top: 10px;
        }
        button:hover { 
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            transform: scale(1.05);
        }
        .success { 
            color: #38a169; 
            background: #f0fff4; 
            padding: 15px; 
            border-radius: 4px; 
            border-left: 4px solid #38a169;
            margin-top: 15px;
        }
        .error { 
            color: #e53e3e; 
            background: #fff5f5; 
            padding: 15px; 
            border-radius: 4px; 
            border-left: 4px solid #e53e3e;
            margin-top: 15px;
        }
        pre { 
            background: #2d3748; 
            color: #e2e8f0; 
            padding: 15px; 
            border-radius: 4px; 
            overflow-x: auto; 
            font-family: 'Courier New', monospace;
            margin: 10px 0;
        }
        .code { 
            background: #edf2f7; 
            padding: 2px 6px; 
            border-radius: 3px; 
            font-family: 'Courier New', monospace; 
            font-size: 14px;
        }
        .result-area {
            min-height: 50px;
            margin-top: 15px;
        }
        .loading {
            color: #718096;
            font-style: italic;
        }
        .key-display {
            background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
            padding: 20px;
            border-radius: 6px;
            margin: 15px 0;
            color: #2d3748;
            font-weight: bold;
        }
        .test-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 10px;
        }
        @media (max-width: 768px) {
            .test-section {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 CourseDetailsExtMEA API Authentication Test</h1>
        
        <div class="card">
            <h2>📋 Default Test API Key</h2>
            <div class="key-display">
                <code style="font-size: 18px;">test-api-key-123</code>
            </div>
            <p>Use this key for testing. Requests(for test) as:</p>
            <div class="test-section">
                <div>
                    <strong>Header:</strong>
                    <pre>X-API-Key: test-api-key-123</pre>
                </div>
                <div>
                    <strong>Query Parameter:</strong>
                    <pre>?api_key=test-api-key-123</pre>
                </div>
            </div>
        </div>

        <div class="card">
            <h2>✅ Validate API Key</h2>
            <p>Check if your API key is valid and active.</p>
            <input type="text" id="validateKey" placeholder="Enter API key to validate" value="test-api-key-123">
            <button onclick="validateKey()">🔍 Validate Key</button>
            <div class="result-area" id="validateResult"></div>
        </div>

        <div class="card">
            <h2>🆕 Generate New API Key</h2>
            <p>Create a new API key for testing or integration.</p>
            <input type="text" id="keyName" placeholder="Key name (e.g., 'Test Key', 'Production Key')" required>
            <textarea id="keyDescription" placeholder="Description (optional)" rows="2"></textarea>
            <button onclick="generateKey()">⚡ Generate New Key</button>
            <div class="result-area" id="generateResult"></div>
        </div>

        <div class="card">
            <h2>📋 List All API Keys</h2>
            <p>View all registered API keys in the system.</p>
            <button onclick="listKeys()">📊 List All Keys</button>
            <div class="result-area" id="listResult"></div>
        </div>

        <div class="card">
            <h2>🔗 Test API Endpoints</h2>
            <p>Test API endpoints with your key.</p>
            
            <div class="test-section">
                <div>
                    <label><strong>Select Endpoint:</strong></label>
                    <select id="endpoint">
                        <option value="/api/personnel">GET /api/personnel</option>
                        <option value="/api/organisations">GET /api/organisations</option>
                        <option value="/api/locations">GET /api/locations</option>
                        <option value="/api/vehicles">GET /api/vehicles</option>
                        <option value="/api/audit/logs">GET /api/audit/logs</option>
                        <option value="/api/personnel/1">GET /api/personnel/1</option>
                    </select>
                </div>
                <div>
                    <label><strong>API Key:</strong></label>
                    <input type="text" id="testApiKey" placeholder="API Key" value="test-api-key-123">
                </div>
            </div>
            
            <button onclick="testEndpoint()">🚀 Test Endpoint</button>
            <div class="result-area" id="testResult"></div>
        </div>

        <div class="card">
            <h2>📊 Quick Test Results</h2>
            <button onclick="runAllTests()">🏃 Run All Tests</button>
            <div class="result-area" id="allTestsResult"></div>
        </div>
    </div>

    <script>
        // Show loading state
        function showLoading(elementId) {
            document.getElementById(elementId).innerHTML = '<div class="loading">Loading...</div>';
        }

        // Show result
        function showResult(elementId, html, isError = false) {
            const element = document.getElementById(elementId);
            element.innerHTML = `<div class="${isError ? 'error' : 'success'}">${html}</div>`;
        }

        // Validate API key
        async function validateKey() {
            const key = document.getElementById('validateKey').value.trim();
            const resultElement = document.getElementById('validateResult');
            
            if (!key) {
                showResult('validateResult', 'Please enter an API key', true);
                return;
            }
            
            showLoading('validateResult');
            
            try {
                const response = await fetch(`/api/validate-key?api_key=${encodeURIComponent(key)}`);
                const data = await response.json();
                
                if (data.success && data.data.is_valid) {
                    const keyInfo = data.data;
                    showResult('validateResult', `
                        <strong>✅ Valid API Key</strong><br><br>
                        <strong>Key Name:</strong> ${keyInfo.key_name}<br>
                        <strong>Status:</strong> ${keyInfo.is_active ? '🟢 Active' : '🔴 Inactive'}<br>
                        <strong>Expires:</strong> ${keyInfo.expires_at || 'Never'}<br>
                        <strong>Last Used:</strong> ${keyInfo.last_used_at || 'Never used'}
                    `);
                } else {
                    showResult('validateResult', `❌ ${data.message || 'Invalid API key'}`, true);
                }
            } catch (error) {
                showResult('validateResult', `Error: ${error.message}`, true);
            }
        }

        // Generate new API key
        async function generateKey() {
            const name = document.getElementById('keyName').value.trim();
            const description = document.getElementById('keyDescription').value.trim();
            const resultElement = document.getElementById('generateResult');
            
            if (!name) {
                showResult('generateResult', 'Please enter a key name', true);
                return;
            }
            
            showLoading('generateResult');
            
            try {
                const response = await fetch('/api/generate-key', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ 
                        name: name,
                        description: description 
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showResult('generateResult', `
                        <strong>✅ API Key Generated Successfully!</strong><br><br>
                        <strong>Key Name:</strong> ${data.data.name}<br>
                        <strong>Your New API Key:</strong><br>
                        <div class="key-display" style="margin: 10px 0;">
                            <code style="font-size: 16px; word-break: break-all;">${data.data.key}</code>
                        </div>
                        <strong>⚠️ Important:</strong> Save this key now! It won't be shown again.<br>
                        <strong>Created:</strong> ${new Date(data.data.created_at).toLocaleString()}
                    `);
                } else {
                    showResult('generateResult', `❌ ${data.message || 'Failed to generate key'}`, true);
                }
            } catch (error) {
                showResult('generateResult', `Error: ${error.message}`, true);
            }
        }

        // List all API keys
        async function listKeys() {
            showLoading('listResult');
            
            try {
                const response = await fetch('/api/list-keys');
                const data = await response.json();
                
                if (data.success) {
                    let html = '<table style="width:100%; border-collapse:collapse; margin-top:10px;">';
                    html += '<tr style="background:#e2e8f0;"><th style="padding:10px; text-align:left;">Name</th><th style="padding:10px; text-align:left;">Key Preview</th><th style="padding:10px; text-align:left;">Status</th><th style="padding:10px; text-align:left;">Last Used</th></tr>';
                    
                    if (data.data.length === 0) {
                        html += '<tr><td colspan="4" style="padding:15px; text-align:center;">No API keys found</td></tr>';
                    } else {
                        data.data.forEach(key => {
                            const status = key.is_active ? '🟢 Active' : '🔴 Inactive';
                            const lastUsed = key.last_used_at ? new Date(key.last_used_at).toLocaleDateString() : 'Never';
                            html += `<tr style="border-bottom:1px solid #e2e8f0;">
                                <td style="padding:10px;">${key.name}</td>
                                <td style="padding:10px;"><code>${key.key_preview}</code></td>
                                <td style="padding:10px;">${status}</td>
                                <td style="padding:10px;">${lastUsed}</td>
                            </tr>`;
                        });
                    }
                    
                    html += '</table>';
                    document.getElementById('listResult').innerHTML = html;
                } else {
                    showResult('listResult', `❌ ${data.message || 'Failed to list keys'}`, true);
                }
            } catch (error) {
                showResult('listResult', `Error: ${error.message}`, true);
            }
        }

        // Test API endpoint
        async function testEndpoint() {
            const endpoint = document.getElementById('endpoint').value;
            const apiKey = document.getElementById('testApiKey').value.trim();
            const resultElement = document.getElementById('testResult');
            
            if (!apiKey) {
                showResult('testResult', 'Please enter an API key', true);
                return;
            }
            
            showLoading('testResult');
            
            try {
                const startTime = performance.now();
                const response = await fetch(endpoint, {
                    headers: { 
                        'X-API-Key': apiKey,
                        'Accept': 'application/json'
                    }
                });
                const endTime = performance.now();
                const responseTime = Math.round(endTime - startTime);
                
                const data = await response.json();
                
                let resultHtml = `
                    <strong>Status Code:</strong> ${response.status} ${response.ok ? '✅' : '❌'}<br>
                    <strong>Response Time:</strong> ${responseTime}ms<br>
                    <strong>Success:</strong> ${data.success ? 'Yes ✅' : 'No ❌'}<br>
                    <strong>Message:</strong> ${data.message || 'N/A'}<br>
                `;
                
                if (data.data) {
                    if (data.data.pagination) {
                        resultHtml += `<strong>Records:</strong> ${data.data.pagination.total}<br>`;
                    }
                    
                    resultHtml += `
                        <details style="margin-top:10px;">
                            <summary style="cursor:pointer; color:#667eea; font-weight:bold;">📋 View Full Response</summary>
                            <pre style="margin-top:10px;">${JSON.stringify(data, null, 2)}</pre>
                        </details>
                    `;
                }
                
                document.getElementById('testResult').innerHTML = `<div class="${response.ok ? 'success' : 'error'}">${resultHtml}</div>`;
                
            } catch (error) {
                showResult('testResult', `Error: ${error.message}`, true);
            }
        }

        // Run all tests
        async function runAllTests() {
            const resultElement = document.getElementById('allTestsResult');
            resultElement.innerHTML = '<div class="loading">Running all tests...</div>';
            
            let results = [];
            
            // Test 1: Health endpoint
            try {
                const response = await fetch('/health');
                results.push(`✅ Health endpoint: ${response.status}`);
            } catch (e) {
                results.push(`❌ Health endpoint: ${e.message}`);
            }
            
            // Test 2: Validate default key
            try {
                const response = await fetch('/api/validate-key?api_key=test-api-key-123');
                const data = await response.json();
                results.push(`✅ Validate default key: ${data.data.is_valid ? 'Valid' : 'Invalid'}`);
            } catch (e) {
                results.push(`❌ Validate default key: ${e.message}`);
            }
            
            // Test 3: Test API with default key
            try {
                const response = await fetch('/api/personnel', {
                    headers: { 'X-API-Key': 'test-api-key-123' }
                });
                const data = await response.json();
                results.push(`✅ Test API endpoint: ${response.status} (${data.success ? 'Success' : 'Failed'})`);
            } catch (e) {
                results.push(`❌ Test API endpoint: ${e.message}`);
            }
            
            // Display results
            let html = '<strong>Test Results:</strong><br><br>';
            results.forEach(result => {
                html += `${result}<br>`;
            });
            
            resultElement.innerHTML = `<div class="success">${html}</div>`;
        }

        // Initialize with some default data
        window.onload = function() {
            // Auto-validate the default key
            setTimeout(() => {
                if (document.getElementById('validateKey').value === 'test-api-key-123') {
                    validateKey();
                }
            }, 1000);
            
            // Load keys list
            listKeys();
        };
    </script>
</body>
</html>