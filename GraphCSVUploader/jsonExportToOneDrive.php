<?php

// Convierte un JSON a un archivo CSV y lo guarda localmente.
function jsonToCsv($json, $filename = 'List-UPDATED.csv') {
    // Decodifica el JSON a un arreglo asociativo de PHP.
    $data = json_decode($json, true);

    // Abre (o crea) un archivo CSV para escritura.
    $csv = fopen($filename, 'w');

    // Si no se pudo abrir el archivo o el JSON está mal formado, lanza una excepción.
    if (!$csv || !$data) {
        throw new Exception("Error creando archivo CSV o decodificando JSON.");
    }

    // Array que contendrá los elementos "aplanados".
    $flattened = [];

    // Itera sobre cada ítem del JSON para aplanar estructuras anidadas.
    foreach ($data as $item) {
        $flattened[] = flatten($item);
    }

    // Obtiene los encabezados del CSV desde las claves del primer elemento aplanado.
    $headers = array_keys(reset($flattened));
    fputcsv($csv, $headers); // Escribe la cabecera al archivo CSV.

    // Escribe cada fila de datos en el archivo CSV.
    foreach ($flattened as $row) {
        fputcsv($csv, $row);
    }

    // Cierra el archivo CSV.
    fclose($csv);

    // Retorna el nombre del archivo generado.
    return $filename;
}

// Función recursiva que aplana arreglos anidados con claves jerárquicas separadas por puntos.
function flatten(array $array, $prefix = '') {
    $result = [];

    foreach ($array as $key => $value) {
        // Crea una clave compuesta si es una estructura anidada (por ejemplo: direccion.ciudad)
        $fullKey = $prefix === '' ? $key : $prefix . '.' . $key;

        // Si el valor es otro arreglo, llama recursivamente a flatten.
        if (is_array($value)) {
            $result += flatten($value, $fullKey);
        } else {
            // Si es un valor escalar (no arreglo), lo agrega al resultado.
            $result[$fullKey] = $value;
        }
    }

    return $result;
}

// Sube un archivo CSV a OneDrive usando Microsoft Graph API.
function uploadToOneDrive($filename, $accessToken, $onedrivePath = '/Documentos/Ejemplos/') {
    // Lee el contenido del archivo local.
    $fileContents = file_get_contents($filename);

    // Construye la URL de carga en OneDrive.
    $uploadUrl = "https://graph.microsoft.com/v1.0/me/drive/root:" . $onedrivePath . $filename . ':/content';

    // Inicializa cURL para hacer la petición HTTP.
    $ch = curl_init($uploadUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retorna la respuesta como string.
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); // Método PUT para subir contenido.
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fileContents); // Adjunta el contenido del archivo.
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $accessToken", // Autenticación con token Bearer.
        "Content-Type: text/csv" // Tipo de contenido enviado.
    ]);

    // Ejecuta la petición y obtiene la respuesta.
    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Obtiene el código de estado HTTP.
    curl_close($ch); // Cierra la sesión cURL.

    // Verifica si la subida fue exitosa (códigos 2xx).

    if ($statusCode >= 200 && $statusCode < 300) {
        echo "✅ Archivo subido exitosamente a OneDrive.\n";
    } else {
        // Muestra mensaje de error con el código y la respuesta.
        echo "❌ Error subiendo archivo ($statusCode): $response\n";
    }
}

// -------------------- Ejemplo de uso --------------------

// JSON de entrada como string. Contiene datos anidados en la clave "direccion".
$json = '[
    {
        "nombre": "Juan",
        "edad": 30,
        "direccion": {
            "ciudad": "San José",
            "pais": "Costa Rica"
        }
    },
    {
        "nombre": "Ana",
        "edad": 25,
        "direccion": {
            "ciudad": "Heredia",
            "pais": "Costa Rica"
        }
    }
]';

// Obtiene el token de acceso desde argumentos de línea de comandos.
// ✅ BUENA PRÁCTICA: evitar hardcodear tokens. Usar variables de entorno o argumentos.
$accessToken = $argv[1] ?? null;

// Si no se proporcionó el token, termina la ejecución con un mensaje de error.
if (!$accessToken) {
    die("❌ No se proporcionó token como argumento.\n");
}

// Convierte el JSON a CSV.
$csvFile = jsonToCsv($json);

// Sube el archivo CSV a OneDrive usando el token proporcionado.
uploadToOneDrive($csvFile, $accessToken);

?>
<!--
    Este script PHP convierte un JSON a CSV y lo sube a OneDrive usando Microsoft Graph API.
    Requiere un token de acceso válido para la autenticación.
    Uso: php script.php <access_token>