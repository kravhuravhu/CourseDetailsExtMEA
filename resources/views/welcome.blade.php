<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CourseDetailsExtMEA - MEA System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(182deg, #667eea 0%, #101010 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        .api-section {
            background: #f8f9fa;
            border-left: 4px solid #3498db;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .endpoint {
            background: #e8f4fc;
            padding: 12px;
            margin: 10px 0;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
            cursor: pointer;
            border: none;
            font-size: 14px;
        }
        .btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
            transition: all 0.3s;
        }
        .btn.test-btn {
            background: #27ae60;
        }
        .btn.test-btn:hover {
            background: #219653;
        }
        .btn.danger-btn {
            background: #e74c3c;
        }
        .btn.danger-btn:hover {
            background: #c0392b;
        }
        .status {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: bold;
            margin: 5px;
        }
        .status-healthy {
            background: #27ae60;
            color: white;
        }
        .status-unhealthy {
            background: #e74c3c;
            color: white;
        }
        .status-loading {
            background: #f39c12;
            color: white;
        }
        .result-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            max-height: 300px;
            overflow-y: auto;
            display: none;
        }
        .result-box.active {
            display: block;
        }
        pre {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-size: 12px;
            margin: 10px 0;
        }
        .tab {
            overflow: hidden;
            border-bottom: 2px solid #3498db;
            margin: 20px 0;
        }
        .tab button {
            background-color: inherit;
            float: left;
            border: none;
            outline: none;
            cursor: pointer;
            padding: 14px 20px;
            transition: 0.3s;
            font-size: 14px;
            border-bottom: 3px solid transparent;
        }
        .tab button:hover {
            background-color: #f1f2f6;
        }
        .tab button.active {
            background-color: #e8f4fc;
            border-bottom: 3px solid #3498db;
            font-weight: bold;
        }
        .tab-content {
            display: none;
            padding: 15px;
            border: 1px solid #dee2e6;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .tab-content.active {
            display: block;
        }
        .api-key-display {
            background: linear-gradient(182deg, #667eea 0%, #101010 100%);
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            color: #eeeeee;
        }
        .test-section {
            margin: 20px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .loading {
            color: #7f8c8d;
            font-style: italic;
        }
        .success {
            color: #27ae60;
            border-left: 4px solid #27ae60;
            padding-left: 10px;
        }
        .error {
            color: #e74c3c;
            border-left: 4px solid #e74c3c;
            padding-left: 10px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #3498db;
            margin: 10px 0;
        }
        .stat-label {
            color: #7f8c8d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>CourseDetailsExtMEA</h1>
            <p>MEA (Engineering Accountability & Competency Database System)</p>
            <p><small>Version 1.0.0 | {{ env('APP_ENV') }}</small></p>
            <div id="systemStatus" style="margin-top: 15px;">
                <span class="status status-loading">Checking System Status...</span>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="tab">
            <button class="tab-btn active" onclick="openTab(event, 'dashboard')">Dashboard</button>
            <button class="tab-btn" onclick="openTab(event, 'apiTesting')">API Testing</button>
            <button class="tab-btn" onclick="openTab(event, 'integration')">Integration</button>
            <button class="tab-btn" onclick="openTab(event, 'database')">Database</button>
            <button class="tab-btn" onclick="openTab(event, 'auth')">Authentication</button>
        </div>

        <!-- Dashboard Tab -->
        <div id="dashboard" class="tab-content active">
            <h2>System Overview</h2>
            <div id="statsGrid" class="stats-grid">
                <!-- Statistics will be loaded here -->
            </div>

            <div class="test-section">
                <h3>Quick Health Check</h3>
                <button class="btn test-btn" onclick="runHealthCheck()">Run Health Check</button>
                <div id="healthResult" class="result-box"></div>
            </div>

            <h3>Default API Key for Testing</h3>
            <div class="api-key-display">{{ env('API_KEY') }}</div>
            <p><small>Use this key in the API Testing tab for testing endpoints</small></p>
        </div>

        <!-- API Testing Tab -->
        <div id="apiTesting" class="tab-content">
            <h2>API Testing Interface</h2>
            
            <div class="test-section">
                <h3>Test Configuration</h3>
                <input type="text" id="apiKeyInput" placeholder="Enter API Key" value="{{ env('API_KEY') }}">
                <input type="text" id="endpointInput" placeholder="Enter Endpoint URL" value="/api/personnel">
                
                <div style="margin: 15px 0;">
                    <button class="btn test-btn" onclick="testEndpoint('GET')">Test GET</button>
                    <button class="btn test-btn" onclick="testEndpoint('POST')">Test POST</button>
                    <button class="btn" onclick="testEndpoint('PUT')">Test PUT</button>
                    <button class="btn danger-btn" onclick="testEndpoint('DELETE')">Test DELETE</button>
                </div>
                
                <div id="apiTestResult" class="result-box"></div>
            </div>

            <h3>Quick Test Endpoints</h3>
            <div class="api-section">
                <h4>Health Check</h4>
                <div class="endpoint">GET /api/health</div>
                <button class="btn test-btn" onclick="testSpecificEndpoint('/api/health')">Test</button>
                
                <h4>Personnel API</h4>
                <div class="endpoint">GET /api/personnel</div>
                <button class="btn test-btn" onclick="testSpecificEndpoint('/api/personnel')">Test</button>
                
                <h4>Organisations API</h4>
                <div class="endpoint">GET /api/organisations</div>
                <button class="btn test-btn" onclick="testSpecificEndpoint('/api/organisations')">Test</button>
                
                <h4>Locations API</h4>
                <div class="endpoint">GET /api/locations</div>
                <button class="btn test-btn" onclick="testSpecificEndpoint('/api/locations')">Test</button>
                
                <h4>Vehicles API</h4>
                <div class="endpoint">GET /api/vehicles</div>
                <button class="btn test-btn" onclick="testSpecificEndpoint('/api/vehicles')">Test</button>
            </div>
        </div>

        <!-- Integration Tab -->
        <div id="integration" class="tab-content">
            <h2>Integration Information</h2>
            
            <div class="api-section">
                <p>This system is designed to integrate with:</p>
                <ul>
                    <li><strong>SAP LSO</strong> (Learning Solution) as source system</li>
                    <li><strong>OSB Integration Layer</strong> (CourseDetailsEBS & CourseDetailsMEAProvABCSREST)</li>
                    <li><strong>JMS Topics:</strong> topicCourseDetailsAdHoc & topicCourseDetailsTakeOn</li>
                </ul>
                
                <h3>Integration Endpoints</h3>
                <div class="endpoint">POST /api/integration/personnel</div>
                <p><small>Main endpoint for receiving data from OSB CourseDetailsMEAProvABCSREST</small></p>
                
                <div class="endpoint">GET /api/integration/health</div>
                <button class="btn test-btn" onclick="testSpecificEndpoint('/api/integration/health')">Test Integration Health</button>
                
                <div class="endpoint">GET /api/integration/stats</div>
                <button class="btn test-btn" onclick="testSpecificEndpoint('/api/integration/stats')">Get Integration Stats</button>
            </div>

            <div class="test-section">
                <h3>Test Integration Endpoint</h3>
                <button class="btn test-btn" onclick="testIntegrationEndpoint()">Test Personnel Integration</button>
                <div id="integrationTestResult" class="result-box"></div>
            </div>
        </div>

        <!-- Database Tab -->
        <div id="database" class="tab-content">
            <h2>Database Information</h2>
            
            <div id="dbStats" class="stats-grid">
                <!-- Database statistics will be loaded here -->
            </div>

            <div class="test-section">
                <h3>Database Connection Test</h3>
                <button class="btn test-btn" onclick="testDatabaseConnection()">Test Connection</button>
                <div id="dbTestResult" class="result-box"></div>
            </div>

            <div class="api-section">
                <h3>Database Schema</h3>
                <p><strong>Total Tables:</strong> <span id="tableCount">Loading...</span></p>
                <p><strong>Database Name:</strong> <span id="dbName">Loading...</span></p>
                <p><strong>Connection:</strong> <span id="dbConnection">Loading...</span></p>
            </div>
        </div>

        <!-- Authentication Tab -->
        <div id="auth" class="tab-content">
            <h2>Authentication Management</h2>
            
            <div class="test-section">
                <h3>Validate API Key</h3>
                <input type="text" id="validateKeyInput" placeholder="Enter API Key to validate" value="{{ env('API_KEY') }}">
                <button class="btn test-btn" onclick="validateApiKey()">Validate Key</button>
                <div id="validateKeyResult" class="result-box"></div>
            </div>

            <div class="test-section">
                <h3>Generate New API Key</h3>
                <input type="text" id="newKeyName" placeholder="Key Name (e.g., 'Production Key')">
                <input type="text" id="newKeyDescription" placeholder="Description (optional)">
                <button class="btn test-btn" onclick="generateApiKey()">Generate New Key</button>
                <div id="generateKeyResult" class="result-box"></div>
            </div>

            <div class="api-section">
                <h3>Authentication Methods</h3>
                <p>The system supports two authentication methods:</p>
                <div class="endpoint">Header: X-API-Key: {{ env('API_KEY') }}</div>
                <div class="endpoint">Query Parameter: ?api_key={{ env('API_KEY') }}</div>
            </div>
        </div>
    </div>

    <script>
        // Tab functionality
        function openTab(evt, tabName) {
            const tabContents = document.getElementsByClassName("tab-content");
            const tabButtons = document.getElementsByClassName("tab-btn");
            
            for (let i = 0; i < tabContents.length; i++) {
                tabContents[i].classList.remove("active");
            }
            
            for (let i = 0; i < tabButtons.length; i++) {
                tabButtons[i].classList.remove("active");
            }
            
            document.getElementById(tabName).classList.add("active");
            evt.currentTarget.classList.add("active");
        }

        // Format JSON for display
        function formatJSON(obj) {
            return JSON.stringify(obj, null, 2);
        }

        // Show result in a result box
        function showResult(elementId, content, isError = false) {
            const element = document.getElementById(elementId);
            element.innerHTML = `<pre>${content}</pre>`;
            element.classList.add("active");
            if (isError) {
                element.style.borderLeft = "4px solid #e74c3c";
            } else {
                element.style.borderLeft = "4px solid #27ae60";
            }
        }

        // Load system status on page load
        document.addEventListener('DOMContentLoaded', function() {
            runHealthCheck();
            loadDatabaseStats();
            loadSystemStats();
        });

        // Health check function
        async function runHealthCheck() {
            const statusElement = document.getElementById('systemStatus');
            const resultElement = document.getElementById('healthResult');
            
            statusElement.innerHTML = '<span class="status status-loading">Checking...</span>';
            resultElement.innerHTML = '<div class="loading">Running health check...</div>';
            resultElement.classList.add("active");

            try {
                const response = await fetch('/health');
                const data = await response.json();
                
                if (data.status === 'healthy') {
                    statusElement.innerHTML = '<span class="status status-healthy">Healthy ✓</span>';
                    showResult('healthResult', formatJSON(data));
                    
                    // Update dashboard stats
                    if (data.table_counts) {
                        updateDashboardStats(data.table_counts);
                    }
                } else {
                    statusElement.innerHTML = '<span class="status status-unhealthy">Unhealthy ✗</span>';
                    showResult('healthResult', formatJSON(data), true);
                }
            } catch (error) {
                statusElement.innerHTML = '<span class="status status-unhealthy">Connection Error ✗</span>';
                showResult('healthResult', `Error: ${error.message}`, true);
            }
        }

        // Load database statistics
        async function loadDatabaseStats() {
            try {
                const apiKey = "{{ env('API_KEY') }}";

                // Fetch all entities
                const [personnelRes, orgsRes, locationsRes, vehiclesRes] = await Promise.all([
                    fetch('/api/personnel', { headers: { 'X-API-Key': apiKey } }).then(r => r.json()),
                    fetch('/api/organisations', { headers: { 'X-API-Key': apiKey } }).then(r => r.json()),
                    fetch('/api/locations', { headers: { 'X-API-Key': apiKey } }).then(r => r.json()),
                    fetch('/api/vehicles', { headers: { 'X-API-Key': apiKey } }).then(r => r.json())
                ]);

                // Safely extract counts
                const personnelCount = personnelRes.data?.pagination?.total || personnelRes.data?.length || 0;
                const orgCount = orgsRes.data?.pagination?.total || orgsRes.data?.length || 0;
                const locCount = locationsRes.data?.pagination?.total || locationsRes.data?.length || 0;
                const vehicleCount = vehiclesRes.data?.pagination?.total || vehiclesRes.data?.length || 0;

                // Update dashboard
                const statsGrid = document.getElementById('statsGrid');
                statsGrid.innerHTML = `
                    <div class="stat-card">
                        <div class="stat-value">${personnelCount}</div>
                        <div class="stat-label">Personnel Records</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">${orgCount}</div>
                        <div class="stat-label">Organisations</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">${locCount}</div>
                        <div class="stat-label">Locations</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">${vehicleCount}</div>
                        <div class="stat-label">Vehicles</div>
                    </div>
                `;
            } catch (error) {
                console.error('Error loading stats:', error);
                document.getElementById('statsGrid').innerHTML = `
                    <p style="color:red;">❌ Error loading stats: ${error.message}</p>
                `;
            }
        }

        // Load entity counts
        async function loadEntityCounts() {
            const entities = ['organisations', 'locations', 'vehicles'];
            
            for (const entity of entities) {
                try {
                    const response = await fetch(`/api/${entity}`);
                    const data = await response.json();
                    if (data.success) {
                        const count = data.data?.pagination?.total || data.data?.length || 0;
                        document.getElementById(`${entity.substring(0, 3)}Count`).textContent = count;
                    }
                } catch (error) {
                    console.error(`Error loading ${entity}:`, error);
                }
            }
        }

        // Update dashboard with table counts
        function updateDashboardStats(tableCounts) {
            const statsGrid = document.getElementById('stats');
            if (statsGrid) {
                statsGrid.innerHTML = `
                    <div class="stat-card">
                        <div class="stat-number">${tableCounts.personnel || 0}</div>
                        <div class="stat-label">Personnel</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">${tableCounts.erp_persons || 0}</div>
                        <div class="stat-label">ERP Persons</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">${tableCounts.organisations || 0}</div>
                        <div class="stat-label">Organisations</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">${tableCounts.locations || 0}</div>
                        <div class="stat-label">Locations</div>
                    </div>
                `;
            }
        }

        // Load system statistics
        async function loadSystemStats() {
            const apiKey = "{{ env('API_KEY', 'default-test-key') }}";
            try {
                const response = await fetch('/api/integration/stats', {
                    headers: {
                        'X-API-Key': apiKey
                    }
                });
                const data = await response.json();
                
                if (data.success) {
                    const dbStats = document.getElementById('dbStats');
                    if (dbStats) {
                        dbStats.innerHTML = `
                            <div class="stat-card">
                                <div class="stat-number">${data.data.last_24_hours.total_personnel}</div>
                                <div class="stat-label">Total Personnel</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number">${data.data.system.database_tables}</div>
                                <div class="stat-label">Database Tables</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number">${data.data.system.api_keys_active}</div>
                                <div class="stat-label">Active API Keys</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number">${data.data.last_24_hours.personnel_created}</div>
                                <div class="stat-label">New (24h)</div>
                            </div>
                        `;
                    }
                    
                    // Update table count
                    document.getElementById('tableCount').textContent = data.data.system.database_tables;
                }
            } catch (error) {
                console.error('Error loading system stats:', error);
            }
        }

        // Generic endpoint testing
        async function testEndpoint(method) {
            const apiKey = document.getElementById('apiKeyInput').value;
            const endpoint = document.getElementById('endpointInput').value;
            const resultElement = document.getElementById('apiTestResult');
            
            resultElement.innerHTML = '<div class="loading">Testing endpoint...</div>';
            resultElement.classList.add("active");
            
            let requestOptions = {
                method: method,
                headers: {
                    'X-API-Key': apiKey,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            };
            
            // Add body for POST and PUT requests
            if (method === 'POST' || method === 'PUT') {
                requestOptions.body = JSON.stringify({
                    MRID: 'TEST_' + Date.now(),
                    Name: 'Test User ' + Date.now(),
                    FirstName: 'Test',
                    LastName: 'User',
                    Gender: 'Male',
                    JobTitle: 'Test Position'
                });
            }
            
            try {
                const response = await fetch(endpoint, requestOptions);
                const data = await response.json();
                
                const result = {
                    status: response.status,
                    statusText: response.statusText,
                    method: method,
                    endpoint: endpoint,
                    timestamp: new Date().toISOString(),
                    data: data
                };
                
                showResult('apiTestResult', formatJSON(result));
            } catch (error) {
                showResult('apiTestResult', `Error: ${error.message}`, true);
            }
        }

        // Test specific endpoint
        async function testSpecificEndpoint(endpoint) {
            const apiKey = document.getElementById('apiKeyInput')?.value || '{{ env('API_KEY') }}';
            const resultElement = endpoint.includes('integration') ? 
                document.getElementById('integrationTestResult') : 
                document.getElementById('apiTestResult');
                
            if (!resultElement) {
                alert('Please open the correct tab for this test');
                return;
            }
            
            resultElement.innerHTML = '<div class="loading">Testing endpoint...</div>';
            resultElement.classList.add("active");
            
            try {
                const response = await fetch(endpoint, {
                    headers: {
                        'X-API-Key': apiKey,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                const result = {
                    status: response.status,
                    statusText: response.statusText,
                    endpoint: endpoint,
                    timestamp: new Date().toISOString(),
                    data: data
                };
                
                showResult(resultElement.id, formatJSON(result));
            } catch (error) {
                showResult(resultElement.id, `Error: ${error.message}`, true);
            }
        }

        // Test integration endpoint
        async function testIntegrationEndpoint() {
            const resultElement = document.getElementById('integrationTestResult');
            resultElement.innerHTML = '<div class="loading">Testing integration endpoint...</div>';
            resultElement.classList.add("active");
            
            const testData = {
                MRID: 'INTEGRATION_TEST_' + Date.now(),
                Name: 'Integration Test User',
                FirstName: 'Integration',
                LastName: 'Test',
                Gender: 'Male',
                BirthDate: '1990-01-01',
                Nationality: 'South African',
                JobTitle: 'Integration Tester',
                StartDate: '2024-01-01',
                KeyPerson: false
            };
            
            try {
                const apiKey = "{{ env('API_KEY', 'default-test-key') }}";
                const response = await fetch('/api/integration/personnel', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-API-Key': apiKey,
                        'X-Transaction-ID': 'TEST_TXN_' + Date.now()
                    },
                    body: JSON.stringify(testData)
                });
                
                const data = await response.json();
                
                const result = {
                    status: response.status,
                    endpoint: '/api/integration/personnel',
                    timestamp: new Date().toISOString(),
                    requestData: testData,
                    responseData: data
                };
                
                showResult('integrationTestResult', formatJSON(result));
            } catch (error) {
                showResult('integrationTestResult', `Error: ${error.message}`, true);
            }
        }

        // Test database connection
        async function testDatabaseConnection() {
            const resultElement = document.getElementById('dbTestResult');
            resultElement.innerHTML = '<div class="loading">Testing database connection...</div>';
            resultElement.classList.add("active");
            
            try {
                // Test multiple endpoints to check database connectivity
                const endpoints = [
                    '/api/personnel',
                    '/api/organisations',
                    '/api/locations',
                    '/api/vehicles'
                ];
                
                const results = [];
                const apiKey = "{{ env('API_KEY', 'default-test-key') }}";
                
                for (const endpoint of endpoints) {
                    try {
                        const response = await fetch(endpoint, {
                            headers: {
                                'X-API-Key': apiKey
                            }
                        });
                        const data = await response.json();
                        results.push({
                            endpoint: endpoint,
                            status: response.status,
                            connected: data.success || false
                        });
                    } catch (error) {
                        results.push({
                            endpoint: endpoint,
                            status: 'Error',
                            connected: false,
                            error: error.message
                        });
                    }
                }
                
                const allConnected = results.every(r => r.connected);
                
                const result = {
                    database: 'coursedetails_ext_mea',
                    connectionTest: 'Complete',
                    timestamp: new Date().toISOString(),
                    allEndpointsConnected: allConnected,
                    individualResults: results,
                    summary: allConnected ? 
                        'All database endpoints are responding correctly' :
                        'Some database endpoints are not responding'
                };
                
                showResult('dbTestResult', formatJSON(result), !allConnected);
                
                // Update database info
                if (allConnected) {
                    document.getElementById('dbName').textContent = 'coursedetails_ext_mea';
                    document.getElementById('dbConnection').textContent = 'Connected';
                    document.getElementById('dbConnection').style.color = '#27ae60';
                }
                
            } catch (error) {
                showResult('dbTestResult', `Error: ${error.message}`, true);
            }
        }

        // Validate API key
        async function validateApiKey() {
            const apiKey = document.getElementById('validateKeyInput').value;
            const resultElement = document.getElementById('validateKeyResult');
            
            if (!apiKey) {
                alert('Please enter an API key to validate');
                return;
            }
            
            resultElement.innerHTML = '<div class="loading">Validating API key...</div>';
            resultElement.classList.add("active");
            
            try {
                const response = await fetch(`/api/validate-key?api_key=${encodeURIComponent(apiKey)}`);
                const data = await response.json();
                
                const result = {
                    key: apiKey.substring(0, 8) + '...' + apiKey.substring(apiKey.length - 4),
                    validationTime: new Date().toISOString(),
                    validationResult: data
                };
                
                showResult('validateKeyResult', formatJSON(result), !data.success);
            } catch (error) {
                showResult('validateKeyResult', `Error: ${error.message}`, true);
            }
        }

        // Generate new API key
        async function generateApiKey() {
            const name = document.getElementById('newKeyName').value;
            const description = document.getElementById('newKeyDescription').value;
            const resultElement = document.getElementById('generateKeyResult');
            
            if (!name) {
                alert('Please enter a key name');
                return;
            }
            
            resultElement.innerHTML = '<div class="loading">Generating API key...</div>';
            resultElement.classList.add("active");
            
            try {
                const response = await fetch('/api/generate-key', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        name: name,
                        description: description
                    })
                });
                
                const data = await response.json();
                
                const result = {
                    generationTime: new Date().toISOString(),
                    result: data
                };
                
                showResult('generateKeyResult', formatJSON(result), !data.success);
            } catch (error) {
                showResult('generateKeyResult', `Error: ${error.message}`, true);
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Set default values
            document.getElementById('dbName').textContent = 'coursedetails_ext_mea';
            document.getElementById('dbConnection').textContent = 'MySQL';
            document.getElementById('tableCount').textContent = '31';
        });
    </script>
</body>
</html>