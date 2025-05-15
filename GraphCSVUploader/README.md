
# 📤 GraphCSVUploader

Este módulo permite autenticarse en Microsoft Graph API usando PowerShell y MSAL.PS (Device Code Flow), convertir datos JSON a CSV con PHP, y subir el archivo generado a OneDrive de forma automatizada.

> 🔐 Autenticación segura + 🔄 Conversión automática + ☁️ Upload a OneDrive

---

## 📦 Contenido

- `graph-token-and-upload.ps1`: Script PowerShell que:
  - Realiza autenticación mediante **MSAL.PS** (flujo Device Code)
  - Obtiene el token de acceso a Microsoft Graph
  - Llama a un script PHP que convierte un JSON a CSV
  - Sube el CSV a **OneDrive** usando Microsoft Graph API

- `jsonExportToOneDrive.php`: Script PHP que:
  - Convierte datos JSON (con estructuras anidadas) a CSV
  - Sube el archivo a una ruta específica en el OneDrive del usuario autenticado

---

## ⚙️ Requisitos

### PowerShell

- PowerShell 5.1 o superior
- Módulo [MSAL.PS](https://www.powershellgallery.com/packages/MSAL.PS)

```powershell
Install-Module -Name MSAL.PS -Scope CurrentUser -Force
````

### PHP

* PHP 7.4 o superior (CLI)
* Extensión `curl` habilitada

---

## 🚀 Cómo usar

### 1. Clona este repositorio o navega al directorio:

```bash
git clone https://github.com/CodeConnection101/GraphAuthKit.git
cd GraphAuthKit/GraphCSVUploader
```

### 2. Configura tus credenciales de Azure

Abre `graph-token-and-upload.ps1` y reemplaza:

```powershell
$tenantId = 'tu-tenandId'
$clientId = 'tu-clientId'
```

> Estos valores deben coincidir con tu app registrada en Azure AD.
> Asegúrate de haber concedido permisos delegados como:
>
> * `User.Read`
> * `Files.ReadWrite.All`

### 3. Ejecuta el script PowerShell

```powershell
.\graph-token-and-upload.ps1
```

Esto hará lo siguiente:

1. Solicita un token usando **Device Code Flow**
2. Llama a `jsonExportToOneDrive.php` pasando el token
3. El script PHP:

   * Convierte un JSON predefinido a CSV
   * Lo sube a tu OneDrive (`/Documentos/Ejemplos/` por defecto)

---

## 📂 Estructura de Archivos

```
GraphCSVUploader/
├── graph-token-and-upload.ps1      # Script de autenticación + ejecución
├── jsonExportToOneDrive.php        # Conversión JSON → CSV y subida a OneDrive
```

---

## ✨ Personalización

* Puedes reemplazar el JSON de ejemplo en el script PHP por datos obtenidos desde Microsoft Graph o cualquier fuente externa.
* Cambia la ruta en OneDrive modificando la variable `$onedrivePath` en el script PHP.

---

## 🔒 Seguridad

* El token de acceso **no se debe imprimir ni registrar en producción**.
* El script PHP lo recibe como argumento para evitar hardcoding.
* Considera usar un sistema de gestión de secretos o entorno seguro en producción.

---

## 📘 Recursos útiles

* [Microsoft Graph API](https://learn.microsoft.com/es-es/graph/)
* [MSAL.PS Documentation](https://github.com/AzureAD/MSAL.PS)
* [Register an app in Azure](https://learn.microsoft.com/en-us/azure/active-directory/develop/quickstart-register-app)

---

## 🧠 Autor

Desarrollado por [CodeConnection101](https://github.com/CodeConnection101) — Integrando automatización, interoperabilidad y buenas prácticas en la nube.


