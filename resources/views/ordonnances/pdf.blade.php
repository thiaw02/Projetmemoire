<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Ordonnance</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #222; }
    .header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom: 12px; }
    .title { font-size: 18px; font-weight: 700; }
    .box { border: 1px solid #ddd; border-radius: 6px; padding: 10px; margin-bottom: 10px; }
    .muted { color:#666; font-size: 11px; }
    ul { margin: 6px 0 0 18px; }
  </style>
</head>
<body>
  <div class="header">
    <div>
      <div class="title">Ordonnance médicale</div>
      <div class="muted">Générée le {{ optional($generatedAt)->format('d/m/Y H:i') }}</div>
    </div>
    <div class="muted">
      @if($medecin)
        <div><strong>Médecin:</strong> {{ $medecin->name }}</div>
        @if(!empty($medecin->specialite))<div><strong>Spécialité:</strong> {{ $medecin->specialite }}</div>@endif
      @endif
    </div>
  </div>

  <div class="box">
    <div><strong>Patient:</strong> {{ $patient->nom }} {{ $patient->prenom }}</div>
    <div class="muted">Sexe: {{ $patient->sexe }} | Né(e) le: {{ optional($patient->date_naissance ? \Carbon\Carbon::parse($patient->date_naissance) : null)->format('d/m/Y') }}</div>
  </div>

  <div class="box">
    <div><strong>Médicaments / Instructions</strong></div>
    @php($text = $ordonnance->medicaments ?: $ordonnance->contenu)
    @if(!empty($text))
      @php($lines = preg_split("/(\r\n|\r|\n)/", $text))
      <ul>
        @foreach($lines as $ln)
          @if(trim($ln) !== '')
            <li>{{ $ln }}</li>
          @endif
        @endforeach
      </ul>
    @else
      <div class="muted">—</div>
    @endif
  </div>

  @if(!empty($ordonnance->dosage))
  <div class="box">
    <div><strong>Dosage global</strong></div>
    <div>{{ $ordonnance->dosage }}</div>
  </div>
  @endif

  <div class="muted">Document destiné à un usage médical. En cas de doute, contactez votre médecin.</div>
</body>
</html>
