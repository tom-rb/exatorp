<footer class="exato-footer">
    <div class="container">
        <div class="has-text-centered">
            <p>
                <strong>{{ config('info.brand_name') }} {{ date('Y') }}</strong> -
                @if (Auth::check())
                    <a href="mailto:cursoexato@reitoria.unicamp.br">cursoexato@reitoria.unicamp.br</a>
                @else
                    cursoexato[arroba]reitoria.unicamp.br
                @endif
            </p>
        </div>
    </div>
</footer>