<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="table-responsive">
      <table class="table table-condensed table-bordered" style="background-color:#bfbfbf;">
        <thead>
          <tr>
            <th style="width:80px;">Operación</th>
            <th style="width:60px;">Cant.</th>
            <th>Descripción</th>
            <th style="width:80px;">P. Unit.</th>
            <th style="width:80px;">Total</th>
          </tr>
        </thead>
        <tbody>
          @if($venta = \App\Venta::where('usuario_id', Auth::user()->id)->where('tienda_id', Auth::user()->tienda_id)->where('estado', 1)->first())
            @foreach($venta->detalles as $detalle)
              <tr>
                <td><button type="button" class="btn btn-xs btn-danger">Quitar</button></td>
                <td>{{$detalle->cantidad}}</td>
                <td>{{$detalle->producto->descripcion}}</td>
                <td style="text-align:right">{{$detalle->precio_unidad}}</td>
                <td style="text-align:right">{{$detalle->total}}</td>
              </tr>
            @endforeach
          <tr>
            <td colspan="4"><strong class="pull-right">TOTAL: </strong></td>
            <td style="text-align:right">{{$venta->total}}</td>
          </tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>
