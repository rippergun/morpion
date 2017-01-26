@extends('layouts.app')

    @section('style')
    <style type="text/css">
        td.case {
            width: 50px;
            height: 50px;
            text-align: center;
            vertical-align: middle;
            font:bold 14pt arial;
        }

        td.ehCase {
            cursor:pointer;
        }
    </style>
    @endsection

@section('content')
    <div class="col-sm-offset-3 col-sm-5">
        <h1 class="page-header">Tic Tac Toe</h1>
        <table class="table table-bordered">
            @for ($y = 1; $y <= $rows; $y++)
            <tr>
                @for ($x = 1; $x <= $rows; $x++)
                <td class="case ehCase" data-x="{{ $x }}" data-y="{{ $y }}">
                </td>
                @endfor
            </tr>
            @endfor
        </table>

        <div class="alert alert-success fade" id="ehAlert" role="alert"></div>
    </div>

    <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
<script type="text/javascript">
    var symbols = <?php echo json_encode($symbols) ?>;
    var currentSymbol = symbols[0];

    $('.ehCase')
        .on('click', function (e) {
            e.preventDefault();
            var $this = $(this);
            //ajax play
            $.ajax({
                url: '{{ route('play') }}',
                data: {
                    x: $this.data('x'),
                    y: $this.data('y'),
                    symbol: currentSymbol,
                    _token: "{!! csrf_token() !!}"
                },
                type: 'POST',
                success: function (data) {
                    if (data.error) {
                        $('.ehCase').unbind();
                        $('#ehAlert').addClass('in alert-danger').removeClass('alert-success').html(data.error);
                    }

                    if (data.gameover) {
                        $('.ehCase').unbind();
                        $('#ehAlert').addClass('in alert-warning').removeClass('alert-success').html('Game Over<br> <a href="{{ url('/') }}">Play Again ?</a>');
                    }

                    if (data.winner) {
                        $('.ehCase').unbind();
                        $('#ehAlert').addClass('in').html('"' + currentSymbol + '" win ! <br> <a href="{{ url('/') }}">Play Again ?</a>');
                    }

                    if (data.result) {
                        $this.html(currentSymbol).removeClass('ehCase').unbind();
                        currentSymbol = symbols[(symbols.indexOf(currentSymbol) + 1) % 2];
                    }
                }
            });
        })
</script>
@endsection