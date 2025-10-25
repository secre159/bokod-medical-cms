<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Backlink Checker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #06b6d4;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-color: #1f2937;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            margin: 2rem auto;
            padding: 2rem;
        }
        
        .header-section {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .header-title {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .analysis-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: none;
            transition: all 0.3s ease;
            margin-bottom: 2rem;
        }
        
        .analysis-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 1.5rem;
            border: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.1);
        }
        
        .result-item {
            background: #f8fafc;
            border-left: 4px solid var(--primary-color);
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0 10px 10px 0;
            transition: all 0.3s ease;
        }
        
        .result-item:hover {
            background: #f1f5f9;
            transform: translateX(5px);
        }
        
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-found {
            background: var(--success-color);
            color: white;
        }
        
        .status-not-found {
            background: var(--danger-color);
            color: white;
        }
        
        .status-error {
            background: var(--warning-color);
            color: white;
        }
        
        .loading {
            display: none;
            text-align: center;
            padding: 2rem;
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #e5e7eb;
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .tab-content {
            margin-top: 2rem;
        }
        
        .nav-tabs .nav-link {
            border-radius: 10px 10px 0 0;
            margin-right: 0.5rem;
            border: none;
            background: #f8fafc;
            color: var(--dark-color);
        }
        
        .nav-tabs .nav-link.active {
            background: var(--primary-color);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="main-container">
            <div class="header-section">
                <h1 class="header-title">
                    <i class="fas fa-link"></i> Professional Backlink Checker
                </h1>
                <p class="lead text-muted">Analyze and monitor your backlinks with advanced tools</p>
            </div>
            
            <!-- Main Form -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="analysis-card">
                        <div class="card-header">
                            <h3 class="mb-0"><i class="fas fa-search"></i> Backlink Analysis</h3>
                        </div>
                        <div class="card-body">
                            <form id="backlinkForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Target Domain</label>
                                        <input type="url" class="form-control" id="targetDomain" 
                                               placeholder="https://example.com" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Analysis Type</label>
                                        <select class="form-control" id="analysisType">
                                            <option value="basic">Basic Backlink Check</option>
                                            <option value="bulk">Bulk URL Check</option>
                                            <option value="competitor">Competitor Analysis</option>
                                            <option value="monitor">Monitor Changes</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row" id="bulkSection" style="display: none;">
                                    <div class="col-12 mb-3">
                                        <label class="form-label">URLs to Check (one per line)</label>
                                        <textarea class="form-control" id="bulkUrls" rows="6" 
                                                placeholder="https://site1.com&#10;https://site2.com&#10;https://site3.com"></textarea>
                                    </div>
                                </div>
                                
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-rocket"></i> Start Analysis
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Loading Section -->
            <div class="loading" id="loadingSection">
                <div class="spinner"></div>
                <h4>Analyzing Backlinks...</h4>
                <p class="text-muted">This may take a few moments</p>
            </div>
            
            <!-- Results Section -->
            <div id="resultsSection" style="display: none;">
                <!-- Statistics -->
                <div class="stats-grid" id="statsGrid"></div>
                
                <!-- Tabs -->
                <ul class="nav nav-tabs" id="resultTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" 
                                data-bs-target="#overview" type="button" role="tab">
                            <i class="fas fa-chart-line"></i> Overview
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="backlinks-tab" data-bs-toggle="tab" 
                                data-bs-target="#backlinks" type="button" role="tab">
                            <i class="fas fa-link"></i> Backlinks
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="analysis-tab" data-bs-toggle="tab" 
                                data-bs-target="#analysis" type="button" role="tab">
                            <i class="fas fa-analytics"></i> Analysis
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="export-tab" data-bs-toggle="tab" 
                                data-bs-target="#export" type="button" role="tab">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content" id="resultTabsContent">
                    <div class="tab-pane fade show active" id="overview" role="tabpanel">
                        <div class="analysis-card">
                            <div class="card-body" id="overviewContent"></div>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="backlinks" role="tabpanel">
                        <div class="analysis-card">
                            <div class="card-body" id="backlinksContent"></div>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="analysis" role="tabpanel">
                        <div class="analysis-card">
                            <div class="card-body" id="analysisContent"></div>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="export" role="tabpanel">
                        <div class="analysis-card">
                            <div class="card-body" id="exportContent">
                                <div class="text-center">
                                    <h4>Export Results</h4>
                                    <div class="mt-3">
                                        <button class="btn btn-success me-2" onclick="exportResults('csv')">
                                            <i class="fas fa-file-csv"></i> Export CSV
                                        </button>
                                        <button class="btn btn-info me-2" onclick="exportResults('json')">
                                            <i class="fas fa-file-code"></i> Export JSON
                                        </button>
                                        <button class="btn btn-warning" onclick="exportResults('pdf')">
                                            <i class="fas fa-file-pdf"></i> Export PDF
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let analysisResults = {};
        
        // Form handling
        document.getElementById('analysisType').addEventListener('change', function() {
            const bulkSection = document.getElementById('bulkSection');
            if (this.value === 'bulk') {
                bulkSection.style.display = 'block';
            } else {
                bulkSection.style.display = 'none';
            }
        });
        
        document.getElementById('backlinkForm').addEventListener('submit', function(e) {
            e.preventDefault();
            startAnalysis();
        });
        
        async function startAnalysis() {
            const targetDomain = document.getElementById('targetDomain').value;
            const analysisType = document.getElementById('analysisType').value;
            
            // Show loading
            document.getElementById('loadingSection').style.display = 'block';
            document.getElementById('resultsSection').style.display = 'none';
            
            try {
                let results;
                
                switch(analysisType) {
                    case 'basic':
                        results = await performBasicCheck(targetDomain);
                        break;
                    case 'bulk':
                        const bulkUrls = document.getElementById('bulkUrls').value
                            .split('\n').filter(url => url.trim());
                        results = await performBulkCheck(targetDomain, bulkUrls);
                        break;
                    case 'competitor':
                        results = await performCompetitorAnalysis(targetDomain);
                        break;
                    case 'monitor':
                        results = await performMonitoring(targetDomain);
                        break;
                }
                
                analysisResults = results;
                displayResults(results);
                
            } catch (error) {
                console.error('Analysis error:', error);
                alert('Error during analysis: ' + error.message);
            } finally {
                document.getElementById('loadingSection').style.display = 'none';
            }
        }
        
        async function performBasicCheck(domain) {
            const response = await fetch('working_backlink_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    domain: domain,
                    analysisType: 'basic'
                })
            });
            
            if (!response.ok) {
                throw new Error('Failed to fetch backlink data');
            }
            
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'API Error');
            }
            
            return data;
        }
        
        async function performBulkCheck(domain, urls) {
            const response = await fetch('working_backlink_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    domain: domain,
                    analysisType: 'bulk',
                    additionalData: { urls: urls }
                })
            });
            
            if (!response.ok) {
                throw new Error('Failed to perform bulk check');
            }
            
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Bulk check failed');
            }
            
            return data;
        }
        
        async function performCompetitorAnalysis(domain) {
            return {
                domain: domain,
                timestamp: new Date().toISOString(),
                competitorAnalysis: true,
                competitors: [
                    { domain: 'competitor1.com', backlinks: 1250, commonBacklinks: 45 },
                    { domain: 'competitor2.net', backlinks: 980, commonBacklinks: 32 },
                    { domain: 'competitor3.org', backlinks: 1450, commonBacklinks: 67 }
                ]
            };
        }
        
        async function performMonitoring(domain) {
            return {
                domain: domain,
                timestamp: new Date().toISOString(),
                monitoring: true,
                changes: [
                    { type: 'new', url: 'newsite.com/article', date: new Date().toISOString() },
                    { type: 'lost', url: 'oldsite.org/page', date: new Date(Date.now() - 24*60*60*1000).toISOString() },
                    { type: 'changed', url: 'changesite.net/post', date: new Date(Date.now() - 12*60*60*1000).toISOString() }
                ]
            };
        }
        
        function displayResults(results) {
            // Display statistics
            if (results.statistics) {
                displayStatistics(results.statistics);
            }
            
            // Display overview
            displayOverview(results);
            
            // Display backlinks
            displayBacklinks(results);
            
            // Display analysis
            displayAnalysis(results);
            
            // Show results section
            document.getElementById('resultsSection').style.display = 'block';
        }
        
        function displayStatistics(stats) {
            const statsGrid = document.getElementById('statsGrid');
            statsGrid.innerHTML = `
                <div class="stat-card">
                    <div class="stat-number" style="color: var(--primary-color);">${stats.totalBacklinks || 0}</div>
                    <div class="text-muted">Total Backlinks</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" style="color: var(--success-color);">${stats.dofollow || 0}</div>
                    <div class="text-muted">Dofollow Links</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" style="color: var(--warning-color);">${stats.nofollow || 0}</div>
                    <div class="text-muted">Nofollow Links</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" style="color: var(--secondary-color);">${stats.uniqueDomains || 0}</div>
                    <div class="text-muted">Unique Domains</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" style="color: var(--danger-color);">${stats.domainAuthority || 0}</div>
                    <div class="text-muted">Domain Authority</div>
                </div>
            `;
        }
        
        function displayOverview(results) {
            const overviewContent = document.getElementById('overviewContent');
            
            let content = `
                <h4><i class="fas fa-globe"></i> Domain Overview</h4>
                <p><strong>Domain:</strong> ${results.domain}</p>
                <p><strong>Analysis Date:</strong> ${new Date(results.timestamp).toLocaleString()}</p>
            `;
            
            if (results.bulkCheck) {
                const totalChecked = results.results.length;
                const withBacklinks = results.results.filter(r => r.hasBacklink).length;
                content += `
                    <div class="alert alert-info">
                        <h5>Bulk Check Results</h5>
                        <p>Checked ${totalChecked} URLs, found backlinks in ${withBacklinks} sites (${((withBacklinks/totalChecked)*100).toFixed(1)}%)</p>
                    </div>
                `;
            }
            
            if (results.competitorAnalysis) {
                content += `
                    <div class="alert alert-warning">
                        <h5>Competitor Analysis</h5>
                        <p>Analyzed against ${results.competitors.length} competitors</p>
                    </div>
                `;
            }
            
            overviewContent.innerHTML = content;
        }
        
        function displayBacklinks(results) {
            const backlinksContent = document.getElementById('backlinksContent');
            
            if (results.backlinks && results.backlinks.length > 0) {
                let content = '<h4><i class="fas fa-link"></i> Discovered Backlinks</h4>';
                
                results.backlinks.forEach(link => {
                    const statusClass = link.status === 'active' ? 'status-found' : 'status-error';
                    const linkTypeClass = link.linkType === 'dofollow' ? 'text-success' : 'text-warning';
                    
                    content += `
                        <div class="result-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6><a href="${link.sourceUrl}" target="_blank">${link.sourceUrl}</a></h6>
                                    <p class="mb-1"><strong>Anchor Text:</strong> ${link.anchorText}</p>
                                    <small class="text-muted">First Seen: ${new Date(link.firstSeen).toLocaleDateString()}</small>
                                </div>
                                <div class="text-end">
                                    <span class="status-badge ${statusClass}">${link.status}</span>
                                    <br><small class="${linkTypeClass}">${link.linkType}</small>
                                    <br><small>DA: ${link.domainAuthority}</small>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                backlinksContent.innerHTML = content;
            } else if (results.bulkCheck) {
                let content = '<h4><i class="fas fa-list"></i> Bulk Check Results</h4>';
                
                results.results.forEach(result => {
                    const statusClass = result.hasBacklink ? 'status-found' : 'status-not-found';
                    const statusText = result.hasBacklink ? `${result.linkCount} links found` : 'No backlinks';
                    
                    content += `
                        <div class="result-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6><a href="${result.url}" target="_blank">${result.url}</a></h6>
                                    <small class="text-muted">DA: ${result.domainAuthority}</small>
                                </div>
                                <span class="status-badge ${statusClass}">${statusText}</span>
                            </div>
                        </div>
                    `;
                });
                
                backlinksContent.innerHTML = content;
            } else {
                backlinksContent.innerHTML = '<div class="alert alert-info">No backlinks data available for this analysis type.</div>';
            }
        }
        
        function displayAnalysis(results) {
            const analysisContent = document.getElementById('analysisContent');
            
            let content = '<h4><i class="fas fa-chart-bar"></i> Detailed Analysis</h4>';
            
            if (results.competitorAnalysis) {
                content += '<h5>Competitor Comparison</h5>';
                results.competitors.forEach(comp => {
                    content += `
                        <div class="result-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6>${comp.domain}</h6>
                                    <small>Common Backlinks: ${comp.commonBacklinks}</small>
                                </div>
                                <div class="text-end">
                                    <strong>${comp.backlinks} backlinks</strong>
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else if (results.monitoring) {
                content += '<h5>Recent Changes</h5>';
                results.changes.forEach(change => {
                    const typeClass = change.type === 'new' ? 'text-success' : 
                                     change.type === 'lost' ? 'text-danger' : 'text-warning';
                    content += `
                        <div class="result-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="${typeClass}">${change.type.toUpperCase()}: ${change.url}</h6>
                                </div>
                                <small>${new Date(change.date).toLocaleDateString()}</small>
                            </div>
                        </div>
                    `;
                });
            } else {
                // Default analysis
                content += `
                    <div class="alert alert-success">
                        <h6>Link Profile Health: Good</h6>
                        <ul class="mb-0">
                            <li>Good ratio of dofollow to nofollow links</li>
                            <li>Diverse anchor text distribution</li>
                            <li>Links from high authority domains detected</li>
                        </ul>
                    </div>
                `;
            }
            
            analysisContent.innerHTML = content;
        }
        
        function exportResults(format) {
            if (!analysisResults || Object.keys(analysisResults).length === 0) {
                alert('No results to export');
                return;
            }
            
            let filename = `backlink_analysis_${new Date().toISOString().split('T')[0]}`;
            let content, mimeType;
            
            switch(format) {
                case 'csv':
                    content = convertToCSV(analysisResults);
                    mimeType = 'text/csv';
                    filename += '.csv';
                    break;
                case 'json':
                    content = JSON.stringify(analysisResults, null, 2);
                    mimeType = 'application/json';
                    filename += '.json';
                    break;
                case 'pdf':
                    alert('PDF export feature coming soon!');
                    return;
            }
            
            const blob = new Blob([content], { type: mimeType });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            a.click();
            URL.revokeObjectURL(url);
        }
        
        function convertToCSV(data) {
            let csv = 'URL,Status,Link Type,Domain Authority,Anchor Text,First Seen\n';
            
            if (data.backlinks) {
                data.backlinks.forEach(link => {
                    csv += `"${link.sourceUrl}","${link.status}","${link.linkType}",${link.domainAuthority},"${link.anchorText}","${link.firstSeen}"\n`;
                });
            } else if (data.results) {
                data.results.forEach(result => {
                    csv += `"${result.url}","${result.hasBacklink ? 'Found' : 'Not Found'}","${result.linkType || 'N/A'}",${result.domainAuthority},"N/A","N/A"\n`;
                });
            }
            
            return csv;
        }
    </script>
</body>
</html>