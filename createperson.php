<!-- PHP Version, before going official project -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    
<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CREAR ALUMNO</title>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>

        <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="style.css">


</head>
<body>
<h1>2 - Crear Alumno</h1>
<p>Ahora se creará a un alumno, luego se le asignará una foto</p>
<hr>
<input type="text" id="nombre">
<button id="crear">Crear Alumno</button>
<hr>
<table class="text-center" id="example" class="display" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>personId</th>
                <th>Añadir foto</th>
                <th>Eliminar persona</th>
            </tr>
        </thead>
    </table>
    <hr>
    <div>
        <button class="btn btn-info" id="compare">Comparar rostro</button>
        <a class="btn btn-default" href="listgroups.html">Volver a Tabla Grupos</a>
    </div>
<script type="text/javascript">
            var groupname = location.search.split('group=')[1];
            var t = $('#example').DataTable({
                "language":{
             "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        }
            });

    $(document).ready(function(){
        getPersonas();
    });

    //ENVIAR A OTRA PAGINA DONDE SE COMPARE UN ROSTRO CON LOS DE ESTE GRUPO DE PERSONAS
    $("#compare").on("click",function(){
        window.location.assign("comparefaces.html?group="+groupname); 
    });

    //CREAR UNA NUEVA PERSONA DENTRO DEL GRUPO DE PERSONAS
$("#crear").on("click",function(){
        var nombre = $("#nombre").val();
        var str = '{"name":"'+nombre+'"}';
        var req = JSON.parse(str);
        var req2 = JSON.stringify(req);
        var personId = "";
              
        $.ajax({
            url: "https://westcentralus.api.cognitive.microsoft.com/face/v1.0/persongroups/"+groupname+"/persons",
            beforeSend: function(xhrObj){
                // Request headers
                xhrObj.setRequestHeader("Content-Type","application/json");
                xhrObj.setRequestHeader("Ocp-Apim-Subscription-Key","");
            },
            type: "POST",
            // Request body
            data: req2
        })
        .done(function(data) {
            personId=data.personId;
            t.row.add([nombre,personId,'<a class="btn btn-success" href="addfacetoperson.html?group='+groupname+'&person='+personId+'">Añadir foto</a>','<button class="btn btn-danger" id="'+personId+'" onclick="eliminarPersona(this.id)">Eliminar persona</button>']).draw(false);
            tryingphp(nombre);
        })
        .fail(function() {
            alert("error");
        });
    });

    function tryingphp(status){
        var tosendname = $("#nombre").val();
        var dataString = 'estado='+tosendname;
        $.ajax({
            url: "pdo.php",
            processData: true,
            type: "POST",
            async: false,
            data: dataString
        })
        .done(function(data){
            alert(data);
        })
        .fail(function(){
            alert("error al enviar a php");
        });
    }

    //OBTENER LISTADO DE PERSONAS DENTRO DE GRUPO DE PERSONAS
    function getPersonas(){
        var params = {
            // Request parameters
            "start": "1",
            "top": "1000",
        };
      
        $.ajax({
            url: "https://westcentralus.api.cognitive.microsoft.com/face/v1.0/persongroups/"+groupname+"/persons?" + $.param(params),
            beforeSend: function(xhrObj){
                // Request headers
                xhrObj.setRequestHeader("Ocp-Apim-Subscription-Key","");
            },
            type: "GET",
            async: false
        })
        .done(function(data) {
            data.forEach(function(element){
                console.log("FACE "+element.persistedFaceIds[0]);
                t.row.add([element.name,element.personId,'<a class="btn btn-success" href="addfacetoperson.html?group='+groupname+'&person='+element.personId+'">Añadir foto</a>','<button class="btn btn-danger" id="'+element.personId+'" onclick="eliminarPersona(this.id)">Eliminar persona</button>']).draw(false);
            });
            
        })
        .fail(function() {
            alert("error");
        });
    }

    function eliminarPersona(persona){
    var params = {
            // Request parameters
        };
      
        $.ajax({
            url: "https://westcentralus.api.cognitive.microsoft.com/face/v1.0/persongroups/"+groupname+"/persons/"+persona+"?" + $.param(params),
            beforeSend: function(xhrObj){
                // Request headers
                xhrObj.setRequestHeader("Ocp-Apim-Subscription-Key","");
            },
            type: "DELETE"
        })
        .done(function(data) {
                alert("SE ELIMINÓ CORRECTAMENTE A LA PERSONA "+persona);
                t.clear();
                getPersonas();
        })
        .fail(function() {
            alert("error");
        });
    }

    
</script>
</body>
</html>

