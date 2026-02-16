param(
  [Parameter(Mandatory=$true)][string]$ApacheConf,
  [Parameter(Mandatory=$true)][string]$NewDocRoot
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

if (-not (Test-Path -LiteralPath $ApacheConf)) {
  throw "httpd.conf não encontrado: $ApacheConf"
}

$content = Get-Content -LiteralPath $ApacheConf -Raw

# Pega o primeiro DocumentRoot não comentado e captura o path antigo
$docPattern = '(?m)^(?!\s*#)\s*DocumentRoot\s+"(?<old>[^"]+)"\s*$'
$doc = [regex]::Match($content, $docPattern)
if (-not $doc.Success) { throw "Não achei DocumentRoot (não comentado) no httpd.conf" }

$old = $doc.Groups['old'].Value

# Se já está correto, não mexe
if ($old -eq $NewDocRoot) {
  "OK: DocumentRoot já está em '$NewDocRoot'"
  exit 0
}

# Troca só a primeira ocorrência do DocumentRoot principal
$content2 = [regex]::Replace($content, $docPattern, ('DocumentRoot "' + $NewDocRoot + '"'), 1)

# A partir da posição do DocumentRoot, procura e troca o <Directory "old"> correspondente
$afterIndex = $doc.Index
$prefix = $content2.Substring(0, $afterIndex)
$suffix = $content2.Substring($afterIndex)

$dirPattern = '(?m)^(?!\s*#)\s*<Directory\s+"' + [regex]::Escape($old) + '">'
$dir = [regex]::Match($suffix, $dirPattern)
if (-not $dir.Success) {
  throw "Não achei <Directory `"$old`"> correspondente ao DocumentRoot."
}

$suffix2 = [regex]::Replace($suffix, $dirPattern, ('<Directory "' + $NewDocRoot + '">'), 1)

Set-Content -LiteralPath $ApacheConf -Value ($prefix + $suffix2) -Encoding UTF8
"OK: DocumentRoot atualizado de '$old' para '$NewDocRoot'"
