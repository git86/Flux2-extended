<#
.SYNOPSIS
GitHub Repository Backdating Script with Co-Authors (PowerShell Version)
#>

# Configuration
REPO_NAME="Flux2-extended"  # Change this to your repository name
CO_AUTHOR_NAME="git86"
CO_AUTHOR_EMAIL="torrifictod@yahoo.com"
$MIN_COMMITS = 50
$MAX_COMMITS = 100
$START_DATE = Get-Date "2022-10-18"
$END_DATE = Get-Date

# Function to generate random date between two dates
function Get-RandomDate {
    param(
        [DateTime]$Start,
        [DateTime]$End
    )
    $randomTicks = Get-Random -Minimum $Start.Ticks -Maximum $End.Ticks
    [DateTime]$randomTicks
}

# Create or clear the repository
if (-not (Test-Path $REPO_NAME)) {
    New-Item -ItemType Directory -Path $REPO_NAME
    Set-Location $REPO_NAME
    git init
} else {
    Set-Location $REPO_NAME
    # Clear existing files (except .git)
    Get-ChildItem -Path . -Exclude .git | Remove-Item -Recurse -Force
}

# Generate random number of commits
$NUM_COMMITS = Get-Random -Minimum $MIN_COMMITS -Maximum $MAX_COMMITS
Write-Host "Generating $NUM_COMMITS backdated commits..."

for ($i=1; $i -le $NUM_COMMITS; $i++) {
    # Generate random date
    $COMMIT_DATE = Get-RandomDate -Start $START_DATE -End $END_DATE
    
    # Format for GIT_AUTHOR_DATE and GIT_COMMITTER_DATE
    $GIT_DATE = $COMMIT_DATE.ToString("yyyy-MM-dd HH:mm:ss")
    
    # Create or modify a file
    Add-Content -Path "history.txt" -Value "Commit $i on $($COMMIT_DATE.ToString('yyyy-MM-dd'))"
    
    # Add to git
    git add .
    
    # Set commit message with co-author
    $COMMIT_MSG = "Backdated commit $i on $($COMMIT_DATE.ToString('yyyy-MM-dd'))"
    $CO_AUTHOR_TRAILER = "Co-authored-by: $CO_AUTHOR_NAME <$CO_AUTHOR_EMAIL>"
    $FULL_MSG = "$COMMIT_MSG`n`n$CO_AUTHOR_TRAILER"
    
    # Commit with specific date
    $env:GIT_AUTHOR_DATE = $GIT_DATE
    $env:GIT_COMMITTER_DATE = $GIT_DATE
    git commit -m $FULL_MSG
    
    Write-Host "Created commit $i with date $($COMMIT_DATE.ToString('yyyy-MM-dd'))"
}

Write-Host "Done! Push to GitHub with:"
Write-Host "cd $REPO_NAME && git push origin main"