@extends('voyager::master')

@section('page_title', __('voyager::generic.view').' '.$dataType->display_name_singular)

@section('page_header')
    <h1 class="page-title" style="padding-left: 15px;">
        {{--removido title--}}
        {{--<i class="{{ $dataType->icon }}"></i> {{ __('voyager::generic.viewing') }} {{ ucfirst($dataType->display_name_singular) }} &nbsp;--}}

        @can('edit', $dataTypeContent)
            <a href="{{ route('voyager.'.$dataType->slug.'.edit', $dataTypeContent->getKey()) }}" class="btn btn-info">
                <span class="glyphicon glyphicon-pencil"></span>&nbsp;
                {{ __('voyager::generic.edit') }}
            </a>
        @endcan
        @can('delete', $dataTypeContent)
            <a href="javascript:;" title="{{ __('voyager::generic.delete') }}" class="btn btn-danger" data-id="{{ $dataTypeContent->getKey() }}" id="delete-{{ $dataTypeContent->getKey() }}">
                <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">{{ __('voyager::generic.delete') }}</span>
            </a>
        @endcan

        <a href="{{ route('voyager.'.$dataType->slug.'.index') }}" class="btn btn-warning">
            <span class="glyphicon glyphicon-list"></span>&nbsp;
            {{ __('voyager::generic.return_to_list') }}
        </a>

        <button type="button" class="btn btn-inverse" data-toggle="modal" data-target="#gerarComandaModal">
            <span class="glyphicon glyphicon-pencil"></span>&nbsp;
            Gerar Comanda
        </button>
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content read container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered" style="padding-bottom:5px;">
                    <!-- form start -->
                    @foreach($dataType->readRows as $row)
                        <div class="panel-heading" style="border-bottom:0;">
                            <h3 class="panel-title">{{ $row->display_name }}</h3>
                        </div>

                        <div class="panel-body" style="padding-top:0;">
                            @if($row->type == "image")
                                <img class="img-responsive"
                                     src="{{ filter_var($dataTypeContent->{$row->field}, FILTER_VALIDATE_URL) ? $dataTypeContent->{$row->field} : Voyager::image($dataTypeContent->{$row->field}) }}">
                            @elseif($row->type == 'multiple_images')
                                @if(json_decode($dataTypeContent->{$row->field}))
                                    @foreach(json_decode($dataTypeContent->{$row->field}) as $file)
                                        <img class="img-responsive"
                                             src="{{ filter_var($file, FILTER_VALIDATE_URL) ? $file : Voyager::image($file) }}">
                                    @endforeach
                                @else
                                    <img class="img-responsive"
                                         src="{{ filter_var($dataTypeContent->{$row->field}, FILTER_VALIDATE_URL) ? $dataTypeContent->{$row->field} : Voyager::image($dataTypeContent->{$row->field}) }}">
                                @endif
                            @elseif($row->type == 'relationship')
                                 @include('voyager::formfields.relationship', ['view' => 'read', 'options' => $row->details])
                            @elseif($row->type == 'select_dropdown' && property_exists($row->details, 'options') &&
                                    !empty($row->details->options->{$dataTypeContent->{$row->field}})
                            )
                                <?php echo $row->details->options->{$dataTypeContent->{$row->field}};?>
                            @elseif($row->type == 'select_multiple')
                                @if(property_exists($row->details, 'relationship'))

                                    @foreach(json_decode($dataTypeContent->{$row->field}) as $item)
                                        {{ $item->{$row->field}  }}
                                    @endforeach

                                @elseif(property_exists($row->details, 'options'))
                                    @if (count(json_decode($dataTypeContent->{$row->field})) > 0)
                                        @foreach(json_decode($dataTypeContent->{$row->field}) as $item)
                                            @if (@$row->details->options->{$item})
                                                {{ $row->details->options->{$item} . (!$loop->last ? ', ' : '') }}
                                            @endif
                                        @endforeach
                                    @else
                                        {{ __('voyager::generic.none') }}
                                    @endif
                                @endif
                            @elseif($row->type == 'date' || $row->type == 'timestamp')
                                {{ property_exists($row->details, 'format') ? \Carbon\Carbon::parse($dataTypeContent->{$row->field})->formatLocalized($row->details->format) : $dataTypeContent->{$row->field} }}
                            @elseif($row->type == 'checkbox')
                                @if(property_exists($row->details, 'on') && property_exists($row->details, 'off'))
                                    @if($dataTypeContent->{$row->field})
                                    <span class="label label-info">{{ $row->details->on }}</span>
                                    @else
                                    <span class="label label-primary">{{ $row->details->off }}</span>
                                    @endif
                                @else
                                {{ $dataTypeContent->{$row->field} }}
                                @endif
                            @elseif($row->type == 'color')
                                <span class="badge badge-lg" style="background-color: {{ $dataTypeContent->{$row->field} }}">{{ $dataTypeContent->{$row->field} }}</span>
                            @elseif($row->type == 'coordinates')
                                @include('voyager::partials.coordinates')
                            @elseif($row->type == 'rich_text_box')
                                @include('voyager::multilingual.input-hidden-bread-read')
                                <p>{!! $dataTypeContent->{$row->field} !!}</p>
                            @elseif($row->type == 'file')
                                @if(json_decode($dataTypeContent->{$row->field}))
                                    @foreach(json_decode($dataTypeContent->{$row->field}) as $file)
                                        <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($file->download_link) ?: '' }}">
                                            {{ $file->original_name ?: '' }}
                                        </a>
                                        <br/>
                                    @endforeach
                                @else
                                    <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($row->field) ?: '' }}">
                                        {{ __('voyager::generic.download') }}
                                    </a>
                                @endif
                            @else
                                @include('voyager::multilingual.input-hidden-bread-read')
                                <p>{{ $dataTypeContent->{$row->field} }}</p>
                            @endif
                        </div><!-- panel-body -->
                        @if(!$loop->last)
                            <hr style="margin:0;">
                        @endif
                    @endforeach

                </div>
            </div>
        </div>
    </div>

    {{-- Single delete modal --}}
    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-trash"></i> {{ __('voyager::generic.delete_question') }} {{ strtolower($dataType->display_name_singular) }}?</h4>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('voyager.'.$dataType->slug.'.index') }}" id="delete_form" method="POST">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-danger pull-right delete-confirm"
                               value="{{ __('voyager::generic.delete_confirm') }} {{ strtolower($dataType->display_name_singular) }}">
                    </form>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@stop

