<!DOCTYPE html>
<html>
<head>
    <title>CourseDetailsExtMEA - OSB Integration Guide</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 0 auto; padding: 20px; }
        h1, h2, h3 { color: #2c3e50; }
        .endpoint { background: #f8f9fa; padding: 15px; border-left: 4px solid #3498db; margin: 15px 0; }
        .method { display: inline-block; padding: 5px 10px; background: #3498db; color: white; border-radius: 3px; }
        .method.get { background: #2ecc71; }
        .method.post { background: #3498db; }
        pre { background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 5px; overflow-x: auto; }
        code { background: #f1f2f6; padding: 2px 5px; border-radius: 3px; }
        .example { background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 15px 0; }
    </style>
</head>
<body>
    <h1>CourseDetailsExtMEA - OSB Integration Guide</h1>
    
    <h2>📋 Overview</h2>
    <p>This document describes the integration endpoints for OSB CourseDetailsMEAProvABCSREST project.</p>
    
    <h2>🔌 Base URL</h2>
    <pre>http://localhost:8000/api/integration</pre>
    <p><strong>Production:</strong> Prod environment URL <a href="https://coursedetailsextmea.jnb1.cloudlet.cloud" target="_blank">here</a></p>
    
    <h2>🔑 Authentication</h2>
    <p>All endpoints require API Key authentication:</p>
    <ul>
        <li><strong>Header:</strong> <code>X-API-Key: cdmea_b5b76664f2cb972124cdc4bb45e3c092</code></li>
        <li><strong>Query Param:</strong> <code>?api_key=cdmea_b5b76664f2cb972124cdc4bb45e3c092</code></li>
    </ul>
    
    <h2>📊 Endpoints</h2>
    
    <div class="endpoint">
        <h3><span class="method get">GET</span> /health</h3>
        <p><strong>Purpose:</strong> Health check for integration monitoring</p>
        <p><strong>Response<i>(expected)</i>:</strong></p>
        <pre>{
    "status": "HEALTHY",
    "service": "CourseDetailsExtMEA Integration API",
    "timestamp": "2024-01-20T10:30:00Z",
    "database": "CONNECTED",
    "table_counts": {
        "personnel": 15,
        "erp_persons": 15,
        "organisations": 5,
        "locations": 8,
        "vehicles": 3
    }
}</pre>
    </div>
    
    <div class="endpoint">
        <h3><span class="method post">POST</span> /personnel</h3>
        <p><strong>Purpose:</strong> Main endpoint for receiving personnel data from OSB</p>
        <p><strong>Headers Required:</strong></p>
        <ul>
            <li><code>X-Transaction-ID</code>: Unique transaction identifier</li>
            <li><code>X-Message-ID</code>: Unique message identifier (optional)</li>
            <li><code>Content-Type</code>: application/json or text/xml</li>
        </ul>
        
        <p><strong>JSON Request Example:</strong></p>
        <pre>{
    "MRID": "EMP_001",
    "Name": "John Doe",
    "FirstName": "John",
    "LastName": "Doe",
    "Gender": "Male",
    "BirthDate": "1985-01-15",
    "Nationality": "South African",
    "Email": "john.doe@example.com",
    "JobTitle": "Senior Engineer",
    "StartDate": "2020-01-01",
    "KeyPerson": true,
    "Responsibility": "Technical Lead"
}</pre>
        
        <p><strong>Batch Processing:</strong></p>
        <pre>{
    "PersonnelDetails": [
        { /* Person 1 */ },
        { /* Person 2 */ },
        { /* Person 3 */ }
    ]
}</pre>
        
        <p><strong>Success Response:</strong></p>
        <pre>{
    "success": true,
    "transaction_id": "TXN_123456",
    "message_id": "MSG_789012",
    "timestamp": "2024-01-20T10:30:00Z",
    "message": "Personnel data processed successfully",
    "status": "PROCESSED"
}</pre>
    </div>
    
    <div class="endpoint">
        <h3><span class="method get">GET</span> /stats</h3>
        <p><strong>Purpose:</strong> Get integration statistics</p>
        <p><strong>Response:</strong></p>
        <pre>{
    "success": true,
    "data": {
        "last_24_hours": {
            "personnel_created": 5,
            "personnel_updated": 3,
            "total_personnel": 25,
            "total_organisations": 8,
            "total_locations": 12
        },
        "system": {
            "database_tables": 31,
            "api_keys_active": 3,
            "audit_logs": 150,
            "error_logs": 5
        }
    }
}</pre>
    </div>
    
    <h2>⚠️ Error Handling</h2>
    <p>All errors return consistent JSON format:</p>
    <pre>{
    "success": false,
    "transaction_id": "TXN_123456",
    "timestamp": "2024-01-20T10:30:00Z",
    "error_code": "PROCESSING_ERROR",
    "error_message": "Detailed error message",
    "error_details": "Additional error details",
    "status": "ERROR"
}</pre>
    
    <h2>🔧 Testing</h2>
    <p>Curl commands for testing:</p>
    
    <div class="example">
        <h3>Test Health Check</h3>
        <pre>curl -H "X-API-Key: test-api-key-123" \
  http://localhost:8000/api/integration/health</pre>
    </div>
    
    <div class="example">
        <h3>Test Personnel Submission</h3>
        <pre>curl -X POST \
  -H "Content-Type: application/json" \
  -H "X-API-Key: test-api-key-123" \
  -H "X-Transaction-ID: TXN_TEST_001" \
  -d '{
    "MRID": "TEST_001",
    "Name": "Test User",
    "FirstName": "Test",
    "LastName": "User"
  }' \
  http://localhost:8000/api/integration/personnel</pre>
    </div>
</body>
</html>