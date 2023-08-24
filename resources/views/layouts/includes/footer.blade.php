<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-10 text-center">
                @php
                    $time = round(microtime(true) - LARAVEL_START);
                @endphp
                <p>©
                    <script>
                        document.write(new Date().getFullYear())
                    </script> {{ config('app.name') }} Design & Develop by EmpyrealinfoTech.
                </p>
            </div>
        </div>
    </div>
</footer>