@section('javascript')
    @if ($isModelTranslatable)
        <script>
            $(document).ready(function () {
                $('.side-body').multilingual();
            });
        </script>
        <script src="{{ voyager_asset('js/multilingual.js') }}"></script>
    @endif
    <script>
        var deleteFormAction;
        $('.delete').on('click', function (e) {
            var form = $('#delete_form')[0];

            if (!deleteFormAction) {
                // Save form action initial value
                deleteFormAction = form.action;
            }

            form.action = deleteFormAction.match(/\/[0-9]+$/)
                ? deleteFormAction.replace(/([0-9]+$)/, $(this).data('id'))
                : deleteFormAction + '/' + $(this).data('id');

            $('#delete_modal').modal('show');
        });

    </script>
@stop


<!-- Modal -->
<div class="modal fade" id="gerarComandaModal" tabindex="-1" role="dialog" aria-labelledby="gerarComandaModalLabel" aria-hidden="true">
    <div  class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form  id="gerar_comanda">
                <div class="modal-body">
                        {{ method_field('POST') }}
                        {{ csrf_field() }}
                    <input  type="hidden" value="{{$dataTypeContent->getKey() }}" name="pedido_id">


                    @foreach($dataType->readRows as $key => $row)

                        @if($row->type == 'relationship')
                            <div class="d-flex col-auto">
                                <input type="hidden" class="form-control" id="{{ "input" . $key }}" name="{{ "input" . $key }}" value="@include('voyager::formfields.relationship', ['view' => 'read', 'options' => $row->details])">
                            </div>
                        @else
                            <div class="d-flex col-auto">
                                <input type="hidden" class="form-control" id="{{ "input" . $key }}" name="{{ "input" . $key }}" value="{{ $dataTypeContent->{$row->field} }}">
                            </div>

                        @endif


                    @endforeach

                    <div class="form-group">
                        <label for="cidade">Cidade</label>
                        <input type="cidade" class="form-control" id="cidade" name="cidade" placeholder="Cidade" value="" required="required">
                    </div>
                    <div class="form-group">
                        <label for="bairro">Bairro</label>
                        <input type="bairro" class="form-control" id="bairro" name="bairro" placeholder="Bairro" value="" required="required">
                    </div>
                    <div class="form-group">
                        <label for="endereco">Endereço</label>
                        <input type="endereco" class="form-control" id="endereco" name="endereco" placeholder="Endereço" value="" required="required">
                    </div>
                    <div class="form-group">
                        <label for="numero">Número</label>
                        <input type="numero" class="form-control" id="numero" name="numero" placeholder="Número" value="" required="required">
                    </div>
                    <div class="form-group">
                        <label for="complemento">Complemento</label>
                        <input type="complemento" class="form-control" id="complemento" name="complemento" placeholder="Complemento" value="" required="required">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-inverse">
                        <span class="glyphicon glyphicon-pencil"></span>&nbsp;
                        Gerar Comanda
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="modalPDF" tabindex="-1" role="dialog" aria-labelledby="modalPDF" aria-hidden="true">
    <div  class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                <div class="modal-body" id="#comandaPdfBody">
                    <div class="flex-row">
                        @foreach($dataType->readRows as $key => $row)

                            @if($row->type == 'relationship')

                                <div class="d-flex col-auto">
                                    <p><b>{{ $row->display_name }}</b></p>
                                    <p> @include('voyager::formfields.relationship', ['view' => 'read', 'options' => $row->details])</p>
                                </div>

                            @else
                                <div class="d-flex col-auto">
                                    <p><b>{{ $row->display_name }}</b></p>
                                    <p>{{ $dataTypeContent->{$row->field} }}</p>
                                </div>

                            @endif


                        @endforeach
                    </div>


                </div>
                <div id="editor"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button id="printPDF" class="btn btn-inverse">
                        <span class="glyphicon glyphicon-pencil"></span>&nbsp;
                        Baixar PDF
                    </button>
                </div>
        </div>
    </div>
</div>


