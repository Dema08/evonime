$p = 'tests/Feature/WatchPageTest.php'
$raw = [System.IO.File]::ReadAllText($p)
$nl = if ($raw.Contains("`r`n")) { "`r`n" } else { "`n" }
$lines = $raw.Split(@("`r`n"), [StringSplitOptions]::None)
if ($lines.Count -eq 1) { $lines = $raw.Split(@("`n"), [StringSplitOptions]::None) }
$out = foreach ($ln in $lines) {
    $t = $ln.Trim()
    if ($t -eq "'studio' => 'Studio Pierrot',") {
        "            'studio' => 'Studio Pierrot',"
    } elseif ($t -eq "'aired_at' => now()->toDateTimeString(),") {
        "                                'aired_at' => now()->toDateTimeString(),"
    } else {
        $ln
    }
}
$joined = $out -join $nl
if ($raw.EndsWith($nl) -and -not $joined.EndsWith($nl)) { $joined = $joined + $nl }
[System.IO.File]::WriteAllText($p, $joined)
"normalized"
