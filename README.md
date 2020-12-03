# AIVO Re-Challenge

**Disclaimer**: El actual proyecto se entrega sin conocimiento del setup del servidor donde se lo probará.
Por lo tanto, se recomienda seguir los pasos a continuación detallados.

## Requisitos
- PHP >= v7.2.5 (Se puede utilizar [XAMMP](https://www.apachefriends.org/es/index.html))
- [Composer](https://getcomposer.org/)

## Instalación
Pasos a seguir para instalar este repositorio

1. Clonar el actual repositorio.
    ```
    git clone https://github.com/SirPaulO/aivochallenge.git
    ```
2. Establecer variables de entorno con las credenciales de Spotify, para lo que
se provee un archivo .env.example
    ```
    cp .env.example .env
    ```
3. Instalar dependencias mediante Composer.
    ```
    cd aivochallenge
    composer install
    ```
4. Ejecutar servidor de PHP dentro de la carpeta del proyecto.
    ```
    cd aivochallenge
    php -S localhost:8000 -t public
    ```
5. Dirigirse a la siguiente URL, dónde ***<band-name>*** es el nombre de la banda a buscar.
    ```
    http://localhost:8000/api/v1/albums?q=<band-name>
    ```