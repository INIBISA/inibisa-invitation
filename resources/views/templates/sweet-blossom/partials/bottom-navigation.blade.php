<nav class="bottom-navigation" aria-label="Navigasi undangan">
    <a href="#home" data-nav-link="home" class="active"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 10.8 12 3l9 7.8v9.7h-6v-6H9v6H3z"/></svg><span>Home</span></a>
    <a href="#couple" data-nav-link="couple"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20.7 3.8 12.6a5.3 5.3 0 0 1 7.5-7.5l.7.7.7-.7a5.3 5.3 0 1 1 7.5 7.5z"/></svg><span>Couple</span></a>
    @if(count(data_get($data, 'events', [])))<a href="#event" data-nav-link="event"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 3v2M19 3v2M4 8h16M5 5h14a2 2 0 0 1 2 2v13H3V7a2 2 0 0 1 2-2Z"/><path d="M7 12h3M14 12h3M7 16h3"/></svg><span>Event</span></a>@endif
    @if(!$isPreview && data_get($settings, 'rsvp', true))<a href="#rsvp" data-nav-link="rsvp"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 5h18v14H3zM3 6l9 7 9-7"/></svg><span>RSVP</span></a>@endif
</nav>
