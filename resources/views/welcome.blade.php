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
            background: #2c3e50;
            color: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .api-section {
            background: #f8f9fa;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin: 20px 0;
        }
        .endpoint {
            background: #e8f4fc;
            padding: 10px;
            margin: 10px 0;
            border-radius: 3px;
            font-family: monospace;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
        }
        .btn:hover {
            background: #2980b9;
        }
        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
        }
        .status-healthy {
            background: #27ae60;
            color: white;
        }
        .status-unhealthy {
            background: #e74c3c;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>CourseDetailsExtMEA</h1>
            <p>MEA (Engineering Accountability & Competency Database System)</p>
            <p><small>Version 1.0.0</small></p>
        </div>

        <h2>System Status</h2>
        <div id="status">
            <p>Loading system status...</p>
        </div>

        <h2>API Documentation</h2>
        <div class="api-section">
            <h3>Base URL: <code><?php echo url('/api'); ?></code></h3>
            
            <h4>Health Check</h4>
            <div class="endpoint">GET /api/health</div>
            <a href="/api/health" class="btn" target="_blank">Test Health</a>
            
            <h4>API Documentation</h4>
            <div class="endpoint">GET /api/docs</div>
            <a href="/api/docs" class="btn" target="_blank">View Docs</a>
            
            <h4>Main Resources</h4>
            <div class="endpoint">GET /api/personnel</div>
            <a href="/api/personnel" class="btn" target="_blank">View Personnel</a>
            
            <div class="endpoint">GET /api/organisations</div>
            <a href="/api/organisations" class="btn" target="_blank">View Organisations</a>
            
            <div class="endpoint">GET /api/locations</div>
            <a href="/api/locations" class="btn" target="_blank">View Locations</a>
            
            <div class="endpoint">GET /api/vehicles</div>
            <a href="/api/vehicles" class="btn" target="_blank">View Vehicles</a>
        </div>

        <h2>Database Statistics</h2>
        <div id="stats">
            <p>Loading database statistics...</p>
        </div>

        <h2>Integration Info</h2>
        <div class="api-section">
            <p>This system is designed to integrate with:</p>
            <ul>
                <li><strong>SAP LSO</strong> (Learning Solution) as source system</li>
                <li><strong>OSB Integration Layer</strong> (CourseDetailsEBS & CourseDetailsMEAProvABCSREST)</li>
                <li>JMS Topics: topicCourseDetailsAdHoc & topicCourseDetailsTakeOn</li>
            </ul>
            <p><strong>Purpose:</strong> Engineering accountability and technical governance competency requirements management</p>
        </div>
    </div>

    <script>
        // Load system status
        fetch('/api/health')
            .then(response => response.json())
            .then(data => {
                const statusDiv = document.getElementById('status');
                if (data.status === 'healthy') {
                    statusDiv.innerHTML = `
                        <p><span class="status status-healthy">Healthy</span> System is operational</p>
                        <p>Timestamp: ${data.timestamp}</p>
                        <p>Database: ${data.database}</p>
                        <p>Version: ${data.version}</p>
                    `;
                } else {
                    statusDiv.innerHTML = `<p><span class="status status-unhealthy">Unhealthy</span> System has issues</p>`;
                }
            })
            .catch(error => {
                document.getElementById('status').innerHTML = 
                    `<p><span class="status status-unhealthy">Error</span> Cannot connect to API</p>`;
            });

        // Load database statistics
        fetch('/api/personnel')
            .then(response => response.json())
            .then(data => {
                const statsDiv = document.getElementById('stats');
                if (data.success) {
                    const personnelCount = data.data?.pagination?.total || data.data?.length || 0;
                    statsDiv.innerHTML = `
                        <p>Personnel Records: ${personnelCount}</p>
                        <p>Database: <?php echo DB::connection()->getDatabaseName(); ?></p>
                        <p>Connection: <?php echo DB::connection()->getConfig('driver'); ?></p>
                    `;
                }
            })
            .catch(error => {
                document.getElementById('stats').innerHTML = 
                    `<p>Unable to load statistics</p>`;
            });
    </script>
</body>
</html>