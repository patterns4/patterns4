# patterns4

build and start: 
    ```docker-compose up```

stop: 
    ```docker-compose down```  


# REST API Documentation

All of our data is open and free to read, but at this phase not open for third party editing.

The API in the docker container can be reached at ```localhost:1337/```, just go ahead and complete this string with the URIs documented below.

## Cities
A city has the following attributes:
> city_id
> 
> city_name
> 
> position

### GET all cities
```v1/city/```

Result:

    [
        {
            "city_id": 12,
            "city_name": "Stockholm Central",
            "position": "59.3289 18.0665"
        },
        {
            "city_id": 13,
            "city_name": "Göteborg",
            "position": "57.7088 11.9736"
        },
        ...
    ]

### GET a specific city
```v1/city/12```

Result:

    [
        {
            "city_id": 12,
            "city_name": "Stockholm Central",
            "position": "59.3289 18.0665"
        }
    ]

### GET all bikes
```v1/bike/```

### GET a specific city
```v1/bike/12```

```v1/station/```

```v1/station/1```

```v1/parking/```

```v1/parking/1```

```v1/log/```

```v1/log/1```
