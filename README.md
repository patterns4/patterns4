# patterns4

## build and start ##
    ```docker-compose up```

## stop ##
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

## Bikes
A bike has the following attributes:
> bike_id
>
> position
> 
> speed
>
> battery
> 
> status
> 
> state
> 
> city_name

Status can be:
> parked
>
> free
> 
> moving
>
> depleted

### GET all bikes
```v1/bike/```

Result:

    [
        {
            "bike_id": 1,
            "position": "59.33317 18.0711",
            "speed": 0,
            "battery": 100,
            "status": 0,
            "state": "free",
            "city_name": "Stockholm Central"
        },
        {
            "bike_id": 2,
            "position": "59.32792 18.06265",
            "speed": 0,
            "battery": 8,
            "status": 0,
            "state": "free",
            "city_name": "Stockholm Central"
        },
        ...
    ]

### GET a specific bike
```v1/bike/12```

Result:

    [
        {
            "bike_id": 12,
            "position": "59.33157 18.06499",
            "speed": 0,
            "battery": 100,
            "status": 0,
            "state": "free",
            "city_name": "Stockholm Central"
        }
    ]

## Charging stations
A charging station has the following attributes:
> station_id
> 
> station_name
> 
> position
> 
> city_name

### GET all charging stations
```v1/station/```

Result:

    [
        {
            "station_id": 1,
            "station_name": "Bert Wenglers gata 3",
            "position": "59.3289 18.0665",
            "city_name": "Stockholm Central"
        },
        {
            "station_id": 2,
            "station_name": "Kungsgatan 21",
            "position": "59.4289 18.8665",
            "city_name": "Stockholm Central"
        },
        ...
    ]

### GET a specific charging station
```v1/station/1```

Result:

    [
        {
            "station_id": 1,
            "station_name": "Bert Wenglers gata 3",
            "position": "59.3289 18.0665",
            "city_name": "Stockholm Central"
        },
    ]

## Parking spots
A parking spot the following attributes:
> parking_id
> 
> parking_name
> 
> position
> 
> city_name

### GET all parking spots
```v1/parking/```

Result:

    [
        {
            "station_id": 1,
            "station_name": "Bert Wenglers gata 3",
            "position": "59.3289 18.0665",
            "city_name": "Stockholm Central"
        },
        {
            "station_id": 2,
            "station_name": "Kungsgatan 21",
            "position": "59.4289 18.8665",
            "city_name": "Stockholm Central"
        },
        ...
    ]

### GET a specific parking spot
```v1/parking/1```

Result:

    [
        {
            "station_id": 1,
            "station_name": "Bert Wenglers gata 3",
            "position": "59.3289 18.0665",
            "city_name": "Stockholm Central"
        },
    ]

## Logs
A log has the following attributes:
> log_id
>
> start_time
> 
> start_point
> 
> end_time
> 
> travel_time
> 
> end_point
> 
> user_id
> 
> bike_id
> 
> cost
> 
> paid

### GET all logs
```v1/log/```

Result:

    [
        {
            "log_id": 1,
            "start_time": "2022-01-13T12:18:20.000Z",
            "start_point": "59.32679 18.06712",
            "end_time": "2022-01-13T12:18:40.000Z",
            "travel_time": 20,
            "end_point": "59.32659 18.06607",
            "user_id": 111,
            "bike_id": 111,
            "cost": 11.6666,
            "paid": 0
        },
        {
            "log_id": 2,
            "start_time": "2022-01-13T12:17:36.000Z",
            "start_point": "59.3337 18.07337",
            "end_time": "2022-01-13T12:19:07.000Z",
            "travel_time": 90,
            "end_point": "59.33315 18.0676",
            "user_id": 25,
            "bike_id": 25,
            "cost": 17.4997,
            "paid": 0
        },
        ...
    ]

### GET a specific log
```v1/log/1```

Result:

    [
        {
            "log_id": 1,
            "start_time": "2022-01-13T12:18:20.000Z",
            "start_point": "59.32679 18.06712",
            "end_time": "2022-01-13T12:18:40.000Z",
            "travel_time": 20,
            "end_point": "59.32659 18.06607",
            "user_id": 111,
            "bike_id": 111,
            "cost": 11.6666,
            "paid": 0
        }
    ]
