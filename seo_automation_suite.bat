@echo off
REM SEO Backlink Building Automation Suite
REM Educational purposes only - Use responsibly

setlocal EnableDelayedExpansion

echo ╔══════════════════════════════════════════════════════════════════╗
echo ║              SEO Backlink Building Automation Suite             ║
echo ║                     Educational Use Only                         ║
echo ╚══════════════════════════════════════════════════════════════════╝
echo.

REM Set default directories
set "INPUT_DIR=.\input_urls"
set "OUTPUT_DIR=.\processed_data"
set "TOOLS_DIR=.\tools"
set "REPORTS_DIR=.\reports"

REM Create directories if they don't exist
if not exist "%INPUT_DIR%" mkdir "%INPUT_DIR%"
if not exist "%OUTPUT_DIR%" mkdir "%OUTPUT_DIR%"
if not exist "%TOOLS_DIR%" mkdir "%TOOLS_DIR%"
if not exist "%REPORTS_DIR%" mkdir "%REPORTS_DIR%"

REM Check for required files
if not exist "url_processor.ps1" (
    echo ERROR: url_processor.ps1 not found!
    echo Please ensure all automation scripts are in the current directory.
    pause
    exit /b 1
)

if not exist "advanced_text_processor.py" (
    echo ERROR: advanced_text_processor.py not found!
    echo Please ensure all automation scripts are in the current directory.
    pause
    exit /b 1
)

:MENU
cls
echo ╔══════════════════════════════════════════════════════════════════╗
echo ║              SEO Backlink Building Automation Suite             ║
echo ║                     Educational Use Only                         ║
echo ╚══════════════════════════════════════════════════════════════════╝
echo.
echo Select an option:
echo.
echo [1] Basic URL Processing (Steps 6-10 equivalent)
echo [2] Advanced Analysis (Vulnerability scanning)
echo [3] Generate SQL Payloads (Steps 16-19 equivalent)
echo [4] Complete Workflow (All steps)
echo [5] Clean URLs Only
echo [6] Extract Keywords (Columns 4-5)
echo [7] Add Prefix/Suffix (TextMechanic equivalent)
echo [8] Generate and Randomize Dorks
echo [9] View Processing Reports
echo [0] Exit
echo.
set /p choice="Enter your choice (0-9): "

if "%choice%"=="1" goto :BASIC_PROCESSING
if "%choice%"=="2" goto :ADVANCED_ANALYSIS
if "%choice%"=="3" goto :GENERATE_PAYLOADS
if "%choice%"=="4" goto :COMPLETE_WORKFLOW
if "%choice%"=="5" goto :CLEAN_ONLY
if "%choice%"=="6" goto :EXTRACT_KEYWORDS
if "%choice%"=="7" goto :PREFIX_SUFFIX
if "%choice%"=="8" goto :RANDOMIZE_DORKS
if "%choice%"=="9" goto :VIEW_REPORTS
if "%choice%"=="0" goto :EXIT
goto :MENU

:GET_INPUT_FILE
echo.
echo Available input files in %INPUT_DIR%:
dir /b "%INPUT_DIR%\*.txt" 2>nul
echo.
set /p input_file="Enter input filename (or full path): "

REM Check if file exists in input directory first
if exist "%INPUT_DIR%\%input_file%" (
    set "input_file=%INPUT_DIR%\%input_file%"
    goto :EOF
)

REM Check if full path provided
if exist "%input_file%" (
    goto :EOF
)

REM File not found
echo ERROR: File not found: %input_file%
echo Please place your URL files in the %INPUT_DIR% directory.
pause
goto :MENU

:BASIC_PROCESSING
echo.
echo ═══════════════════════════════════════════════════════════════════
echo                     BASIC URL PROCESSING
echo ═══════════════════════════════════════════════════════════════════
echo.
call :GET_INPUT_FILE

echo Processing URLs with PowerShell script...
powershell -ExecutionPolicy Bypass -File "url_processor.ps1" -InputFile "%input_file%" -OutputDir "%OUTPUT_DIR%\basic" -CleanDuplicates -ExtractKeywords -Categorize

