<h1>{{$concert->title}}</h1>
<h2>{{$concert->subtitle}}</h2>
<p>{{$concert->formattedDate}}</p>
<p>Doors at {{$concert->formattedStartTime}}</p>
<p>{{ $concert->ticketPriceInDollars}}</p>
<p>{{$concert->venue}}</p>
<p>{{$concert->address}}</p>
<p>{{$concert->city}}, {{$concert->state}} {{$concert->zip}}</p>
<p>{{$concert->additionalInformation}}</p>