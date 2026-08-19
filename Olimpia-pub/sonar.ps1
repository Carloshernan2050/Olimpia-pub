$ErrorActionPreference = 'Stop'

$token = $env:SONAR_TOKEN
if (-not $token -and (Test-Path -LiteralPath '.sonar-token')) {
    $token = (Get-Content -LiteralPath '.sonar-token' -Raw).Trim()
}

if (-not $token) {
    Write-Error @"
SONAR_TOKEN is not set.
Generate a token in SonarQube, then either:
  `$env:SONAR_TOKEN = 'your_token'
or create a .sonar-token file in this directory (already gitignored).
"@
    exit 1
}

$source = (Resolve-Path '.').Path
docker run --rm -v "${source}:/usr/src" -w /usr/src sonarsource/sonar-scanner-cli:5 "-Dsonar.host.url=http://host.docker.internal:9000" "-Dsonar.login=$token"