if errorlevel 1 (
    echo ERROR: Processing failed!
    pause
    goto :MENU
)

echo.
echo ✓ Basic processing complete! Check %OUTPUT_DIR%\basic for results.
pause
goto :MENU

:ADVANCED_ANALYSIS
echo.
echo ═══════════════════════════════════════════════════════════════════
echo                     ADVANCED ANALYSIS
echo ═══════════════════════════════════════════════════════════════════
echo.
call :GET_INPUT_FILE

echo Running advanced analysis with Python script...
python advanced_text_processor.py "%input_file%" -o "%OUTPUT_DIR%\advanced" --clean --categorize --analyze --keywords

if errorlevel 1 (
    echo ERROR: Analysis failed! Make sure Python is installed.
    pause
    goto :MENU
)

echo.
echo ✓ Advanced analysis complete! Check %OUTPUT_DIR%\advanced for results.
pause
goto :MENU

:GENERATE_PAYLOADS
echo.
echo ═══════════════════════════════════════════════════════════════════
echo                    GENERATE SQL PAYLOADS
echo ═══════════════════════════════════════════════════════════════════
echo.
call :GET_INPUT_FILE

echo Generating SQL injection payloads...
python advanced_text_processor.py "%input_file%" -o "%OUTPUT_DIR%\payloads" --clean --analyze --payloads --randomize

if errorlevel 1 (
    echo ERROR: Payload generation failed!
    pause
    goto :MENU
)

echo.
echo ✓ SQL payloads generated! Check %OUTPUT_DIR%\payloads for results.
echo ⚠️  WARNING: Use these payloads only for educational or authorized testing!
pause
goto :MENU

:COMPLETE_WORKFLOW
echo.
echo ═══════════════════════════════════════════════════════════════════
echo                     COMPLETE WORKFLOW
echo ═══════════════════════════════════════════════════════════════════
echo.
call :GET_INPUT_FILE

set timestamp=%date:~-4%%date:~3,2%%date:~0,2%_%time:~0,2%%time:~3,2%%time:~6,2%
set timestamp=%timestamp: =0%
set "workflow_dir=%OUTPUT_DIR%\complete_%timestamp%"

echo Starting complete workflow...
echo Output directory: %workflow_dir%
echo.

echo Step 1: Basic cleaning and processing...
powershell -ExecutionPolicy Bypass -File "url_processor.ps1" -InputFile "%input_file%" -OutputDir "%workflow_dir%\step1_basic" -CleanDuplicates -ExtractKeywords -Categorize

echo Step 2: Advanced analysis and vulnerability assessment...
python advanced_text_processor.py "%input_file%" -o "%workflow_dir%\step2_analysis" --clean --categorize --analyze --keywords

echo Step 3: SQL payload generation...
python advanced_text_processor.py "%input_file%" -o "%workflow_dir%\step3_payloads" --clean --analyze --payloads --randomize

echo Step 4: Generating comprehensive report...
call :GENERATE_REPORT "%workflow_dir%" "%input_file%"

echo.
echo ✓ Complete workflow finished! 
echo ✓ Results saved to: %workflow_dir%
echo ✓ Report generated: %workflow_dir%\workflow_report.txt
pause
goto :MENU

:CLEAN_ONLY
echo.
echo ═══════════════════════════════════════════════════════════════════
echo                        CLEAN URLS ONLY
echo ═══════════════════════════════════════════════════════════════════
echo.
call :GET_INPUT_FILE

echo Cleaning URLs...
powershell -ExecutionPolicy Bypass -File "url_processor.ps1" -InputFile "%input_file%" -OutputDir "%OUTPUT_DIR%\cleaned" -CleanDuplicates

echo.
echo ✓ URL cleaning complete! Check %OUTPUT_DIR%\cleaned for results.
pause
goto :MENU

:EXTRACT_KEYWORDS
echo.
echo ═══════════════════════════════════════════════════════════════════
echo                    EXTRACT KEYWORDS (Columns 4-5)
echo ═══════════════════════════════════════════════════════════════════
echo.
call :GET_INPUT_FILE

echo Extracting keywords from URLs...
python advanced_text_processor.py "%input_file%" -o "%OUTPUT_DIR%\keywords" --clean --keywords

