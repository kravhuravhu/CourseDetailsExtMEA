<!DOCTYPE html>
<html>
<head>
    <title>CourseDetailsExtMEA - API Authentication</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .card { border: 1px solid #ddd; padding: 20px; margin: 20px 0; border-radius: 5px; }
        input, textarea { width: 100%; padding: 8px; margin: 5px 0; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer; }
        .success { color: green; }
        .error { color: red; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 3px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>CourseDetailsExtMEA API Authentication</h1>
    
    <div class="card">
        <h2>🔑 Default Test API Key</h2>
        <pre>test-api-key-123</pre>
        <p>Use this for testing. Add to requests as:</p>
        <ul>
            <li>Header: <code>X-API-Key: test-api-key-123</code></li>
            <li>Query param: <code>?api_key=test-api-key-123</code></li>
        </ul>
    </div>

    <div class="card">
        <h2>✅ Validate API Key</h2>
        <input type="text" id="validateKey" placeholder="Enter API key to validate">
        <button onclick="validateKey()">Validate</button>
        <div id="validateResult"></div>
    </div>

    <div class="card">
        <h2>🆕 Generate New API Key</h2>
        <input type="text" id="keyName" placeholder="Key name (required)">
        <textarea id="keyDescription" placeholder="Description (optional)" rows="3"></textarea>
        <button onclick="generateKey()">Generate Key</button>
        <div id="generateResult"></div>
    </div>

    <div class="card">
        <h2>📋 List API Keys</h2>
        <button onclick="listKeys()">List All Keys</button>
        <div id="listResult"></div>
    </div>

    <div class="card">
        <h2>🔗 Test API Endpoint</h2>
        <select id="endpoint">
            <option value="/api/personnel">GET /api/personnel</option>
            <option value="/api/organisations">GET /api/organisations</option>
            <option value="/api/locations">GET /api/locations</option>
            <option value="/api/vehicles">GET /api/vehicles</option>
        </select>
        <input type="text" id="testApiKey" placeholder="API Key" value="test-api-key-123">
        <button onclick="testEndpoint()">Test Endpoint</button>
        <div id="testResult"></div>
    </div>

    <script>
        async function validateKey() {
            const key = document.getElementById('validateKey').value;
            const result = document.getElementById('validateResult');
            result.innerHTML = 'Validating...';
            
            try {
                const response = await fetch(`/api/validate-key?api_key=${encodeURIComponent(key)}`);
                const data = await response.json();
                
                if (data.success) {
                    result.innerHTML = `<div class="success">
                        <strong>✓ Valid Key</strong><br>
                        Name: ${data.data.key_name}<br>
                        Active: ${data.data.is_active ? 'Yes' : 'No'}<br>
                        Expires: ${data.data.expires_at || 'Never'}
                    </div>`;
                } else {
                    result.innerHTML = `<div class="error">✗ ${data.message}</div>`;
                }
            } catch (error) {
                result.innerHTML = `<div class="error">Error: ${error.message}</div>`;
            }
        }

        async function generateKey() {
            const name = document.getElementById('keyName').value;
            const description = document.getElementById('keyDescription').value;
            const result = document.getElementById('generateResult');
            
            if (!name) {
                result.innerHTML = '<div class="error">Please enter a key name</div>';
                return;
            }
            
            result.innerHTML = 'Generating...';
            
            try {
                const response = await fetch('/api/generate-key', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name, description })
                });
                const data = await response.json();
                
                if (data.success) {
                    result.innerHTML = `<div class="success">
                        <strong>✓ Key Generated</strong><br>
                        Key: <pre>${data.data.key}</pre>
                        <small>Save this key - it won't be shown again!</small>
                    </div>`;
                } else {
                    result.innerHTML = `<div class="error">✗ ${data.message}</div>`;
                }
            } catch (error) {
                result.innerHTML = `<div class="error">Error: ${error.message}</div>`;
            }
        }

        async function listKeys() {
            const result = document.getElementById('listResult');
            result.innerHTML = 'Loading...';
            
            try {
                const response = await fetch('/api/list-keys');
                const data = await response.json();
                
                if (data.success) {
                    let html = '<table style="width:100%; border-collapse:collapse; margin-top:10px;">';
                    html += '<tr><th>Name</th><th>Key Preview</th><th>Active</th><th>Last Used</th></tr>';
                    
                    data.data.forEach(key => {
                        html += `<tr>
                            <td>${key.name}</td>
                            <td><code>${key.key_preview}</code></td>
                            <td>${key.is_active ? '✅' : '❌'}</td>
                            <td>${key.last_used_at || 'Never'}</td>
                        </tr>`;
                    });
                    
                    html += '</table>';
                    result.innerHTML = html;
                } else {
                    result.innerHTML = `<div class="error">✗ ${data.message}</div>`;
                }
            } catch (error) {
                result.innerHTML = `<div class="error">Error: ${error.message}</div>`;
            }
        }

        async function testEndpoint() {
            const endpoint = document.getElementById('endpoint').value;
            const apiKey = document.getElementById('testApiKey').value;
            const result = document.getElementById('testResult');
            
            result.innerHTML = 'Testing...';
            
            try {
                const response = await fetch(endpoint, {
                    headers: { 'X-API-Key': apiKey }
                });
                const data = await response.json();
                
                result.innerHTML = `<div>
                    <strong>Status: ${response.status}</strong><br>
                    <strong>Success:</strong> ${data.success ? 'Yes' : 'No'}<br>
                    <strong>Message:</strong> ${data.message || 'N/A'}<br>
                    <details>
                        <summary>View Full Response</summary>
                        <pre>${JSON.stringify(data, null, 2)}</pre>
                    </details>
                </div>`;
            } catch (error) {
                result.innerHTML = `<div class="error">Error: ${error.message}</div>`;
            }
        }
    </script>
</body>
</html>