Bună, {{ $jobRequest->name }}!

{{ $craftsman->name }} este interesat de cererea ta: "{{ $jobRequest->title }}"
@if($craftsmanMessage)

Mesaj: {{ $craftsmanMessage }}
@endif

Contact meseriaș:
  Telefon: {{ $craftsman->phone ?? 'vezi profilul' }}
  Profil: {{ $profileUrl }}

Te sfătuim să suni cât mai curând — meseriașii răspund de obicei primilor clienți care îi contactează.

-- Echipa OmulPotrivit — meseriasionline.ro