echo.
echo ✓ Keyword extraction complete! Check %OUTPUT_DIR%\keywords for results.
pause
goto :MENU

:PREFIX_SUFFIX
echo.
echo ═══════════════════════════════════════════════════════════════════
echo                 ADD PREFIX/SUFFIX (TextMechanic equivalent)
echo ═══════════════════════════════════════════════════════════════════
echo.
call :GET_INPUT_FILE

echo.
set /p prefix="Enter prefix (or press Enter for none): "
set /p suffix="Enter suffix (or press Enter for none): "

if "%prefix%"=="" if "%suffix%"=="" (
    echo No prefix or suffix specified. Returning to menu...
    pause
    goto :MENU
)

echo Adding prefix '%prefix%' and suffix '%suffix%' to URLs...
python advanced_text_processor.py "%input_file%" -o "%OUTPUT_DIR%\prefixed" --clean --prefix "%prefix%" --suffix "%suffix%"

echo.
echo ✓ Prefix/Suffix processing complete! Check %OUTPUT_DIR%\prefixed for results.
pause
goto :MENU

:RANDOMIZE_DORKS
echo.
echo ═══════════════════════════════════════════════════════════════════
echo                    GENERATE AND RANDOMIZE DORKS
echo ═══════════════════════════════════════════════════════════════════
echo.
call :GET_INPUT_FILE

echo Generating and randomizing dorks...
python advanced_text_processor.py "%input_file%" -o "%OUTPUT_DIR%\dorks" --clean --randomize

echo.
echo ✓ Dork generation complete! Check %OUTPUT_DIR%\dorks for results.
pause
goto :MENU

:VIEW_REPORTS
echo.
echo ═══════════════════════════════════════════════════════════════════
echo                       PROCESSING REPORTS
echo ═══════════════════════════════════════════════════════════════════
echo.

echo Available reports:
dir /s /b "%OUTPUT_DIR%\*.log" "%OUTPUT_DIR%\*report*" "%OUTPUT_DIR%\*.json" 2>nul

echo.
echo Available output directories:
dir /b "%OUTPUT_DIR%" 2>nul

echo.
set /p report_choice="Enter directory name to explore (or press Enter to return): "

if "%report_choice%"=="" goto :MENU

if exist "%OUTPUT_DIR%\%report_choice%" (
    echo.
    echo Contents of %OUTPUT_DIR%\%report_choice%:
    dir /b "%OUTPUT_DIR%\%report_choice%"
    echo.
    echo Opening directory in Explorer...
    explorer "%OUTPUT_DIR%\%report_choice%"
)

pause
goto :MENU

:GENERATE_REPORT
set "report_dir=%~1"
set "input_file=%~2"

echo Generating workflow report...

(
echo SEO Backlink Building Automation Report
echo ======================================
echo Generated on: %date% %time%
echo Input file: %input_file%
echo Output directory: %report_dir%
echo.
echo PROCESSING SUMMARY:
echo ------------------
echo ✓ Step 1: Basic URL processing and cleaning
echo ✓ Step 2: Advanced analysis and categorization  
echo ✓ Step 3: SQL payload generation and randomization
echo ✓ Step 4: Comprehensive reporting
echo.
echo OUTPUT FILES:
echo ------------
) > "%report_dir%\workflow_report.txt"

REM List all generated files
for /r "%report_dir%" %%f in (*.*) do (
    echo %%~nxf >> "%report_dir%\workflow_report.txt"
)

(
echo.
echo EDUCATIONAL DISCLAIMER:
echo ----------------------
echo This automation suite is designed for educational purposes only.
echo All techniques demonstrated should only be used on systems you own
echo or have explicit authorization to test. Unauthorized access to
echo computer systems is illegal and unethical.
echo.
echo Always follow responsible disclosure practices and comply with
echo applicable laws and regulations in your jurisdiction.
) >> "%report_dir%\workflow_report.txt"

goto :EOF

:EXIT
echo.
echo Thank you for using the SEO Automation Suite!
echo Remember: Use these tools responsibly and ethically.
echo.
pause
exit /b 0