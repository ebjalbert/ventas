<script type="text/javascript">
  $(document).ready(function() {

    /**
    * Al hacer clic en el botón btnRegistrarTarjeta, se muestra un modal con un formulario para registrar
    * la venta con tarjeta.
    * Fecha: 24/09/2017
    */
    $("#btnRegistrarTarjeta").click(function() {

      $("#registrarTarjeta").modal("show"); 
    });

    /**
    * Al cambiar de opcion el tipo de tarjeta que se va a utilizar se va a mostrar el incremento en la venta.
    * Fecha: 23/09/2017
    */
    $("#tarjeta_id").change(function() {
      if ($(this).val()) {

        $.post("{{url('comision')}}", {tarjeta_id: $(this).val()}, function(data, textStatus, xhr) {
          $("#comision").html('EL INCREMENTO POR EL USO DE ESTA TARJETA SERÁ DE ' + data);
        });
      }else{
        $("#comision").html('');
      }
    });

    /**
    * Al hacer clic en el botón btnTipoCambio, se muestra un modal con el tipo de cambio ya configurado
    * o vacio para configurar.
    * Fecha: 22/09/2017
    */
    $("#btnTipoCambio").click(function() {
      $.post("{{url('tipo-cambio')}}", {efectivo: $(this).val()}, function(data, textStatus, xhr) {
        // Si el retorno es 0, mostramos un modal para que configure el tipo de cambio.
        if (data == 0) {
          $("#msjTipoCambio").html("DEBE CONFIGURAR EL TIPO DE CAMBIO DE DOLARES A SOLES. ESTO SOLO SE RELIAZA UNA VEZ,"+
          "PARA ACTUALIZAR EL TIPO DE CAMBIO PULSE EL BOTÓN \"Tipo Cambio\".");
          $("#txtCambio").val("");
        }else{
          $("#msjTipoCambio").html("EL TIPO DE CAMBIO DE DOLARES A SOLES ESTÁ CONFIGURADO ACTUELMENTE A "+data);
          $("#txtCambio").val(data);
        }
        $("#tipoCambio").modal("show");
      });
    });

    $('.moneda').mask("# ##0.00", {reverse: true});
    $('.numero').mask("#0", {reverse: true});

    /**
    * Al cambiar el valor de la tarjeta, se tiene que registrar la venta por tarjeta con el sistema.
    * Para esto, mostramos el modal para que el usuario registre esa venta.
    * Fecha: 22/09/2017
    */
    $("#tarjeta").change(function() {
      $.post("{{url('vuelto')}}", {efectivo: $("#efectivo").val(), dolares: $("#dolares").val(), tarjeta: $(this).val()},
      function(data, textStatus, xhr) {
        if (data == 0) {
          $("#mensaje").html("HAY UN VALOR EN EL CAMPO DOLARES, DEBE CONFIGURAR EL TIPO DE CAMBIO QUE VA A UTILIZAR HACIENDO "+
            "CLIC EN EL BOTÓN 'Tipo Cambio' O DE LO CONTRARIO BORRE EL VALOR EN EL CAMPO DOLARES, SI NO, NO SE PODRÁ REALIZAR LA VENTA.");
          $("#errores").modal("show");
        }else{
          $("#vuelto").val(data);
        }
      });
    });

    $("#btnCambio").click(function() {
      if ($("#txtCambio").val() != "") {
        $.post("{{url('cambio')}}", {cambio: $("#txtCambio").val()}, function(data, textStatus, xhr) {
          $("#tipoCambio").modal("hide");
        });
      }
      $.post("{{url('vuelto')}}", {efectivo: $("#efectivo").val(), dolares: $("#dolares").val(), tarjeta: $(this).val()},
      function(data, textStatus, xhr) {
        $("#vuelto").val(data);
      });
    });

    /**
    * Verificamos si el tipo de cambio está configurado y posteriormente el vuelto del cliente.
    * Fecha: 21/09/2017
    */
    $("#dolares").change(function() {
      $.post("{{url('tipo-cambio')}}", {efectivo: $(this).val()}, function(data, textStatus, xhr) {
        // Si el retorno es 0, mostramos un modal para que configure el tipo de cambio.
        if (data == 0) {
          $("#tipoCambio").modal("show");
        }
      });
      $.post("{{url('vuelto')}}", {efectivo: $("#efectivo").val(), dolares: $(this).val(), tarjeta: $("#tarjeta").val()},
      function(data, textStatus, xhr) {
        $("#vuelto").val(data);
      });
    });

    /**
    * Verificamos el estado o características del dinero entregado al cajer, si es mayor
    * al monto total de la venta se muestra el vuelto, de lo contrario, se muestra lo que falta en negativo.
    * Fecha: 21/09/2017
    */
    $("#efectivo").change(function() {
      $.post("{{url('vuelto')}}", {efectivo: $(this).val(), dolares: $("#dolares").val(), tarjeta: $("#tarjeta").val()},
        function(data, textStatus, xhr) {
          if (data == 0) {
            $("#mensaje").html("HAY UN VALOR EN EL CAMPO DOLARES, DEBE CONFIGURAR EL TIPO DE CAMBIO QUE VA A UTILIZAR HACIENDO "+
              "CLIC EN EL BOTÓN 'Tipo Cambio' O DE LO CONTRARIO BORRE EL VALOR EN EL CAMPO DOLARES, SI NO, NO SE PODRÁ REALIZAR LA VENTA.");
            $("#errores").modal("show");
          }else{
            $("#vuelto").val(data);
          }
      });
    });

    /**
    * Busca un cliente, ya sea persona o empresa, si el campo queda vacio puede guardarse la venta.
    * Fecha: 21/09/2017
    */
    $("#documento").change(function(){
      // Verificamos si el campo está vacio.
      if ($(this).val() != "") {
        // Verificamos si es un número de DNI, o RUC.
        if($(this).val().length == 8){
          // Buscarmos los datos de la persona que tenga este dni, si existe mostramos los datos, de lo contrario
          // solo activamos los inputs para que igresen los datos del nuevo cliente.
          $.post("{{url('buscar-persona')}}", {dni: $(this).val()}, function(data, textStatus, xhr) {
            $("#nombre").prop('readonly', true);
            $("#nombres").prop('readonly', false);
            $("#apellidos").prop('readonly', false);
            $("#direccion").prop('readonly', false);
            $("#nombre").val("");
            $("#nombres").val(data['nombres']);
            $("#apellidos").val(data['apellidos']);
            $("#direccion").val(data['direccion']);
          });
        }else if ($(this).val().length == 11) {
          // Buscarmos los datos de la empresa que tenga este ruc, si existe mostramos los datos, de lo contrario
          // solo activamos los inputs para que igresen los datos del nuevo cliente.
          $.post("{{url('buscar-empresa')}}", {ruc: $(this).val()}, function(data, textStatus, xhr) {
            $("#nombre").prop('readonly', false);
            $("#nombres").prop('readonly', true);
            $("#apellidos").prop('readonly', true);
            $("#direccion").prop('readonly', false);
            $("#nombre").val(data['nombre']);
            $("#nombres").val("");
            $("#apellidos").val("");
            $("#direccion").val(data['direccion']);
          });
        }else{
          // Si no tiene 8 ni 11 dítos el documento, se limpia el formulario y se muestra un mensaje de error.
          $("#nombre").prop('readonly', true);
          $("#nombres").prop('readonly', true);
          $("#apellidos").prop('readonly', true);
          $("#direccion").prop('readonly', false);
          $("#nombre").val("");
          $("#nombres").val("");
          $("#apellidos").val("");
          $("#direccion").val("");
          $("#mensaje").html("EL DOCUMENTO SOLO PUEDE CONTENER 8 U 11 DÍGITOS.");
          $("#errores").modal("show");
        }
      }else{
        // Limpiamos todos los campos y la venta se hará con un cliente común.
        $("#nombre").prop('readonly', true);
        $("#nombres").prop('readonly', true);
        $("#apellidos").prop('readonly', true);
        $("#direccion").prop('readonly', true);
        $("#nombre").val("");
        $("#nombres").val("");
        $("#apellidos").val("");
        $("#direccion").val("");
      }
    });

    /*
    * Token necesario para hacer consultas por ajax.
    * Fecha 18/09/2017
    */
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    /**
    * se llama al método enter del input txtCodigo para buscar el producto que tenga ese código.
    * Fecha: 18/09/2017
    */
    $("#txtCodigo").change(function() {
      if ($(this).val() != "") {
        $.post("{{url('buscar-producto')}}", {codigo: $(this).val(), tienda_id: $("#tienda_id").val()}, function(data, textStatus, xhr) {
          if (data['producto'] != 0) {
            $(".codigo").html(data['producto']['codigo']);
            $("#producto_codigo").val(data['producto']['codigo']);
            $(".descripcion").html(data['producto']['descripcion']);
            $(".precio").html(data['producto']['precio']);
            $(".precio").val(data['producto']['precio']);
            $(".cantidad").html(data['stock'][1]);
            $("#stock").val(data['stock'][1]);
            $(".foto").html(data['foto']);
          }else{
            $(".codigo").html("");
            $("#producto_codigo").val("");
            $(".descripcion").html("");
            $(".precio").html("");
            $(".precio").val("");
            $(".cantidad").html("");
            $("#stock").val(null);
            $(".foto").html("<img src='"+"{{url('storage/productos').'/producto.png'}}"+"' style='width:100px;'>");
          }
        });
      }
    });


    $('#panelBuscar').on('hidden.bs.collapse', function () {
      $("#btnbuscar").html("<span class='fa fa-plus'></span>")
    });
    $('#panelBuscar').on('shown.bs.collapse', function () {
      $("#btnbuscar").html("<span class='fa fa-minus'></span>")
    });
    $('#panelAgregar').on('hidden.bs.collapse', function () {
      $("#btnAgregar").html("<span class='fa fa-plus'></span>")
    });
    $('#panelAgregar').on('shown.bs.collapse', function () {
      $("#btnAgregar").html("<span class='fa fa-minus'></span>")
    });
  });
</script>
