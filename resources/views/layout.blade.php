<!DOCTYPE html>
<html>
<head>
    <title>{{ config('laractive-admin.title') }}</title>
    <meta charset="utf-8" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/metisMenu/1.1.3/metisMenu.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/3.3.7+1/css/sb-admin-2.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css" />
</head>
<body>
<div id="wrapper">
    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ route('admin.dashboard.index') }}">{{ config('laractive-admin.title') }}</a>
        </div>

        <ul class="nav navbar-top-links navbar-right">
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-user">
                    <li>
                        <a href="{{ route('admin.logout') }}"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                    </li>
                </ul>
            </li>
        </ul>

        <div class="navbar-default sidebar" role="navigation">
            <div class="sidebar-nav navbar-collapse">
                <ul class="nav" id="side-menu">
                    @foreach (app(Enomotodev\LaractiveAdmin\Menu::class)->getPages() as $page)
                        <li>
                            <a href="{{ $page['url'] }}"{!! $page['name'] === $class ? 'class="active"' : '' !!}>{{ $page['name'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </nav>

    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">@yield('title')</h1>
            </div>
        </div>

        @if (session()->get('message'))
            <div class="row">
                <div class="col-lg-12">
                    <div class="alert alert-success">
                        {{ session()->get('message') }}
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/metisMenu/1.1.3/metisMenu.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/3.3.7+1/js/sb-admin-2.min.js"></script>
<script>
    var $form = $('form');
    var isSend = false;
    $form.on('submit', function () {
        if (isSend) {
            return false;
        }

        isSend = true;

        $('input[type=checkbox]').each(function (i, target) {
            if (!$(target).prop('checked')) {
                $('<input>',{
                    type: 'hidden',
                    name: $(target).attr('name'),
                    value: '0'
                }).appendTo($form)
            }
        });

        return true;
    });

    $('.datetimepicker').datetimepicker({
        format:'Y-m-d H:i:00'
    });
    $('.datepicker').datetimepicker({
        timepicker: false,
        format:'Y-m-d'
    });
    $('.timepicker').datetimepicker({
        datepicker: false,
        format:'H:i:00'
    });
</script>
</body>
</html>
