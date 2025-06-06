How to run:

Clone repository.
``git clone git@github.com:Yarcrazy/test_fix.git``  
Up docker:
``docker compose up -d``  
Install dependencies:
``docker exec -it app composer install``  
Do migrations:
``docker exec -it app php yii migrate``  
Use:
    Auth:
    POST /api/auth/login: Authenticate a user and obtain a JWT token.
    {"username":"admin","password":"admin123"}
    
    Track:
    GET /api/track: List tracks (supports filtering by status).
    
    GET /api/track/{id}: View a specific track by ID.
    
    POST /api/track: Create a new track (requires JWT, put token to Authorization header like "Authorization: Bearer 123").
    {"track_number":"NEW123","status":"new"}
    
    PUT/PATCH /api/track/{id}: Update a track by ID (requires JWT).
    {"status":"shipped"}
    
    DELETE /api/track/{id}: Delete a track by ID (requires JWT).

